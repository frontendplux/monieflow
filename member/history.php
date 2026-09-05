<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Token Guard
$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
    header("Location: /index.php");
    exit();
}

// Fetch Logged-in User
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

// 2. Filter Transactions
$typeFilter = $_GET['type'] ?? 'all';

$query = "SELECT * FROM transaction WHERE uid = ?";
if ($typeFilter === 'credit' || $typeFilter === 'debit') {
    $query .= " AND type = '" . $conn->real_escape_string($typeFilter) . "'";
}
$query .= " ORDER BY created_at DESC";

$tStmt = $conn->prepare($query);
$tStmt->bind_param("s", $user['uid']);
$tStmt->execute();
$transactions = $tStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Transaction History</title>
    
    <link rel="icon" type="image/png" href="/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        @media (min-width: 992px) { body { padding-bottom: 20px; } }

        .history-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .history-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 168, 232, 0.08);
        }

        .icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .icon-credit { background-color: #d1fae5; color: #10b981; }
        .icon-debit { background-color: #fee2e2; color: #ef4444; }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #ffffff;
            border-top: 1px solid rgba(0, 168, 232, 0.15);
            z-index: 1030;
        }

        .mobile-bottom-nav .nav-link { color: var(--text-muted); font-size: 0.72rem; padding: 8px 0; text-align: center; }
        .mobile-bottom-nav .nav-link.active { color: var(--brand-skyblue); }
        .mobile-bottom-nav i { font-size: 1.25rem; display: block; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/member/index.php">
                <img src="/logo.png" alt="MonieFlow" style="width: 40px; height: 40px; background: #008cc3; border-radius: 50%; padding: 4px;">
                <span style="color: var(--brand-skyblue-hover);">MonieFlow</span>
            </a>
            <a href="/member/index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <main class="container py-4">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Transaction History</h1>
                <p class="text-muted small mb-0">Track all your credits, debits, and transfers.</p>
            </div>

            <div class="btn-group rounded-pill p-1 bg-white border">
                <a href="?type=all" class="btn btn-sm <?= $typeFilter === 'all' ? 'btn-primary' : 'btn-light' ?> rounded-pill px-3">All</a>
                <a href="?type=credit" class="btn btn-sm <?= $typeFilter === 'credit' ? 'btn-success' : 'btn-light' ?> rounded-pill px-3">Credits</a>
                <a href="?type=debit" class="btn btn-sm <?= $typeFilter === 'debit' ? 'btn-danger' : 'btn-light' ?> rounded-pill px-3">Debits</a>
            </div>
        </div>

        <div class="row g-3">
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $tx): ?>
                    <?php $isCredit = $tx['type'] === 'credit'; ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="history-card">
                            <div class="d-flex align-items-center justify-content-between">
                                
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle <?= $isCredit ? 'icon-credit' : 'icon-debit' ?>">
                                        <i class="bi bi-arrow-<?= $isCredit ? 'down-left' : 'up-right' ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($tx['description'] ?? ($isCredit ? 'Credit Transaction' : 'Debit Transaction')) ?></h6>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            <?= date('M d, Y • h:i A', strtotime($tx['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <span class="fw-bold fs-6 <?= $isCredit ? 'text-success' : 'text-danger' ?>">
                                        <?= $isCredit ? '+' : '-' ?><?= number_format($tx['amount'], 2) ?> MF
                                    </span>
                                    <span class="d-block badge bg-<?= $isCredit ? 'success' : 'danger' ?>-subtle text-<?= $isCredit ? 'success' : 'danger' ?> rounded-pill px-2 py-1 mt-1 text-uppercase" style="font-size: 0.65rem;">
                                        <?= $tx['type'] ?>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-receipt fs-1 text-muted mb-2 d-block"></i>
                    <p class="text-muted">No transactions found for this filter.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col"><a href="/member/index.php" class="nav-link"><i class="bi bi-house-door"></i><span>Home</span></a></div>
                <div class="col"><a href="/member/deposit.php" class="nav-link"><i class="bi bi-arrow-down-circle"></i><span>Deposit</span></a></div>
                <div class="col"><a href="/member/peer2peer.php" class="nav-link"><i class="bi bi-people"></i><span>P2P</span></a></div>
                <div class="col"><a href="/member/history.php" class="nav-link active"><i class="bi bi-clock-history"></i><span>History</span></a></div>
                <div class="col"><a href="/member/settings.php" class="nav-link"><i class="bi bi-gear"></i><span>Settings</span></a></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>