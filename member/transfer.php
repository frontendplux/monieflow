<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Token Authentication Guard
$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Unauthorized access.']);
        exit();
    }
    header("Location: /index.php");
    exit();
}

// 2. Fetch Sender Details
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
$stmt->bind_param("ss", $suid, $token);
$stmt->execute();
$sender = $stmt->get_result()->fetch_assoc();

if (!$sender) {
    session_unset();
    session_destroy();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Session expired.']);
        exit();
    }
    header("Location: /index.php");
    exit();
}

// Fetch Sender Wallet Balance & Account Details
$stmt = $conn->prepare("SELECT * FROM wallets WHERE uid = ?");
$stmt->bind_param("s", $sender['uid']);
$stmt->execute();
$senderWallet = $stmt->get_result()->fetch_assoc();

// -----------------------------------------------------------------------------
// AJAX API ENDPOINTS
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // Action A: Verify Recipient Account or UID
    if ($action === 'verify_recipient') {
        $recipientInput = trim($_POST['recipient'] ?? '');

        if (empty($recipientInput)) {
            echo json_encode(['status' => false, 'message' => 'Please enter account number or UID.']);
            exit();
        }

        // Search by Account Number or UID
        $query = "SELECT u.full_name, u.username, u.uid, w.account_number 
                  FROM users u 
                  JOIN wallets w ON u.uid = w.uid 
                  WHERE (w.account_number = ? OR u.uid = ?) AND u.is_active = TRUE";
        $vStmt = $conn->prepare($query);
        $vStmt->bind_param("ss", $recipientInput, $recipientInput);
        $vStmt->execute();
        $recipient = $vStmt->get_result()->fetch_assoc();

        if (!$recipient) {
            echo json_encode(['status' => false, 'message' => 'Recipient account not found.']);
            exit();
        }

        if ($recipient['uid'] === $sender['uid']) {
            echo json_encode(['status' => false, 'message' => 'You cannot transfer funds to yourself.']);
            exit();
        }

        echo json_encode([
            'status' => true,
            'name' => $recipient['full_name'],
            'username' => $recipient['username'],
            'account' => $recipient['account_number'],
            'uid' => $recipient['uid']
        ]);
        exit();
    }

    // Action B: Process Transfer
    if ($action === 'process_transfer') {
        $recipientUid = trim($_POST['recipient_uid'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $note = trim($_POST['description'] ?? 'Fund Transfer');

        if (empty($recipientUid) || $amount <= 0) {
            echo json_encode(['status' => false, 'message' => 'Invalid transfer parameters.']);
            exit();
        }

        if ($senderWallet['balance'] < $amount) {
            echo json_encode(['status' => false, 'message' => 'Insufficient wallet balance.']);
            exit();
        }

        // Verify recipient exists
        $rStmt = $conn->prepare("SELECT uid FROM users WHERE uid = ? AND is_active = TRUE");
        $rStmt->bind_param("s", $recipientUid);
        $rStmt->execute();
        $recipientUser = $rStmt->get_result()->fetch_assoc();

        if (!$recipientUser) {
            echo json_encode(['status' => false, 'message' => 'Invalid recipient selected.']);
            exit();
        }

        // Perform Transfer with Database Transaction
        $conn->begin_transaction();

        try {
            // 1. Debit Sender Wallet
            $debitStmt = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE uid = ? AND balance >= ?");
            $debitStmt->bind_param("dsd", $amount, $sender['uid'], $amount);
            $debitStmt->execute();

            if ($debitStmt->affected_rows === 0) {
                throw new Exception("Transfer failed due to insufficient funds.");
            }

            // 2. Credit Receiver Wallet
            $creditStmt = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE uid = ?");
            $creditStmt->bind_param("ds", $amount, $recipientUid);
            $creditStmt->execute();

            // 3. Log Sender Transaction (Debit)
            $senderDesc = "Transfer to " . $recipientUid . ($note ? " ($note)" : "");
            $sTx = $conn->prepare("INSERT INTO transaction (uid, amount, type, description) VALUES (?, ?, 'debit', ?)");
            $sTx->bind_param("sds", $sender['uid'], $amount, $senderDesc);
            $sTx->execute();

            // 4. Log Receiver Transaction (Credit)
            $receiverDesc = "Transfer from " . $sender['username'] . ($note ? " ($note)" : "");
            $rTx = $conn->prepare("INSERT INTO transaction (uid, amount, type, description) VALUES (?, ?, 'credit', ?)");
            $rTx->bind_param("sds", $recipientUid, $amount, $receiverDesc);
            $rTx->execute();

            // Commit Transaction
            $conn->commit();

            // Get Updated Balance
            $newBalStmt = $conn->prepare("SELECT balance FROM wallets WHERE uid = ?");
            $newBalStmt->bind_param("s", $sender['uid']);
            $newBalStmt->execute();
            $newWallet = $newBalStmt->get_result()->fetch_assoc();

            echo json_encode([
                'status' => true,
                'message' => 'Transfer of ₦' . number_format($amount, 2) . ' was successful!',
                'new_balance' => number_format($newWallet['balance'], 2)
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Transfer Funds</title>
    
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

        .transfer-card {
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

        .mobile-bottom-nav .nav-link.active { color: var(--brand-skyblue); }
        .mobile-bottom-nav i { font-size: 1.25rem; display: block; margin-bottom: 2px; }
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

    <!-- Main Content -->
    <main class="container py-4 px-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                
                <div class="transfer-card p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-arrow-up-right-circle fs-2 text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Transfer Funds</h4>
                        <p class="text-muted small mb-0">Balance: <strong id="currentBalance">₦<?= number_format($senderWallet['balance'], 2) ?></strong></p>
                    </div>

                    <!-- Alert Box -->
                    <div id="alertContainer"></div>

                    <form id="transferForm">
                        <input type="hidden" id="confirmedRecipientUid" name="recipient_uid">

                        <!-- Recipient Input & Verify Button -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Recipient Account No. or UID</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="recipientInput" placeholder="Enter Acc No. or UID" required>
                                <button class="btn btn-outline-secondary" type="button" id="verifyBtn" onclick="verifyRecipient()">
                                    <span id="verifyBtnSpinner" class="spinner-border spinner-border-sm d-none me-1"></span>
                                    Verify
                                </button>
                            </div>
                        </div>

                        <!-- Recipient Badge Details -->
                        <div id="recipientBadge" class="p-3 mb-3 bg-light border rounded-3 d-none">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Verified Account</small>
                            <span class="fw-bold text-success" id="recipientName">---</span>
                            <small class="text-muted d-block" id="recipientAccount">Account: ---</small>
                        </div>

                        <!-- Amount Input -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Amount (₦)</label>
                            <input type="number" step="0.01" min="1" class="form-control form-control-lg fw-bold" id="amountInput" placeholder="0.00" required>
                        </div>

                        <!-- Description Input -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Description (Optional)</label>
                            <input type="text" class="form-control" id="descInput" placeholder="What is this transfer for?">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-skyblue btn-lg w-100 py-3 rounded-3" id="submitTransferBtn" disabled>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                            Send Money
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <!-- Small Screen Bottom Nav -->
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
                    <a href="/member/deposit.php" class="nav-link">
                        <i class="bi bi-arrow-down-circle"></i>
                        <span>Deposit</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/transfer.php" class="nav-link active">
                        <i class="bi bi-arrow-up-right-circle"></i>
                        <span>Transfer</span>
                    </a>
                </div>
                <div class="col">
                    <a href="#" class="nav-link">
                        <i class="bi bi-people"></i>
                        <span>P2P</span>
                    </a>
                </div>
                <div class="col">
                    <a href="#" class="nav-link">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Fetch API Implementation -->
    <script>
        function showAlert(type, message) {
            const container = document.getElementById('alertContainer');
            container.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
        }

        async function verifyRecipient() {
            const recipientInput = document.getElementById('recipientInput').value.trim();
            const verifyBtn = document.getElementById('verifyBtn');
            const spinner = document.getElementById('verifyBtnSpinner');
            const badge = document.getElementById('recipientBadge');
            const submitBtn = document.getElementById('submitTransferBtn');

            if (!recipientInput) {
                showAlert('danger', 'Please enter a valid Account Number or UID.');
                return;
            }

            verifyBtn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData();
            formData.append('action', 'verify_recipient');
            formData.append('recipient', recipientInput);

            try {
                const response = await fetch('/member/transfer.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status) {
                    document.getElementById('confirmedRecipientUid').value = result.uid;
                    document.getElementById('recipientName').innerText = result.name + ' (@' + result.username + ')';
                    document.getElementById('recipientAccount').innerText = 'Account: ' + result.account;
                    badge.classList.remove('d-none');
                    submitBtn.disabled = false;
                    showAlert('success', 'Recipient account verified successfully.');
                } else {
                    badge.classList.add('d-none');
                    submitBtn.disabled = true;
                    document.getElementById('confirmedRecipientUid').value = '';
                    showAlert('danger', result.message);
                }
            } catch (err) {
                showAlert('danger', 'Unable to verify recipient. Please try again.');
            } finally {
                verifyBtn.disabled = false;
                spinner.classList.add('d-none');
            }
        }

        // Form Submit handler via Fetch
        document.getElementById('transferForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const recipientUid = document.getElementById('confirmedRecipientUid').value;
            const amount = document.getElementById('amountInput').value;
            const description = document.getElementById('descInput').value;
            const submitBtn = document.getElementById('submitTransferBtn');
            const spinner = document.getElementById('submitSpinner');

            if (!recipientUid) {
                showAlert('danger', 'Please verify the recipient before sending funds.');
                return;
            }

            if (!confirm(`Are you sure you want to transfer ₦${parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2})}?`)) {
                return;
            }

            submitBtn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData();
            formData.append('action', 'process_transfer');
            formData.append('recipient_uid', recipientUid);
            formData.append('amount', amount);
            formData.append('description', description);

            try {
                const response = await fetch('/member/transfer.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status) {
                    showAlert('success', result.message);
                    document.getElementById('currentBalance').innerText = '₦' + result.new_balance;
                    
                    // Reset form fields
                    document.getElementById('transferForm').reset();
                    document.getElementById('recipientBadge').classList.add('d-none');
                    document.getElementById('confirmedRecipientUid').value = '';
                    window.location.href = '/member/index.php'; // Redirect to dashboard after successful transfer
                } else {
                    showAlert('danger', result.message);
                    submitBtn.disabled = false;
                }
            } catch (err) {
                showAlert('danger', 'Transfer failed due to a network error.');
                submitBtn.disabled = false;
            } finally {
                spinner.classList.add('d-none');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>