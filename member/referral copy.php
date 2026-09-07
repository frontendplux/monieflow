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

// 2. Localization & Multi-Currency Engine
// Base referral reward value internal to system (10 NGN)
define('BASE_REWARD_NGN', 10.00); 

// Detect client IP address
$userIp = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (strpos($userIp, ',') !== false) {
    $userIp = trim(explode(',', $userIp)[0]);
}

// Fetch IP Geolocation data (fallback to USD/NGN if localhost or lookup fails)
$userCurrency = $user['currency'] ?? 'NGN'; 
$currencySymbol = '₦';

if ($userIp !== '127.0.0.1' && $userIp !== '::1') {
    $geoJson = @file_get_contents("http://ip-api.com/json/{$userIp}?fields=currency,status");
    if ($geoJson) {
        $geoData = json_decode($geoJson, true);
        if (!empty($geoData['currency'])) {
            $userCurrency = $geoData['currency'];
        }
    }
}

// Map Currency Symbols
$currencySymbols = [
    'NGN' => '₦',
    'USD' => '$',
    'GBP' => '£',
    'EUR' => '€',
    'GHS' => 'GH₵',
    'KES' => 'KSh',
    'ZAR' => 'R'
];
$currencySymbol = $currencySymbols[$userCurrency] ?? $userCurrency . ' ';

/**
 * Fetch live exchange rates against NGN
 * Returns exchange rate: 1 NGN = X Local Currency
 */
function getExchangeRateToLocal($targetCurrency) {
    if ($targetCurrency === 'NGN') return 1.0;

    // Cache rate in session to prevent API rate-limiting
    if (isset($_SESSION['exchange_rates'][$targetCurrency])) {
        return $_SESSION['exchange_rates'][$targetCurrency];
    }

    $apiUrl = "https://open.er-api.com/v6/latest/NGN";
    $json = @file_get_contents($apiUrl);
    if ($json) {
        $data = json_decode($json, true);
        if ($data && isset($data['rates'][$targetCurrency])) {
            $_SESSION['exchange_rates'][$targetCurrency] = (float)$data['rates'][$targetCurrency];
            return $_SESSION['exchange_rates'][$targetCurrency];
        }
    }

    // Fallback static conversion safety net
    $fallbacks = ['USD' => 0.00067, 'GBP' => 0.00053, 'EUR' => 0.00062, 'GHS' => 0.010, 'KES' => 0.086];
    return $fallbacks[$targetCurrency] ?? 1.0;
}

$rateToLocal = getExchangeRateToLocal($userCurrency);

// Calculate 10 NGN equivalent in user's detected local currency
$userRewardInLocal = BASE_REWARD_NGN * $rateToLocal;

// Convert reward to MF Tokens (Assuming 1 MF Token = 1 Unit of Local Currency or Custom Ratio)
// E.g., If 1 MF = 1 Local Currency unit:
$rewardInMF = $userRewardInLocal; 

// Ensure referral code exists
$refCode = $user['uid'];
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
            <p class="text-muted small">Invite your friends to MonieFlow and earn instant MF rewards on every registration!</p>
        </div>

        <!-- Referral Link Banner -->
        <div class="custom-card mb-4 bg-primary text-white" style="background: linear-gradient(135deg, #00a8e8 0%, #00719e 100%);">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-7">
                    <h5 class="fw-bold mb-2"><i class="bi bi-gift me-2"></i>Your Referral Link</h5>
                    <!-- Hidden internal NGN calculation; displaying local currency + MF value -->
                    <p class="small opacity-90 mb-3">
                        Share this link to claim <strong><?= number_format($rewardInMF, 2) ?> MF</strong> 
                        <span class="opacity-75">(~<?= htmlspecialchars($currencySymbol . number_format($userRewardInLocal, 2)) ?> <?= htmlspecialchars($userCurrency) ?>)</span> 
                        for each friend who registers.
                    </p>
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

    <?php $page='refferral'; include __DIR__."/nav-xs.php"; ?>

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
show list of people refered then a button to transfer to wallet
then referrals.status will now be completed not pending mean while have change the login referrals.status to pending
CREATE TABLE IF NOT EXISTS wallets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) NOT NULL UNIQUE,
    public_id VARCHAR(255) NOT NULL UNIQUE,
    private_key VARCHAR(255) NOT NULL,
    account_number VARCHAR(20) NOT NULL UNIQUE,
    balance DECIMAL(36, 15) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    constraint fk_user FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `referrals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `referrer_uid` VARCHAR(64) NOT NULL,
  `referred_uid` VARCHAR(64) NOT NULL,
  `reward_mf` DECIMAL(18, 4) DEFAULT 10.0000,
  `status` ENUM('pending', 'completed') DEFAULT 'completed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`referrer_uid`),
  INDEX (`referred_uid`)
);
