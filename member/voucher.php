<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Token Authentication Guard
$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized session. Please log in again.']);
        exit();
    }
    header("Location: /index.php");
    exit();
}

// 2. Validate Session User & Get Active Wallet
$stmt = $conn->prepare("
    SELECT u.uid, u.token, u.is_active, w.balance 
    FROM users u 
    LEFT JOIN wallets w ON u.uid = w.uid 
    WHERE u.uid = ? AND u.token = ? AND u.is_active = TRUE
");
$stmt->bind_param("ss", $suid, $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_unset();
    session_destroy();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid session token.']);
        exit();
    }
    header("Location: /index.php");
    exit();
}

function getClientIP() {
    $ipkeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    foreach ($ipkeys as $key) {
        if (!empty($_SERVER[$key])) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

function getUserCountryCode() {
    if (!empty($_SESSION['user_country_code'])) {
        return $_SESSION['user_country_code'];
    }

    if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
        $_SESSION['user_country_code'] = strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']);
        return $_SESSION['user_country_code'];
    }

    $ip = getClientIP();
    if (empty($ip)) {
        $_SESSION['user_country_code'] = 'NG';
        return 'NG';
    }

    $geoUrl = "http://ip-api.com/json/" . urlencode($ip) . "?fields=countryCode";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $geoUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 2,
        CURLOPT_CONNECTTIMEOUT => 2
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $json = json_decode($response, true);
        if (!empty($json['countryCode'])) {
            $_SESSION['user_country_code'] = strtoupper($json['countryCode']);
            return $_SESSION['user_country_code'];
        }
    }

    $_SESSION['user_country_code'] = 'NG';
    return 'NG';
}

// 3. Fetch Currency & Rate from Database based on User Country
$userCountryCode = getUserCountryCode();

$rateStmt = $conn->prepare("SELECT country, currency_code, amount FROM monieflow_coin_values WHERE country_code = ? LIMIT 1");
$rateStmt->bind_param("s", $userCountryCode);
$rateStmt->execute();
$currencyData = $rateStmt->get_result()->fetch_assoc();

// Fallback to NGN if user country is not in database
if (!$currencyData) {
    $defaultCode = 'NG';
    $rateStmt = $conn->prepare("SELECT country, currency_code, amount FROM monieflow_coin_values WHERE country_code = ? LIMIT 1");
    $rateStmt->bind_param("s", $defaultCode);
    $rateStmt->execute();
    $currencyData = $rateStmt->get_result()->fetch_assoc();
}

$userCountryName = $currencyData['country'] ?? 'Nigeria';
$userCurrencyCode = $currencyData['currency_code'] ?? 'NGN';
$userRate = (float)($currencyData['amount'] ?? 1.00);

// Helper for currency symbols
$currencySymbols = [
    'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'NGN' => '₦', 'GHS' => 'GH₵',
    'KES' => 'KSh', 'ZAR' => 'R', 'INR' => '₹', 'CAD' => 'CA$', 'AUD' => 'A$',
    'AED' => 'AED ', 'CNY' => '¥', 'JPY' => '¥', 'CHF' => 'CHF ', 'BRL' => 'R$',
    'EGP' => 'E£', 'RWF' => 'FRw ', 'UGX' => 'USh ', 'TZS' => 'TSh ', 'XAF' => 'FCFA '
];
$userCurrencySymbol = $currencySymbols[$userCurrencyCode] ?? $userCurrencyCode . ' ';

// 4. Helper Function: 10-Digit Alphanumeric Code Generator
function generateUniquePinCode($conn) {
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    do {
        $code = '';
        for ($i = 0; $i < 10; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        $checkStmt = $conn->prepare("SELECT id FROM deposits WHERE pin_code = ? LIMIT 1");
        $checkStmt->bind_param("s", $code);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->num_rows > 0;
    } while ($exists);

    return $code;
}

// 5. Process Fetch Request to Generate Code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_code') {
    header('Content-Type: application/json');

    $amount = filter_var($_POST['amount'] ?? 0, FILTER_VALIDATE_FLOAT);

    if (!$amount || $amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid MF amount.']);
        exit();
    }

    $conn->begin_transaction();

    try {
        $walletStmt = $conn->prepare("SELECT balance FROM wallets WHERE uid = ? FOR UPDATE");
        $walletStmt->bind_param("s", $user['uid']);
        $walletStmt->execute();
        $wallet = $walletStmt->get_result()->fetch_assoc();

        $currentBalance = (float)($wallet['balance'] ?? 0);

        if ($currentBalance < $amount) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Insufficient MF wallet balance to generate this code.']);
            exit();
        }

        $pinCode = generateUniquePinCode($conn);

        $deductStmt = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE uid = ?");
        $deductStmt->bind_param("ds", $amount, $user['uid']);
        $deductStmt->execute();

        $depositStmt = $conn->prepare("INSERT INTO deposits (uid, pin_code, amount, status) VALUES (?, ?, ?, 'pending')");
        $depositStmt->bind_param("ssd", $user['uid'], $pinCode, $amount);
        $depositStmt->execute();
        $depositId = $conn->insert_id;

        $description = "Generated Deposit Code (" . $pinCode . ")";
        $payloads = json_encode(['deposit_id' => $depositId, 'pin_code' => $pinCode, 'currency' => 'MF']);

        $txStmt = $conn->prepare("INSERT INTO transaction (uid, amount, type, description, payloads) VALUES (?, ?, 'debit', ?, ?)");
        $txStmt->bind_param("sdss", $user['uid'], $amount, $description, $payloads);
        $txStmt->execute();

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Deposit code generated successfully!',
            'pin_code' => $pinCode,
            'amount' => number_format($amount, 2),
            'new_balance' => number_format($currentBalance - $amount, 2)
        ]);
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to generate code due to a database error.']);
        exit();
    }
}

