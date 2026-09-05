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

// Ensure referral code exists (fallback to username or UID)
$refCode = $user['username'] ?? $user['uid'];
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$referralLink = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/register.php?ref=" . urlencode($refCode);

// Fetch Referral Stats
$statStmt = $conn->prepare("SELECT COUNT(*) AS total_ref, COALESCE(SUM(reward_mf), 0) AS total_earned FROM referrals WHERE referrer_uid = ?");
$statStmt->bind_param("s", $user['uid']);
$statStmt->execute();
$stats = $statStmt->get_result()->fetch_assoc();

// Fetch Recent Referrals List
$listStmt = $conn->prepare("
    SELECT r.*, u.username, u.email 
    FROM referrals r 
    JOIN users u ON r.referred_uid = u.uid 
    WHERE r.referrer_uid = ? 
    ORDER BY r.created_at DESC LIMIT 10
");
$listStmt->bind_param("s", $user['uid']);
$listStmt->execute();
$recentReferrals = $listStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Referral Program</title>
    
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

        .custom-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            padding: 1.5rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            background: rgba(0, 168, 232, 0.1);
            color: var(--brand-skyblue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
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

        <!-- Header Title -->
        <div class="mb-4">
            <h1 class="h3 fw-bold mb-1">Refer & Earn MF</h1>
            <p class="text-muted small">Invite your friends to MonieFlow and earn MF rewards for every active user!</p>
        </div>

        <!-- Referral Link Banner -->
        <div class="custom-card mb-4 bg-primary text-white" style="background: linear-gradient(135deg, #00a8e8 0%, #00719e 100%);">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-7">
                    <h5 class="fw-bold mb-2"><i class="bi bi-gift me-2"></i>Your Referral Link</h5>
                    <p class="small opacity-75 mb-3">Share this link to claim 10 MF for each friend who registers.</p>
                    <div class="input-group">
                        <input type="text" class="form-control border-0 fw-semibold" id="refLinkInput" value="<?= htmlspecialchars($referralLink) ?>" readonly>
                        <button class="btn btn-dark px-4" onclick="copyRefLink()"><i class="bi bi-copy me-1"></i> Copy</button>
                    </div>
                </div>
                <div class="col-12 col-md-5 text-md-end">
                    <span class="d-block small opacity-75">Your Referral Code</span>
                    <span class="fs-3 fw-bold border-bottom border-2 border-white pb-1"><?= htmlspecialchars($refCode) ?></span>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6">
                <div class="custom-card d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div>
                        <span class="text-muted small d-block">Total Referrals</span>
                        <h3 class="fw-bold mb-0"><?= number_format($stats['total_ref']) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="custom-card d-flex align-items-center gap-3">
                    <div class="stat-icon text-success" style="background: rgba(16, 185, 129, 0.1);"><i class="bi bi-wallet2"></i></div>
                    <div>
                        <span class="text-muted small d-block">Total Earned</span>
                        <h3 class="fw-bold mb-0 text-success"><?= number_format($stats['total_earned'], 2) ?> MF</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Referrals Table -->
        <div class="custom-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Referred Users History</h6>
            <?php if (!empty($recentReferrals)): ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>User</th>
                                <th>Reward</th>
                                <th>Status</th>
                                <th>Date Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentReferrals as $ref): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($ref['username']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($ref['email']) ?></small>
                                    </td>
                                    <td class="fw-bold text-success">+<?= number_format($ref['reward_mf'], 2) ?> MF</td>
                                    <td><span class="badge bg-success-subtle text-success border border-success-subtle">Completed</span></td>
                                    <td class="small text-muted"><?= date('M d, Y', strtotime($ref['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-person-x fs-2 text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-0 small">No referrals yet. Share your link to start earning!</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Bottom Mobile Nav -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col"><a href="/member/index.php" class="nav-link"><i class="bi bi-house-door"></i><span>Home</span></a></div>
                <div class="col"><a href="/member/chart.php" class="nav-link"><i class="bi bi-graph-up"></i><span>Chart</span></a></div>
                <div class="col"><a href="/member/tasks.php" class="nav-link"><i class="bi bi-check2-square"></i><span>Tasks</span></a></div>
                <div class="col"><a href="/member/referral.php" class="nav-link active"><i class="bi bi-person-plus"></i><span>Referral</span></a></div>
                <div class="col"><a href="/member/settings.php" class="nav-link"><i class="bi bi-gear"></i><span>Settings</span></a></div>
            </div>
        </div>
    </div>

    <script>
        function copyRefLink() {
            const input = document.getElementById('refLinkInput');
            input.select();
            navigator.clipboard.writeText(input.value).then(() => {
                alert('Referral link copied to clipboard!');
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>