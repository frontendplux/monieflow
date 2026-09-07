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

// 2. Validate Session User
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
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

// 3. Handle Fetch API Deposit Redemption Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_deposit'])) {
    header('Content-Type: application/json');

    $inputPin = trim($_POST['pin_code'] ?? '');

    if (empty($inputPin)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in the deposit code.']);
        exit();
    }

    // Begin Database Transaction
    $conn->begin_transaction();

    try {
        // FOR UPDATE acquires a row-level write lock to prevent simultaneous double-redemptions by multiple users
        $depositStmt = $conn->prepare("SELECT * FROM deposits WHERE pin_code = ? AND status = 'pending' LIMIT 1 FOR UPDATE");
        $depositStmt->bind_param("s", $inputPin);
        $depositStmt->execute();
        $deposit = $depositStmt->get_result()->fetch_assoc();

        if (!$deposit) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Invalid or already redeemed deposit code.']);
            exit();
        }

        $depositAmount = $deposit['amount'];
        $depositId = $deposit['id'];
        $creatorUid = $deposit['uid'];

        // Step A: Update wallet balance for the CURRENT authenticated user ($user['uid'])
        $updateWalletStmt = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE uid = ?");
        $updateWalletStmt->bind_param("ds", $depositAmount, $user['uid']);
        $updateWalletStmt->execute();

        // Step B: Record transaction entry for the redeemer
        $description = "Wallet Deposit via Code (" . htmlspecialchars($inputPin) . ")";
        $payloads = json_encode([
            'deposit_id' => $depositId,
            'pin_code' => $inputPin,
            'created_by' => $creatorUid
        ]);

        $txStmt = $conn->prepare("INSERT INTO transaction (uid, amount, type, description, payloads) VALUES (?, ?, 'credit', ?, ?)");
        $txStmt->bind_param("sdss", $user['uid'], $depositAmount, $description, $payloads);
        $txStmt->execute();

        // Step C: Delete the redeemed code from deposits table so it can never be queried again
        $deleteStmt = $conn->prepare("DELETE FROM deposits WHERE id = ?");
        $deleteStmt->bind_param("i", $depositId);
        $deleteStmt->execute();

        // Commit Transaction (Releases lock on the row)
        $conn->commit();

        $_SESSION['flash_message'] = 'Deposit successful! ₦' . number_format($depositAmount, 2) . ' has been added to your wallet.';
        $_SESSION['flash_type'] = 'success';

        echo json_encode([
            'status' => 'success', 
            'message' => 'Deposit successful!', 
            'redirect' => '/member/index.php'
        ]);
        exit();

    } catch (Exception $e) {
        // Rollback in case of any execution or database error
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Transaction failed due to a server error. Please try again.']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Deposit Funds</title>
    
    <link rel="icon" type="image/png" href="/logo.png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
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

        .deposit-card {
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
    </style>
</head>
<body>

    <!-- Header / Navbar -->
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

    <!-- Main Content Container -->
    <main class="container py-4 px-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                
                <div class="deposit-card p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-arrow-down-circle fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Redeem Deposit Code</h4>
                        <p class="text-muted small">Enter your deposit code below to credit your wallet instantly.</p>
                    </div>

                    <div id="alertContainer"></div>

                    <form id="depositForm">
                        <input type="hidden" name="submit_deposit" value="1">
                        
                        <div class="mb-4">
                            <label for="pin_code" class="form-label fw-semibold small">Deposit Code</label>
                            <input type="text" class="form-control form-control-lg text-center fw-bold text-uppercase" id="pin_code" name="pin_code" maxlength="10" placeholder="e.g. X8A92B" required style="letter-spacing: 4px;" autocomplete="off">
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-skyblue btn-lg w-100 py-3 rounded-3 d-flex align-items-center justify-content-center">
                            <span id="btnSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                            <span id="btnText"><i class="bi bi-wallet2 me-2"></i> Redeem Code</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <?php $page='deposit'; include __DIR__."/nav-xs.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('depositForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const btnSpinner = document.getElementById('btnSpinner');
            const btnText = document.getElementById('btnText');
            const alertContainer = document.getElementById('alertContainer');

            alertContainer.innerHTML = '';
            submitBtn.disabled = true;
            btnSpinner.classList.remove('d-none');

            try {
                const formData = new FormData(form);
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success fade show" role="alert">
                            ${data.message} Redirecting...
                        </div>
                    `;
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    submitBtn.disabled = false;
                    btnSpinner.classList.add('d-none');
                }
            } catch (error) {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        An unexpected network error occurred. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                submitBtn.disabled = false;
                btnSpinner.classList.add('d-none');
            }
        });
    </script>
</body>
</html>