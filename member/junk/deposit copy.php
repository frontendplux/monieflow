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

// 2. Validate Session User
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

$alertMessage = '';
$alertType = '';

// 3. Handle Deposit Redemption Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_deposit'])) {
    $inputUid = trim($_POST['uid'] ?? '');
    $inputPin = trim($_POST['pin_code'] ?? '');

    if (empty($inputUid) || empty($inputPin)) {
        $alertType = 'danger';
        $alertMessage = 'Please fill in both the UID code and the 6-digit code.';
    } elseif ($inputUid !== $user['uid']) {
        $alertType = 'danger';
        $alertMessage = 'Invalid UID code provided. It must match your account UID.';
    } else {
        // Find matching pending deposit record
        $depositStmt = $conn->prepare("SELECT * FROM deposits WHERE uid = ? AND pin_code = ? AND status = 'pending' LIMIT 1");
        $depositStmt->bind_param("ss", $inputUid, $inputPin);
        $depositStmt->execute();
        $deposit = $depositStmt->get_result()->fetch_assoc();

        if (!$deposit) {
            $alertType = 'danger';
            $alertMessage = 'Invalid or expired 6-digit code. Please check and try again.';
        } else {
            $depositAmount = $deposit['amount'];
            $depositId = $deposit['id'];

            // Begin Database Transaction to avoid partial state updates
            $conn->begin_transaction();

            try {
                // Step A: Update wallet balance
                $updateWalletStmt = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE uid = ?");
                $updateWalletStmt->bind_param("ds", $depositAmount, $inputUid);
                $updateWalletStmt->execute();

                // Step B: Record transaction entry
                $description = "Wallet Deposit via Code (" . htmlspecialchars($inputPin) . ")";
                $payloads = json_encode(['deposit_id' => $depositId, 'pin_code' => $inputPin]);

                $txStmt = $conn->prepare("INSERT INTO transaction (uid, amount, type, description, payloads) VALUES (?, ?, 'credit', ?, ?)");
                $txStmt->bind_param("sdss", $inputUid, $depositAmount, $description, $payloads);
                $txStmt->execute();

                // Step C: Delete the redeemed code from deposits table
                $deleteStmt = $conn->prepare("DELETE FROM deposits WHERE id = ?");
                $deleteStmt->bind_param("i", $depositId);
                $deleteStmt->execute();

                // Commit Transaction
                $conn->commit();

                $alertType = 'success';
                $alertMessage = 'Deposit successful! ₦' . number_format($depositAmount, 2) . ' has been added to your wallet.';

            } catch (Exception $e) {
                // Rollback in case of any database error
                $conn->rollback();
                $alertType = 'danger';
                $alertMessage = 'Transaction failed. Please try again later.';
            }
        }
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

        .mobile-bottom-nav .nav-link.active {
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
                        <p class="text-muted small">Enter your UID code and 6-digit deposit code to fund your wallet.</p>
                    </div>

                    <?php if (!empty($alertMessage)): ?>
                        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
                            <?= $alertMessage ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="uid" class="form-label fw-semibold small">User UID Code</label>
                            <input type="text" class="form-control form-control-lg text-monospace" id="uid" name="uid" value="<?= htmlspecialchars($user['uid']) ?>" required readonly>
                            <div class="form-text">Auto-filled with your active session UID.</div>
                        </div>

                        <div class="mb-4">
                            <label for="pin_code" class="form-label fw-semibold small">6-Digit Code (Alphanumeric)</label>
                            <input type="text" class="form-control form-control-lg text-center fw-bold" id="pin_code" name="pin_code" maxlength="10" placeholder="e.g. X8A92B" required style="letter-spacing: 4px;">
                        </div>

                        <button type="submit" name="submit_deposit" class="btn btn-skyblue btn-lg w-100 py-3 rounded-3">
                            <i class="bi bi-wallet2 me-2"></i> Deposit Now
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <?php include __DIR__."/nav-xs.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
CREATE TABLE IF NOT EXISTS deposits (
    id int AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) NOT NULL,
    pin_code VARCHAR(10) NOT NULL,
    amount DECIMAL(36, 15) default 0.00,
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    constraint fk_deposit_user FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
);
the deposit any body can use it.  not user specific the uid there is just the person owns it so we can keep track of transaction when not used 
for the owner 
