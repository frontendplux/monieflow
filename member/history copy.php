<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Token Authentication Guard
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

// -----------------------------------------------------------------------------
// FETCH TRANSACTION HISTORY
// -----------------------------------------------------------------------------
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$query = "SELECT * FROM transaction WHERE uid = ?";
$params = [$user['uid']];
$types = "s";

if ($filter === 'credit' || $filter === 'debit') {
    $query .= " AND type = ?";
    $params[] = $filter;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND description LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$tStmt = $conn->prepare($query);
$tStmt->bind_param($types, ...$params);
$tStmt->execute();
$transactions = $tStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Recent Escrows Log
$eStmt = $conn->prepare("
    SELECT e.*, l.type as listing_type 
    FROM p2p_escrows e 
    JOIN p2p_listings l ON e.listing_id = l.id 
    WHERE e.buyer_uid = ? OR e.seller_uid = ? 
    ORDER BY e.created_at DESC LIMIT 10
");
$eStmt->bind_param("ss", $user['uid'], $user['uid']);
$eStmt->execute();
$escrows = $eStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Activity & Transaction History</title>
    
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
            padding: 1.5rem;
        }

        .icon-shape {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

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

    <!-- Header / Navbar -->
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

    <!-- Main Content -->
    <main class="container py-4">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Activity History</h1>
                <p class="text-muted small mb-0">Track all your credits, debits, and escrow transactions.</p>
            </div>
            
            <!-- Filters & Search Form -->
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm rounded-pill" placeholder="Search description..." value="<?= htmlspecialchars($search) ?>">
                <select name="filter" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Types</option>
                    <option value="credit" <?= $filter === 'credit' ? 'selected' : '' ?>>Credits Only</option>
                    <option value="debit" <?= $filter === 'debit' ? 'selected' : '' ?>>Debits Only</option>
                </select>
            </form>
        </div>

        <div class="row g-4">
            <!-- Main Ledger -->
            <div class="col-12 col-lg-8">
                <div class="history-card">
                    <h5 class="fw-bold mb-3">Wallet Ledger</h5>

                    <?php if (!empty($transactions)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="small text-muted">
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $tx): ?>
                                        <tr>
                                            <td style="width: 50px;">
                                                <div class="icon-shape bg-<?= $tx['type'] === 'credit' ? 'success' : 'danger' ?>-subtle text-<?= $tx['type'] === 'credit' ? 'success' : 'danger' ?>">
                                                    <i class="bi bi-arrow-<?= $tx['type'] === 'credit' ? 'down-left' : 'up-right' ?>"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold small"><?= htmlspecialchars($tx['description']) ?></div>
                                                <small class="text-muted text-uppercase" style="font-size: 0.7rem;"><?= $tx['type'] ?></small>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-<?= $tx['type'] === 'credit' ? 'success' : 'danger' ?>">
                                                    <?= $tx['type'] === 'credit' ? '+' : '-' ?><?= number_format($tx['amount'], 2) ?> MF
                                                </span>
                                            </td>
                                            <td class="text-muted small">
                                                <?= date('M d, Y H:i', strtotime($tx['created_at'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-receipt fs-1 text-muted mb-2 d-block"></i>
                            <p class="text-muted">No transaction logs found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Escrows Summary -->
            <div class="col-12 col-lg-4">
                <div class="history-card">
                    <h5 class="fw-bold mb-3">Escrow Status</h5>

                    <?php if (!empty($escrows)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($escrows as $esc): ?>
                                <?php 
                                    $isBuyer = ($esc['buyer_uid'] === $user['uid']);
                                    $role = $isBuyer ? 'Buyer' : 'Seller';
                                ?>
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-<?= $esc['status'] === 'released' ? 'success' : ($esc['status'] === 'locked' ? 'warning' : 'secondary') ?> rounded-pill">
                                            <?= strtoupper($esc['status']) ?>
                                        </span>
                                        <small class="text-muted"><?= date('M d, H:i', strtotime($esc['created_at'])) ?></small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="d-block small"><?= number_format($esc['amount'], 2) ?> MF</strong>
                                            <small class="text-muted">Role: <?= $role ?></small>
                                        </div>
                                        <?php if ($esc['deposit_pin']): ?>
                                            <code class="fw-bold"><?= htmlspecialchars($esc['deposit_pin']) ?></code>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-shield-slash fs-2 text-muted mb-2 d-block"></i>
                            <p class="text-muted small">No escrow history found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

    <!-- Bottom Mobile Nav -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col"><a href="/member/index.php" class="nav-link"><i class="bi bi-house-door"></i><span>Home</span></a></div>
                <div class="col"><a href="/member/deposit.php" class="nav-link"><i class="bi bi-arrow-down-circle"></i><span>Deposit</span></a></div>
                <div class="col"><a href="/member/peer2peer.php" class="nav-link"><i class="bi bi-people"></i><span>P2P</span></a></div>
                <div class="col"><a href="/member/settings.php" class="nav-link"><i class="bi bi-gear"></i><span>Settings</span></a></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>