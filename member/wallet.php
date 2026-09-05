<?php
session_start();
include __DIR__ . '/../conn.php';

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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

// Exchange Rate & Country Code Lookup
$userCountryCode = $_SESSION['user_country_code'] ?? 'NG';
$rateStmt = $conn->prepare("SELECT currency_code, amount FROM monieflow_coin_values WHERE country_code = ? LIMIT 1");
$rateStmt->bind_param("s", $userCountryCode);
$rateStmt->execute();
$rateData = $rateStmt->get_result()->fetch_assoc();

if (!$rateData) {
    $fallbackStmt = $conn->query("SELECT currency_code, amount FROM monieflow_coin_values WHERE country_code = 'NG' LIMIT 1");
    $rateData = $fallbackStmt->fetch_assoc();
}

$currencyCode = $rateData['currency_code'] ?? 'NGN';
$mfExchangeRate = floatval($rateData['amount'] ?? 1.00);

// Fetch Wallet Info
$walletStmt = $conn->prepare("SELECT public_id, account_number, balance FROM wallets WHERE uid = ?");
$walletStmt->bind_param("s", $user['uid']);
$walletStmt->execute();
$wallet = $walletStmt->get_result()->fetch_assoc();

$balanceMF = floatval($wallet['balance'] ?? 0.00);
$fiatEquivalent = $balanceMF * $mfExchangeRate;

/// -----------------------------------------------------------------------------
// Fetch Purchased & Acquired Items (Matches Actual Database Schema)
// -----------------------------------------------------------------------------
$purchasesQuery = "
    SELECT 
        'auction' AS category,
        COALESCE(art.artifact_name, ai.title, 'Acquired Artifact') AS item_name,
        COALESCE(art.description, ai.description, 'No description provided.') AS item_desc,
        COALESCE(ba.current_bid, ab.bid_amount, 0) AS cost_mf,
        ba.expires_at AS purchase_date,
        'Artifact Acquired' AS type_label
    FROM bounty_auctions ba
    LEFT JOIN artifacts art ON ba.artifact_id = art.id
    LEFT JOIN artifact_items ai ON ba.item_id = ai.id
    INNER JOIN (
        SELECT auction_id, bidder_uid, MAX(bid_amount) AS bid_amount
        FROM auction_bids
        GROUP BY auction_id, bidder_uid
    ) ab ON ba.id = ab.auction_id
    WHERE ab.bidder_uid = ? AND ba.status = 'completed'

    UNION ALL

    SELECT 
        'task' AS category,
        t.title AS item_name,
        t.description AS item_desc,
        t.reward_mf AS cost_mf,
        ts.submitted_at AS purchase_date,
        'Task Reward' AS type_label
    FROM task_submissions ts
    JOIN tasks t ON ts.task_id = t.id
    WHERE ts.member_uid = ? AND ts.status = 'approved'

    ORDER BY purchase_date DESC
";

$purchases = [];
if ($purchasesStmt = $conn->prepare($purchasesQuery)) {
    // Exactly 2 parameters bound since winner_uid and updated_at were removed
    $purchasesStmt->bind_param("ss", $user['uid'], $user['uid']);
    $purchasesStmt->execute();
    $purchases = $purchasesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Wallet & Assets</title>
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

        .item-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            padding: 1.25rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 168, 232, 0.1);
        }

        .item-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.85rem;
            background-color: rgba(0, 168, 232, 0.1);
            color: var(--brand-skyblue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
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
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/member/index.php">
                <img src="/logo.png" alt="MonieFlow">
                <span style="color: var(--brand-skyblue-hover);">MonieFlow</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Logged in as</small>
                    <span class="fw-semibold small"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <a href="/member/index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4 px-3">

        <!-- Header Title -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1"><i class="bi bi-wallet2 text-primary me-2"></i>My Wallet & Assets</h1>
                <p class="text-muted mb-0 small">
                    Manage your balance and review items or artifact rewards won and acquired on the platform.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="/member/deposit.php" class="btn btn-skyblue rounded-3 px-3 py-2">
                    <i class="bi bi-plus-circle me-1"></i> Deposit
                </a>
                <a href="/member/bidding.php" class="btn btn-outline-primary rounded-3 px-3 py-2">
                    <i class="bi bi-hammer me-1"></i> Go to Auctions
                </a>
            </div>
        </div>

        <!-- Balance Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="balance-card p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="small opacity-75 d-block mb-1">Available MonieFlow Balance</span>
                        <h2 class="display-6 fw-bold mb-1"><?= number_format($balanceMF, 2) ?> <span class="fs-5">MF</span></h2>
                        <small class="opacity-75">
                            ≈ <?= htmlspecialchars($currencyCode) ?> <?= number_format($fiatEquivalent, 2) ?> 
                            <span class="badge bg-white text-dark ms-1" style="font-size: 0.65rem; opacity: 0.9;"><?= $userCountryCode ?></span>
                        </small>
                    </div>
                    <div class="text-md-end pt-3 pt-md-0 border-top border-white-50 border-md-0">
                        <span class="d-block small opacity-75">Account Number</span>
                        <span class="fw-bold fs-5"><?= htmlspecialchars($wallet['account_number'] ?? 'N/A') ?></span>
                        <span class="badge bg-success-subtle text-white d-block mt-1" style="font-size: 0.7rem;">Active Wallet</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchased / Acquired Items Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Purchased & Acquired Items</h5>
            <span class="badge bg-light text-dark border"><?= count($purchases) ?> Total Items</span>
        </div>

        <div class="row g-3 mb-4">
            <?php if (!empty($purchases)): ?>
                <?php foreach ($purchases as $item): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="item-card d-flex align-items-start gap-3">
                            <div class="item-icon">
                                <i class="bi <?= $item['category'] === 'auction' ? 'bi-hammer' : 'bi-check2-circle' ?>"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.68rem;">
                                        <?= htmlspecialchars($item['type_label']) ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?= date('M d, Y', strtotime($item['purchase_date'])) ?>
                                    </small>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 text-truncate"><?= htmlspecialchars($item['item_name']) ?></h6>
                                <p class="text-muted small mb-2 text-truncate"><?= htmlspecialchars($item['item_desc'] ?? 'No description provided.') ?></p>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <small class="text-muted"><?= $item['category'] === 'auction' ? 'Cost' : 'Earned' ?></small>
                                    <span class="fw-bold text-primary"><?= number_format($item['cost_mf'], 2) ?> MF</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-bag-x fs-1 text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-0">No purchased or acquired items found yet.</p>
                    <small class="text-muted">Win auctions or complete tasks to build your inventory.</small>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Mobile Navigation Bar -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col">
                    <a href="/member/index.php" class="nav-link">
                        <i class="bi bi-house-door"></i>
                        <span>Home</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/wallet.php" class="nav-link active">
                        <i class="bi bi-wallet2"></i>
                        <span>Wallet</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/tasks.php" class="nav-link">
                        <i class="bi bi-check2-square"></i>
                        <span>Tasks</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/bidding.php" class="nav-link">
                        <i class="bi bi-hammer"></i>
                        <span>Bidding</span>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>