// 6. Fetch User's Active Generated Codes
$historyStmt = $conn->prepare("SELECT pin_code, amount, status, created_at FROM deposits WHERE uid = ? ORDER BY id DESC LIMIT 10");
$historyStmt->bind_param("s", $user['uid']);
$historyStmt->execute();
$generatedCodes = $historyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Generate Deposit Code</title>
    
    <link rel="icon" type="image/png" href="/logo.png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-whitesmoke: #f4f9fc;
            --brand-skyblue: #00a8e8;
            --brand-skyblue-hover: #008cc3;
            --card-white: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-whitesmoke);
            color: var(--text-dark);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        @media (min-width: 992px) {
            body { padding-bottom: 20px; }
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            background: #008cc3;
            border-radius: 50%;
            padding: 4px;
        }

        .action-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
        }

        .btn-skyblue {
            background-color: var(--brand-skyblue);
            color: #ffffff;
            font-weight: 600;
        }

        .btn-skyblue:hover {
            background-color: var(--brand-skyblue-hover);
            color: #ffffff;
        }

        .code-badge {
            letter-spacing: 2px;
            font-family: monospace;
            font-size: 1rem;
        }

        .mf-symbol {
            font-weight: 700;
            color: var(--brand-skyblue-hover);
        }

        .currency-badge {
            font-size: 0.75rem;
            background-color: rgba(0, 168, 232, 0.1);
            color: var(--brand-skyblue-hover);
            padding: 4px 8px;
            border-radius: 6px;
        }
    </style>
