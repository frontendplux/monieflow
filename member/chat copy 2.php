<?php
session_start();
include __DIR__ . '/../conn.php';

$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Unauthorized']);
        exit();
    }
    header("Location: /index.php");
    exit();
}

// Authenticate User
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
$stmt->bind_param("ss", $suid, $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_unset();
    session_destroy();
    exit();
}

$listingId = intval($_GET['listing_id'] ?? $_POST['listing_id'] ?? 0);

// Fetch Listing & Partner Details
$query = "SELECT l.*, u.uid as owner_uid, u.username as owner_username, u.payloads as owner_payload 
          FROM p2p_listings l 
          JOIN users u ON l.uid = u.uid 
          WHERE l.id = ?";
$lStmt = $conn->prepare($query);
$lStmt->bind_param("i", $listingId);
$lStmt->execute();
$listing = $lStmt->get_result()->fetch_assoc();

if (!$listing) {
    die("Listing not found.");
}

$partnerUid = ($user['uid'] === $listing['owner_uid']) ? ($_GET['partner'] ?? $_POST['partner'] ?? '') : $listing['owner_uid'];

// Fetch Partner Information
$pStmt = $conn->prepare("SELECT uid, username, payloads FROM users WHERE uid = ?");
$pStmt->bind_param("s", $partnerUid);
$pStmt->execute();
$partnerUser = $pStmt->get_result()->fetch_assoc();

// Determine Seller & Buyer Roles
$sellerUid = ($listing['type'] === 'sell') ? $listing['owner_uid'] : $partnerUid;
$buyerUid  = ($listing['type'] === 'sell') ? $partnerUid : $listing['owner_uid'];
$isSeller  = ($user['uid'] === $sellerUid);
$isBuyer   = ($user['uid'] === $buyerUid);

// Decode Bank Accounts for Seller
$sellerPayload = json_decode($isSeller ? $user['payload'] : ($partnerUser['payload'] ?? '{}'), true);
$sellerBanks   = $sellerPayload['bank'] ?? [];

