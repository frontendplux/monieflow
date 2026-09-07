<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Authentication Guard
$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
    header("Location: /index.php");
    exit();
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
$stmt->bind_param("ss", $suid, $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: /index.php");
    exit();
}

// -----------------------------------------------------------------------------
// IP Geolocation & Country Currency Lookup
// -----------------------------------------------------------------------------
function getUserCountryCode() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    // Skip local/loopback IPs
    if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0) {
        return 'NG';
    }
    
    $geoUrl = "http://ip-api.com/json/" . $ip . "?fields=countryCode";
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $geoData = @file_get_contents($geoUrl, false, $ctx);
    
    if ($geoData) {
        $json = json_decode($geoData, true);
        if (isset($json['countryCode'])) {
            return strtoupper($json['countryCode']);
        }
    }
    return 'NG'; // Default fallback country code
}

$userCountryCode = getUserCountryCode();

// Fetch Exchange Rate based on detected Country Code
$rateStmt = $conn->prepare("SELECT * FROM monieflow_coin_values WHERE country_code = ? LIMIT 1");
$rateStmt->bind_param("s", $userCountryCode);
$rateStmt->execute();
$rateData = $rateStmt->get_result()->fetch_assoc();

// Fallback to NG (Naira) if specific country isn't supported in database
if (!$rateData) {
    $fallbackStmt = $conn->query("SELECT * FROM monieflow_coin_values WHERE country_code = 'NG' LIMIT 1");
    $rateData = $fallbackStmt->fetch_assoc();
}

$currencyCode = $rateData['currency_code'] ?? 'NGN';
$mfExchangeRate = floatval($rateData['amount'] ?? 1.00);

// -----------------------------------------------------------------------------
// Helper Functions to Generate Guaranteed Unique Keys
// -----------------------------------------------------------------------------
function generateUniquePublicId($conn) {
    do {
        $publicId = 'pub_' . bin2hex(random_bytes(8));
        $stmt = $conn->prepare("SELECT id FROM wallets WHERE public_id = ?");
        $stmt->bind_param("s", $publicId);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
    } while ($exists);
    return $publicId;
}

function generateUniqueAccountNumber($conn) {
    do {
        $accNo = '30' . sprintf('%08d', rand(0, 99999999));
        $stmt = $conn->prepare("SELECT id FROM wallets WHERE account_number = ?");
        $stmt->bind_param("s", $accNo);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
    } while ($exists);
    return $accNo;
}