</head>
<body 
    data-exchange-rate="<?= $userRate ?>" 
    data-currency-code="<?= htmlspecialchars($userCurrencyCode) ?>" 
    data-currency-symbol="<?= htmlspecialchars($userCurrencySymbol) ?>"
    data-country-name="<?= htmlspecialchars($userCountryName) ?>">

    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/member/index.php">
                <img src="/logo.png" alt="MonieFlow">
                <span style="color: var(--brand-skyblue-hover);">MonieFlow</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <a href="/member/index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-4 px-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                
                <div class="action-card p-4 mb-4 text-center">
                    <span class="text-muted small fw-semibold">AVAILABLE BALANCE</span>
                    <h2 class="fw-bold text-dark mt-1 mb-0"><span id="userBalance"><?= number_format($user['balance'] ?? 0, 2) ?></span> <span class="fs-4 mf-symbol">MF</span></h2>
                    <div id="localBalancePreview" class="text-muted small mt-1"></div>
                </div>

                <div class="action-card p-4 p-sm-5 mb-4">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-qr-code fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Generate Deposit Code</h4>
                        <p class="text-muted small mb-2">Convert your MF balance into a shareable 10-character deposit code.</p>
                        
                        <div id="locationBadge" class="d-inline-block">
                            <span class="currency-badge fw-semibold">
                                <i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($userCountryName) ?> (<?= htmlspecialchars($userCurrencyCode) ?>)
                            </span>
                        </div>
                    </div>

                    <div id="alertContainer"></div>

                    <form id="generateForm">
                        <input type="hidden" name="action" value="generate_code">
                        <input type="hidden" id="mfAmountInput" name="amount" value="0">
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="localAmount" class="form-label fw-semibold small mb-0">Amount (<span class="userCurrencyCode"><?= htmlspecialchars($userCurrencyCode) ?></span>)</label>
                                <span class="text-muted extra-small" style="font-size: 0.8rem;">1 MF = <span id="rateDisplay"><?= number_format($userRate, 2) ?></span> <span class="userCurrencyCode"><?= htmlspecialchars($userCurrencyCode) ?></span></span>
                            </div>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white fw-bold userCurrencySymbol"><?= htmlspecialchars($userCurrencySymbol) ?></span>
                                <input type="number" step="any" class="form-control fw-bold" id="localAmount" placeholder="0.00">
                            </div>
                        </div>

                        <div class="card bg-light border-0 p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-semibold">YOU SEND (MF):</span>
                                <span class="fs-5 fw-bold text-primary"><span id="mfCalculation">0.00</span> MF</span>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-skyblue btn-lg w-100 py-3 rounded-3 d-flex align-items-center justify-content-center">
                            <span id="btnSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                            <span id="btnText"><i class="bi bi-plus-circle me-2"></i> Generate Code</span>
                        </button>
                    </form>
                </div>

                <div class="action-card p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Your Recent Codes</h6>
                    
                    <div class="list-group list-group-flush" id="codesList">
                        <?php if (empty($generatedCodes)): ?>
                            <p class="text-muted small text-center my-3" id="noCodesMsg">No generated codes yet.</p>
                        <?php else: ?>
                            <?php foreach ($generatedCodes as $item): ?>
                                <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-3">
                                    <div>
                                        <span class="badge bg-light text-dark border code-badge me-2"><?= htmlspecialchars($item['pin_code']) ?></span>
                                        <small class="text-muted d-block mt-1"><?= date('M d, Y h:i A', strtotime($item['created_at'])) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-dark d-block"><?= number_format($item['amount'], 2) ?> MF</span>
                                        <button class="btn btn-sm btn-link p-0 text-decoration-none text-primary copy-btn" data-code="<?= htmlspecialchars($item['pin_code']) ?>">
                                            <i class="bi bi-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php $page='voucher'; include __DIR__."/nav-xs.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const bodyDataset = document.body.dataset;
        const exchangeRate = parseFloat(bodyDataset.exchangeRate) || 1.0; 
        const userCurrency = bodyDataset.currencyCode || 'NGN';
        const userSymbol = bodyDataset.currencySymbol || '₦';

        // Initialize Localized Balance View
        function initLocalBalance() {
            const rawBalance = parseFloat(document.getElementById('userBalance').textContent.replace(/,/g, '')) || 0;
            const localBalance = rawBalance * exchangeRate;
            document.getElementById('localBalancePreview').textContent = `≈ ${userSymbol}${localBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${userCurrency}`;
        }

        initLocalBalance();

        // Calculate MF on local input change
        document.getElementById('localAmount').addEventListener('input', function() {
            const localVal = parseFloat(this.value) || 0;
            const mfVal = localVal / exchangeRate;
            
            document.getElementById('mfCalculation').textContent = mfVal.toFixed(2);
            document.getElementById('mfAmountInput').value = mfVal.toFixed(4);
        });

        // Handle Form Submission
        document.getElementById('generateForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const btnSpinner = document.getElementById('btnSpinner');
            const alertContainer = document.getElementById('alertContainer');
            const mfVal = parseFloat(document.getElementById('mfAmountInput').value) || 0;

            if (mfVal <= 0) {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Please enter a valid amount.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                return;
            }

            alertContainer.innerHTML = '';
            submitBtn.disabled = true;
            btnSpinner.classList.remove('d-none');

            try {
                const formData = new FormData(form);
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Update Balance UI
                    const cleanNewBal = parseFloat(data.new_balance.replace(/,/g, ''));
                    document.getElementById('userBalance').textContent = data.new_balance;
                    
                    const localBalance = cleanNewBal * exchangeRate;
                    document.getElementById('localBalancePreview').textContent = `≈ ${userSymbol}${localBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${userCurrency}`;

                    alertContainer.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> ${data.message}<br>
                            <strong>Code:</strong> <span class="code-badge bg-white px-2 py-1 rounded text-dark">${data.pin_code}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;

                    const noCodesMsg = document.getElementById('noCodesMsg');
                    if (noCodesMsg) noCodesMsg.remove();

                    const newRow = document.createElement('div');
                    newRow.className = 'list-group-item d-flex align-items-center justify-content-between px-0 py-3';
                    newRow.innerHTML = `
                        <div>
                            <span class="badge bg-light text-dark border code-badge me-2">${data.pin_code}</span>
                            <small class="text-muted d-block mt-1">Just now</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark d-block">${data.amount} MF</span>
                            <button class="btn btn-sm btn-link p-0 text-decoration-none text-primary copy-btn" data-code="${data.pin_code}">
                                <i class="bi bi-copy"></i> Copy
                            </button>
                        </div>
                    `;
                    document.getElementById('codesList').prepend(newRow);

                    form.reset();
                    document.getElementById('mfCalculation').textContent = '0.00';
                    document.getElementById('mfAmountInput').value = '0';
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                }
            } catch (error) {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        An unexpected network error occurred. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            } finally {
                submitBtn.disabled = false;
                btnSpinner.classList.add('d-none');
            }
        });

        // Copy Code Event Delegation
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.copy-btn');
            if (btn) {
                const code = btn.getAttribute('data-code');
                navigator.clipboard.writeText(code).then(() => {
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
                    setTimeout(() => btn.innerHTML = originalHTML, 2000);
                });
            }
        });
    </script>
</body>
</html>