// -----------------------------------------------------------------------------
// AJAX ACTIONS
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // 1. Fetch Chat Messages, Active Escrow & Bank Info
    if ($action === 'fetch_messages') {
        $cStmt = $conn->prepare("SELECT * FROM p2p_chats WHERE listing_id = ? AND ((sender_uid = ? AND receiver_uid = ?) OR (sender_uid = ? AND receiver_uid = ?)) ORDER BY created_at ASC");
        $cStmt->bind_param("issss", $listingId, $user['uid'], $partnerUid, $partnerUid, $user['uid']);
        $cStmt->execute();
        $chats = $cStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fetch Escrow Status
        $eStmt = $conn->prepare("SELECT * FROM p2p_escrows WHERE listing_id = ? AND buyer_uid = ? AND seller_uid = ? ORDER BY id DESC LIMIT 1");
        $eStmt->bind_param("iss", $listingId, $buyerUid, $sellerUid);
        $eStmt->execute();
        $escrow = $eStmt->get_result()->fetch_assoc();

        echo json_encode([
            'status' => true, 
            'chats' => $chats, 
            'escrow' => $escrow, 
            'my_uid' => $user['uid'],
            'is_seller' => $isSeller,
            'is_buyer' => $isBuyer
        ]);
        exit();
    }

    // 2. Send Standard Chat Message
    if ($action === 'send_message') {
        $msg = trim($_POST['message'] ?? '');
        if (!empty($msg)) {
            $sStmt = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message) VALUES (?, ?, ?, ?)");
            $sStmt->bind_param("isss", $listingId, $user['uid'], $partnerUid, $msg);
            $sStmt->execute();
        }
        echo json_encode(['status' => true]);
        exit();
    }

    // 3. Initiate Escrow Trade (Buyer Action)
    if ($action === 'initiate_escrow') {
        if (!$isBuyer) {
            echo json_encode(['status' => false, 'message' => 'Only the buyer can initiate an order.']);
            exit();
        }

        $fiatAmount = floatval($_POST['fiat_amount'] ?? 0);
        $rate = floatval($listing['rate']);
        
        if ($fiatAmount <= 0 || $rate <= 0) {
            echo json_encode(['status' => false, 'message' => 'Invalid amount entered.']);
            exit();
        }

        $flowAmount = $fiatAmount / $rate;

        $conn->begin_transaction();
        try {
            // Ensure no existing locked escrow is active for this order
            $checkE = $conn->prepare("SELECT id FROM p2p_escrows WHERE listing_id = ? AND status = 'locked'");
            $checkE->bind_param("i", $listingId);
            $checkE->execute();
            if ($checkE->get_result()->num_rows > 0) {
                throw new Exception("An active escrow is already open for this trade.");
            }

            // Verify Seller Balance
            $balStmt = $conn->prepare("SELECT balance FROM wallets WHERE uid = ? FOR UPDATE");
            $balStmt->bind_param("s", $sellerUid);
            $balStmt->execute();
            $sellerWallet = $balStmt->get_result()->fetch_assoc();

            if (!$sellerWallet || $sellerWallet['balance'] < $flowAmount) {
                throw new Exception("Seller does not have sufficient balance in escrow wallet.");
            }

            // Lock funds from Seller's wallet
            $deductStmt = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE uid = ?");
            $deductStmt->bind_param("ds", $flowAmount, $sellerUid);
            $deductStmt->execute();

            // Insert Escrow Record
            $insE = $conn->prepare("INSERT INTO p2p_escrows (listing_id, buyer_uid, seller_uid, amount, status) VALUES (?, ?, ?, ?, 'locked')");
            $insE->bind_param("issd", $listingId, $buyerUid, $sellerUid, $flowAmount);
            $insE->execute();

            // Notify System Chat
            $sysMsg = "New Order Placed! Fiat: ₦" . number_format($fiatAmount, 2) . " | Crypto: " . number_format($flowAmount, 4) . " FLOW locked in escrow.";
            $sysStmt = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message, type) VALUES (?, ?, ?, ?, 'system')");
            $sysStmt->bind_param("isss", $listingId, $user['uid'], $partnerUid, $sysMsg);
            $sysStmt->execute();

            $conn->commit();
            echo json_encode(['status' => true, 'message' => 'Escrow locked successfully!']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // 4. Save/Update Seller Bank Account
    if ($action === 'save_bank_account') {
        if (!$isSeller) {
            echo json_encode(['status' => false, 'message' => 'Only the seller can manage bank details.']);
            exit();
        }

        $bankName = trim($_POST['bank_name'] ?? '');
        $accNumber = trim($_POST['acc_number'] ?? '');
        $accName = trim($_POST['acc_name'] ?? '');

        if (empty($bankName) || empty($accNumber) || empty($accName)) {
            echo json_encode(['status' => false, 'message' => 'Please complete all bank fields.']);
            exit();
        }

        $userPayload = json_decode($user['payload'] ?? '{}', true);
        if (!isset($userPayload['bank']) || !is_array($userPayload['bank'])) {
            $userPayload['bank'] = [];
        }

        $newBank = [
            'id' => uniqid(),
            'bank_name' => $bankName,
            'acc_number' => $accNumber,
            'acc_name' => $accName
        ];

        $userPayload['bank'][] = $newBank;
        $updatedPayload = json_encode($userPayload);

        $upStmt = $conn->prepare("UPDATE users SET payload = ? WHERE uid = ?");
        $upStmt->bind_param("ss", $updatedPayload, $user['uid']);
        $upStmt->execute();

        // Share new bank details into chat
        $bankMsg = "Payment Details Added:\nBank: $bankName\nAccount No: $accNumber\nName: $accName";
        $sStmt = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message) VALUES (?, ?, ?, ?)");
        $sStmt->bind_param("isss", $listingId, $user['uid'], $partnerUid, $bankMsg);
        $sStmt->execute();

        echo json_encode(['status' => true, 'message' => 'Bank details saved and posted to chat!']);
        exit();
    }

    // 5. Seller Share Existing Bank Account
    if ($action === 'share_bank') {
        $bankId = $_POST['bank_id'] ?? '';
        $selectedBank = null;

        foreach ($sellerBanks as $b) {
            if (($b['id'] ?? '') === $bankId) {
                $selectedBank = $b;
                break;
            }
        }

        if ($selectedBank) {
            $bankMsg = "Payment Details:\nBank: {$selectedBank['bank_name']}\nAccount No: {$selectedBank['acc_number']}\nAccount Name: {$selectedBank['acc_name']}";
            $sStmt = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message) VALUES (?, ?, ?, ?)");
            $sStmt->bind_param("isss", $listingId, $user['uid'], $partnerUid, $bankMsg);
            $sStmt->execute();
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Bank detail not found.']);
        }
        exit();
    }

    // 6. Seller Release Escrow Code
    if ($action === 'release_escrow') {
        if (!$isSeller) {
            echo json_encode(['status' => false, 'message' => 'Only the seller can release funds.']);
            exit();
        }

        $escrowId = intval($_POST['escrow_id'] ?? 0);
        
        $conn->begin_transaction();
        try {
            $pinCode = strtoupper(bin2hex(random_bytes(3)));

            $eStmt = $conn->prepare("SELECT * FROM p2p_escrows WHERE id = ? AND status = 'locked' FOR UPDATE");
            $eStmt->bind_param("i", $escrowId);
            $eStmt->execute();
            $escrow = $eStmt->get_result()->fetch_assoc();

            if (!$escrow) throw new Exception("Escrow not found or already processed.");

            $upE = $conn->prepare("UPDATE p2p_escrows SET status = 'released', deposit_pin = ? WHERE id = ?");
            $upE->bind_param("si", $pinCode, $escrowId);
            $upE->execute();

            $insDep = $conn->prepare("INSERT INTO deposits (uid, pin_code, amount, status) VALUES (?, ?, ?, 'pending')");
            $insDep->bind_param("ssd", $escrow['buyer_uid'], $pinCode, $escrow['amount']);
            $insDep->execute();

            $sysMsg = "Escrow Released! Deposit Voucher Code: " . $pinCode;
            $payload = json_encode(['pin_code' => $pinCode, 'amount' => $escrow['amount']]);
            $sysStmt = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message, type, payloads) VALUES (?, ?, ?, ?, 'escrow_released', ?)");
            $sysStmt->bind_param("issss", $listingId, $user['uid'], $partnerUid, $sysMsg, $payload);
            $sysStmt->execute();

            $conn->commit();
            echo json_encode(['status' => true, 'message' => 'Redemption code generated and shared!']);
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
    <title>MonieFlow - P2P Escrow Trade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f9fc; font-family: 'Plus Jakarta Sans', sans-serif; }
        .chat-box { height: 440px; overflow-y: auto; background: #ffffff; border-radius: 1rem; border: 1px solid rgba(0, 168, 232, 0.15); }
        .msg-bubble { max-width: 78%; border-radius: 1rem; padding: 0.75rem 1rem; margin-bottom: 0.6rem; font-size: 0.9rem; white-space: pre-line; }
        .msg-me { background-color: #00a8e8; color: #fff; align-self: flex-end; border-bottom-right-radius: 0.2rem; }
        .msg-partner { background-color: #f1f5f9; color: #1e293b; align-self: flex-start; border-bottom-left-radius: 0.2rem; }
        .msg-system { background-color: #e0f2fe; color: #0369a1; width: 100%; text-align: center; font-weight: 600; border-radius: 0.5rem; }
        .bot-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a href="/member/peer2peer.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-arrow-left me-1"></i> Back</a>
            <span class="fw-bold text-primary">P2P Escrow Trade</span>
            <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="collapse" data-bs-target="#tradeInfoPanel"><i class="bi bi-info-circle"></i></button>
        </div>
    </nav>

    <main class="container py-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                
                <!-- Trade Details Card -->
                <div class="collapse show mb-3" id="tradeInfoPanel">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Listing Details</small>
                                <strong class="text-dark">Unit Rate: ₦<?= number_format($listing['rate'], 2) ?> / FLOW</strong>
                            </div>
                            <div id="escrowBtnContainer"></div>
                        </div>
                    </div>
                </div>

                <!-- Bot Interactive Wizard Modal / Collapse for Buyer -->
                <?php if ($isBuyer): ?>
                <div class="card border-0 shadow-sm mb-3 bot-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-robot text-primary fs-4"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Trade Assistant</h6>
                                <small class="text-muted">Hello <?= htmlspecialchars($user['username']) ?>, ready to buy?</small>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm rounded-pill" id="startBotBtn" onclick="toggleBotFlow()"><i class="bi bi-cart-plus me-1"></i> Buy Flow</button>
                    </div>

                    <div id="botWorkflow" class="mt-3 d-none">
                        <hr class="my-2">
                        <label class="form-label small fw-bold">Enter Fiat Amount (₦):</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text">₦</span>
                            <input type="number" id="fiatInput" class="form-control" placeholder="0.00" oninput="calculateFlow()">
                        </div>
                        <div class="p-2 bg-white rounded border mb-3">
                            <small class="text-muted d-block">Calculated Receive Amount:</small>
                            <strong class="text-success fs-5" id="calculatedFlow">0.0000 FLOW</strong>
                        </div>
                        <button class="btn btn-success btn-sm w-100" id="confirmLockBtn" onclick="initiateEscrowOrder()"><i class="bi bi-lock-fill me-1"></i> Lock Funds in Escrow</button>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Bank Management Bar for Seller -->
                <?php if ($isSeller): ?>
                <div class="card border-0 shadow-sm mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold small"><i class="bi bi-bank me-1"></i> Bank Account Controls</span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addBankModal"><i class="bi bi-plus-circle"></i> Add Bank</button>
                            <?php if (!empty($sellerBanks)): ?>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#selectBankModal"><i class="bi bi-share"></i> Send Bank</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Chat Box Area -->
                <div class="chat-box p-3 d-flex flex-column" id="chatBox"></div>

                <!-- Chat Form -->
                <form id="chatForm" class="mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="msgInput" placeholder="Type a message..." required autocomplete="off">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-send-fill"></i></button>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <!-- Modal: Add Bank Account -->
    <?php if ($isSeller): ?>
    <div class="modal fade" id="addBankModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-6 fw-bold">Add Payment Bank Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small">Bank Name</label>
                        <input type="text" id="bankNameInput" class="form-control" placeholder="e.g. GTBank / Kuda">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Account Number</label>
                        <input type="text" id="accNumInput" class="form-control" placeholder="0123456789">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Account Holder Name</label>
                        <input type="text" id="accNameInput" class="form-control" placeholder="John Doe">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveBankAccount()">Save & Send to Chat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Select Bank Account -->
    <div class="modal fade" id="selectBankModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-6 fw-bold">Select Saved Bank Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <?php foreach ($sellerBanks as $b): ?>
                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="shareBank('<?= $b['id'] ?>')">
                                <div>
                                    <strong><?= htmlspecialchars($b['bank_name']) ?></strong><br>
                                    <small><?= htmlspecialchars($b['acc_number']) ?> - <?= htmlspecialchars($b['acc_name']) ?></small>
                                </div>
                                <i class="bi bi-send-check text-primary fs-5"></i>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const listingId = <?= $listingId ?>;
        const partnerUid = "<?= $partnerUid ?>";
        const unitRate = <?= floatval($listing['rate']) ?>;

        function toggleBotFlow() {
            document.getElementById('botWorkflow').classList.toggle('d-none');
        }

        function calculateFlow() {
            const fiat = floatval(document.getElementById('fiatInput').value || 0);
            const calculated = fiat > 0 ? (fiat / unitRate).toFixed(4) : "0.0000";
            document.getElementById('calculatedFlow').innerText = `${calculated} FLOW`;
        }

        async function loadChat() {
            const formData = new FormData();
            formData.append('action', 'fetch_messages');
            formData.append('listing_id', listingId);
            formData.append('partner', partnerUid);

            try {
                const res = await fetch('', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.status) {
                    const box = document.getElementById('chatBox');
                    box.innerHTML = '';

                    data.chats.forEach(c => {
                        const div = document.createElement('div');
                        if (c.type === 'system' || c.type === 'escrow_released') {
                            div.className = 'msg-bubble msg-system mx-auto p-2 my-1';
                            div.innerHTML = `<i class="bi bi-shield-check me-1"></i> ${c.message}`;
                        } else {
                            const isMe = c.sender_uid === data.my_uid;
                            div.className = `msg-bubble ${isMe ? 'msg-me ms-auto' : 'msg-partner me-auto'}`;
                            div.innerText = c.message;
                        }
                        box.appendChild(div);
                    });

                    // Manage Escrow Release Controls
                    const btnBox = document.getElementById('escrowBtnContainer');
                    if (data.escrow && data.escrow.status === 'locked') {
                        if (data.is_seller) {
                            btnBox.innerHTML = `<button onclick="releaseEscrow(${data.escrow.id})" class="btn btn-success btn-sm"><i class="bi bi-key me-1"></i> Release Voucher Code</button>`;
                        } else {
                            btnBox.innerHTML = `<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Payment Pending</span>`;
                        }
                    } else if (data.escrow && data.escrow.status === 'released') {
                        btnBox.innerHTML = `<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Code Released: ${data.escrow.deposit_pin}</span>`;
                    } else {
                        btnBox.innerHTML = `<span class="badge bg-secondary">No Active Escrow</span>`;
                    }
                }
            } catch (err) {}
        }

        document.getElementById('chatForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const msgInput = document.getElementById('msgInput');
            const formData = new FormData();
            formData.append('action', 'send_message');
            formData.append('listing_id', listingId);
            formData.append('partner', partnerUid);
            formData.append('message', msgInput.value);

            msgInput.value = '';
            await fetch('', { method: 'POST', body: formData });
            loadChat();
        });

        async function initiateEscrowOrder() {
            const fiatAmount = document.getElementById('fiatInput').value;
            if (!fiatAmount || fiatAmount <= 0) {
                alert('Please enter a valid fiat amount.');
                return;
            }

            if (!confirm(`Are you sure you want to buy ₦${fiatAmount} worth of FLOW?`)) return;

            const formData = new FormData();
            formData.append('action', 'initiate_escrow');
            formData.append('listing_id', listingId);
            formData.append('partner', partnerUid);
            formData.append('fiat_amount', fiatAmount);

            const res = await fetch('', { method: 'POST', body: formData });
            const data = await res.json();
            alert(data.message);
            if (data.status) {
                toggleBotFlow();
                loadChat();
            }
        }

        async function saveBankAccount() {
            const formData = new FormData();
            formData.append('action', 'save_bank_account');
            formData.append('bank_name', document.getElementById('bankNameInput').value);
            formData.append('acc_number', document.getElementById('accNumInput').value);
            formData.append('acc_name', document.getElementById('accNameInput').value);

            const res = await fetch('', { method: 'POST', body: formData });
            const data = await res.json();
            alert(data.message);
            if (data.status) {
                bootstrap.Modal.getInstance(document.getElementById('addBankModal')).hide();
                loadChat();
            }
        }

        async function shareBank(bankId) {
            const formData = new FormData();
            formData.append('action', 'share_bank');
            formData.append('bank_id', bankId);
            formData.append('listing_id', listingId);
            formData.append('partner', partnerUid);

            const res = await fetch('', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.status) {
                bootstrap.Modal.getInstance(document.getElementById('selectBankModal')).hide();
                loadChat();
            }
        }

        async function releaseEscrow(escrowId) {
            if (!confirm('Are you sure payment has been received? This will generate a redeemable code for the buyer.')) return;

            const formData = new FormData();
            formData.append('action', 'release_escrow');
            formData.append('escrow_id', escrowId);
            formData.append('listing_id', listingId);
            formData.append('partner', partnerUid);

            const res = await fetch('', { method: 'POST', body: formData });
            const data = await res.json();
            alert(data.message);
            loadChat();
        }

        setInterval(loadChat, 3000);
        loadChat();
    </script>
</body>
</html>