function createOrGetWallet($conn, $uid) {
    $stmt = $conn->prepare("SELECT * FROM wallets WHERE uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $wallet = $stmt->get_result()->fetch_assoc();

    if (!$wallet) {
        $publicId = generateUniquePublicId($conn);
        $privateKey = 'prv_' . bin2hex(random_bytes(16));
        $accNumber = generateUniqueAccountNumber($conn);
        $initialBalance = 0.00;

        $insertStmt = $conn->prepare("INSERT INTO wallets (uid, public_id, private_key, account_number, balance) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssd", $uid, $publicId, $privateKey, $accNumber, $initialBalance);
        $insertStmt->execute();

        return [
            'public_id' => $publicId,
            'private_key' => $privateKey,
            'account_number' => $accNumber,
            'balance' => $initialBalance
        ];
    }

    return $wallet;
}

// Generate or fetch user wallet
$wallet = createOrGetWallet($conn, $user['uid']);

// Balance in MF and Fiat calculation
$balanceMF = floatval($wallet['balance']);
$fiatEquivalent = $balanceMF * $mfExchangeRate;

// Handle AJAX Request: Regenerate Public ID & Keys
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'regenerate_keys') {
    header('Content-Type: application/json');
    $newPublicId = generateUniquePublicId($conn);
    $newPrivateKey = 'prv_' . bin2hex(random_bytes(16));

    $updateStmt = $conn->prepare("UPDATE wallets SET public_id = ?, private_key = ? WHERE uid = ?");
    $updateStmt->bind_param("sss", $newPublicId, $newPrivateKey, $user['uid']);
    
    if ($updateStmt->execute()) {
        echo json_encode(['status' => true, 'public_id' => $newPublicId, 'message' => 'New credentials generated!']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to generate credentials.']);
    }
    exit();
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: /index.php");
    exit();
}

// Fetch user recent activity
$txStmt = $conn->prepare("SELECT * FROM transaction WHERE uid = ? ORDER BY created_at DESC LIMIT 2");
$txStmt->bind_param("s", $user['uid']);
$txStmt->execute();
$transactions = $txStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Member Dashboard</title>
    
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
            width: 40px; height: 40px;
            background: #008cc3;
            border-radius: 50%;
            padding: 4px;
        }

        .balance-card {
            background: linear-gradient(135deg, #00a8e8 0%, #00719e 100%);
            color: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 168, 232, 0.25);
        }

        .action-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            color: var(--text-dark);
            cursor: pointer;
            height: 100%;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 168, 232, 0.12);
            color: var(--brand-skyblue);
        }

        .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 0.75rem;
            background-color: rgba(0, 168, 232, 0.1);
            color: var(--brand-skyblue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin: 0 auto;
        }

        .activity-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.12);
            border-radius: 1rem;
            padding: 1rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .activity-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }

        .activity-icon.credit { background-color: #d1fae5; color: #10b981; }
        .activity-icon.debit { background-color: #fee2e2; color: #ef4444; }

        .btn-skyblue {
            background-color: var(--brand-skyblue);
            color: #ffffff; font-weight: 600;
        }

        .btn-skyblue:hover {
            background-color: var(--brand-skyblue-hover);
            color: #ffffff;
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #ffffff;
            border-top: 1px solid rgba(0, 168, 232, 0.15);
            z-index: 1030;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }

        .mobile-bottom-nav .nav-link {
            color: var(--text-muted);
            font-size: 0.72rem;
            padding: 8px 0;
            text-align: center;
        }

        .mobile-bottom-nav .nav-link.active,
        .mobile-bottom-nav .nav-link:hover {
            color: var(--brand-skyblue);
        }

        .mobile-bottom-nav i {
            font-size: 1.25rem;
            display: block;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="#">
                <img src="/logo.png" alt="MonieFlow">
                <span style="color: var(--brand-skyblue-hover);">MonieFlow</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Logged in as</small>
                    <span class="fw-semibold small"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <a href="?action=logout" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container py-4 px-3">

        <!-- Welcome Banner & Public ID Info -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Welcome back, <?= htmlspecialchars($user['username']) ?>!</h1>
                <p class="text-muted mb-0 small">
                    Public-ID: 
                    <span class="badge bg-light text-dark border fw-mono" id="publicIdDisplay">
                        <?= htmlspecialchars($wallet['public_id']) ?>
                    </span>
                    <br class="d-sm-none">
                    <span class="mt-1 d-inline-block">
                        <a href="#" onclick="regenerateKeys(event)" class="text-decoration-none brand-logo me-2"><i class="bi bi-arrow-repeat"></i> Generate New</a> | 
                        <a href="#" onclick="copyPublicId(event)" class="text-decoration-none brand-logo ms-2"><i class="bi bi-copy"></i> Copy Public ID</a>
                    </span>
                </p>
            </div>
            <div>
                <a href="/member/transfer.php" class="btn btn-skyblue rounded-3 px-4 py-2 w-100 w-md-auto">
                    <i class="bi bi-plus-lg me-1"></i> New Transfer
                </a>
            </div>
        </div>

        <!-- Account Balance Card -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-4">
                <div class="balance-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small opacity-75">Available Balance</span>
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                        <!-- Main Balance in MF -->
                        <h2 class="display-6 fw-bold mb-1"><?= number_format($balanceMF, 2) ?> <span class="fs-5">MF</span></h2>
                        <!-- Local Fiat Equivalent based on IP -->
                        <small class="opacity-75 d-block mb-3">
                            ≈ <?= htmlspecialchars($currencyCode) ?> <?= number_format($fiatEquivalent, 2) ?> 
                            <span class="badge bg-white text-dark ms-1" style="font-size: 0.65rem; opacity: 0.9;"><?= $userCountryCode ?></span>
                        </small>
                    </div>
                    <div class="d-flex justify-content-between small opacity-75 pt-2 border-top border-white-50">
                        <span>Acc No: <?= htmlspecialchars($wallet['account_number']) ?></span>
                        <span>Active</span>
                    </div>
                </div>
            </div>

            <!-- Action Grid (With Referral, Chart, Task, Bidding Added) -->
            <div class="col-12 col-lg-8">
                <div class="row g-2 g-sm-3">
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/deposit.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-arrow-down-circle"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Deposit</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/transfer.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-arrow-up-right-circle"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Transfer</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/peer2peer.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-people"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Peer2Peer</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/chart.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-graph-up text-info"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Chart</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/referral.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-person-plus text-primary"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Referral</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/tasks.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-check2-square text-success"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Tasks</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/bidding.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-hammer text-warning"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Bidding</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/history.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-clock-history"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">History</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/settings.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-gear"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Settings</span>
                        </a>
                    </div>
                    <div class="col-3 col-sm-4 col-md-3">
                        <a href="/member/support.php" class="action-card p-2 p-sm-3 text-center d-block">
                            <div class="icon-box mb-2"><i class="bi bi-headset"></i></div>
                            <span class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">Support</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Recent Activity</h5>
            <a href="/member/history.php" class="text-decoration-none small brand-logo" style="color: var(--brand-skyblue-hover);">View All</a>
        </div>

        <div class="d-flex flex-column gap-3">
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $tx): ?>
                    <div class="activity-card d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="activity-icon <?= $tx['type'] === 'credit' ? 'credit' : 'debit' ?>">
                                <i class="bi bi-arrow-<?= $tx['type'] === 'credit' ? 'down-left' : 'up-right' ?>"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0"><?= htmlspecialchars($tx['description'] ?? 'Transaction') ?></h6>
                                <small class="text-muted d-block"><?= ucfirst($tx['type']) ?> • <?= date('M d, Y', strtotime($tx['created_at'])) ?></small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold <?= $tx['type'] === 'credit' ? 'text-success' : 'text-danger' ?> d-block">
                                <?= $tx['type'] === 'credit' ? '+' : '-' ?><?= number_format($tx['amount'], 2) ?> MF
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Completed</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-card text-center py-4">
                    <i class="bi bi-receipt fs-2 text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-0 small">No transactions found yet.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Mobile Navigation Bar -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col">
                    <a href="/member/index.php" class="nav-link active">
                        <i class="bi bi-house-door"></i>
                        <span>Home</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/chart.php" class="nav-link">
                        <i class="bi bi-graph-up"></i>
                        <span>Chart</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/tasks.php" class="nav-link">
                        <i class="bi bi-check2-square"></i>
                        <span>Tasks</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/peer2peer.php" class="nav-link">
                        <i class="bi bi-people"></i>
                        <span>P2P</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/settings.php" class="nav-link">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function copyPublicId(e) {
            e.preventDefault();
            const publicId = document.getElementById('publicIdDisplay').innerText.trim();
            navigator.clipboard.writeText(publicId).then(() => {
                alert('Public ID copied to clipboard!');
            });
        }

        async function regenerateKeys(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to generate a new Public ID?')) return;

            const formData = new FormData();
            formData.append('action', 'regenerate_keys');

            try {
                const response = await fetch('/member/index.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.status) {
                    document.getElementById('publicIdDisplay').innerText = result.public_id;
                    alert(result.message);
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('An error occurred while generating new keys.');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>