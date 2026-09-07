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
$query = "SELECT l.*, u.uid as owner_uid, u.username as owner_username 
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

// -----------------------------------------------------------------------------
// AJAX ACTIONS
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // 1. Fetch Chat Messages & Escrow State
    if ($action === 'fetch_messages') {
        $cStmt = $conn->prepare("SELECT * FROM p2p_chats WHERE listing_id = ? AND ((sender_uid = ? AND receiver_uid = ?) OR (sender_uid = ? AND receiver_uid = ?)) ORDER BY created_at ASC");
        $cStmt->bind_param("issss", $listingId, $user['uid'], $partnerUid, $partnerUid, $user['uid']);
        $cStmt->execute();
        $chats = $cStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fetch Escrow Status
        $eStmt = $conn->prepare("SELECT * FROM p2p_escrows WHERE listing_id = ? AND buyer_uid = ? AND seller_uid = ? ORDER BY id DESC LIMIT 1");
        $buyerUid = ($listing['type'] === 'sell') ? $user['uid'] : $partnerUid;
        $sellerUid = ($listing['type'] === 'sell') ? $partnerUid : $user['uid'];
        $eStmt->bind_param("iss", $listingId, $buyerUid, $sellerUid);
        $eStmt->execute();
        $escrow = $eStmt->get_result()->fetch_assoc();

        echo json_encode(['status' => true, 'chats' => $chats, 'escrow' => $escrow, 'my_uid' => $user['uid']]);
        exit();
    }

    // 2. Send Standard Message
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

    // 3. Seller Release Escrow and Generate Redeem Code
    if ($action === 'release_escrow') {
        $escrowId = intval($_POST['escrow_id'] ?? 0);
        
        $conn->begin_transaction();
        try {
            // Generate 6-digit alphanumeric code
            $pinCode = strtoupper(bin2hex(random_bytes(3)));

            // Fetch Escrow
            $eStmt = $conn->prepare("SELECT * FROM p2p_escrows WHERE id = ? AND status = 'locked'");
            $eStmt->bind_param("i", $escrowId);
            $eStmt->execute();
            $escrow = $eStmt->get_result()->fetch_assoc();

            if (!$escrow) throw new Exception("Escrow not found or already released.");

            // Update Escrow
            $upE = $conn->prepare("UPDATE p2p_escrows SET status = 'released', deposit_pin = ? WHERE id = ?");
            $upE->bind_param("si", $pinCode, $escrowId);
            $upE->execute();

            // Insert into deposits table for Buyer to redeem
            $insDep = $conn->prepare("INSERT INTO deposits (uid, pin_code, amount, status) VALUES (?, ?, ?, 'pending')");
            $insDep->bind_param("ssd", $escrow['buyer_uid'], $pinCode, $escrow['amount']);
            $insDep->execute();

            // System Message
            $sysMsg = "Escrow Released! Deposit Code: " . $pinCode;
            $payload = json_encode(['pin_code' => $pinCode, 'amount' => $escrow['amount']]);
            $sysStmt = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message, type, payloads) VALUES (?, ?, ?, ?, 'escrow_released', ?)");
            $sysStmt->bind_param("issss", $listingId, $user['uid'], $partnerUid, $sysMsg, $payload);
            $sysStmt->execute();

            $conn->commit();
            echo json_encode(['status' => true, 'message' => 'Redemption code generated and sent to chat!']);
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
    <title>MonieFlow - P2P Escrow Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f9fc; font-family: 'Plus Jakarta Sans', sans-serif; }
        .chat-box { height: 420px; overflow-y: auto; background: #ffffff; border-radius: 1rem; border: 1px solid rgba(0, 168, 232, 0.15); }
        .msg-bubble { max-width: 75%; border-radius: 1rem; padding: 0.75rem 1rem; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .msg-me { background-color: #00a8e8; color: #fff; align-self: flex-end; }
        .msg-partner { background-color: #f1f5f9; color: #1e293b; align-self: flex-start; }
        .msg-system { background-color: #d1fae5; color: #065f46; width: 100%; text-align: center; font-weight: 600; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a href="/member/peer2peer.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-arrow-left me-1"></i> Back to P2P</a>
            <span class="fw-bold text-primary">Escrow Chat</span>
        </div>
    </nav>

    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                
                <div class="card border-0 shadow-sm mb-3 p-3" id="escrowControlPanel">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="small text-muted d-block">Listing Details</span>
                            <strong class="text-dark">Rate: ₦<?= number_format($listing['rate'], 2) ?> | Amount: <?= number_format($listing['amount'], 2) ?> MF</strong>
                        </div>
                        <div id="escrowBtnContainer"></div>
                    </div>
                </div>

                <div class="chat-box p-3 d-flex flex-column" id="chatBox"></div>

                <form id="chatForm" class="mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="msgInput" placeholder="Type a message..." required autocomplete="off">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-send-fill"></i></button>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <script>
        const listingId = <?= $listingId ?>;
        const partnerUid = "<?= $partnerUid ?>";

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
                        if (c.type === 'escrow_released') {
                            div.className = 'msg-bubble msg-system mx-auto';
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
                        btnBox.innerHTML = `<button onclick="releaseEscrow(${data.escrow.id})" class="btn btn-success btn-sm"><i class="bi bi-key me-1"></i> Generate & Release Code</button>`;
                    } else if (data.escrow && data.escrow.status === 'released') {
                        btnBox.innerHTML = `<span class="badge bg-success">Code Released: ${data.escrow.deposit_pin}</span>`;
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

        async function releaseEscrow(escrowId) {
            if (!confirm('Are you sure payment has been received? This generates a redeemable code.')) return;

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
redesign the chat
create bot button 
hi user how are you doing 
how much do you want to buy 
user enter amount in accordance with seller country price 
amount auto calculated and converted to total amount of flow 
then if the user allowed then it register and remove the amount 
from seller wallet and place in the 
then the seller provide account number from users.payload['bank'] // arrays of bank seller can selet which one or create new one 
CREATE TABLE IF NOT EXISTS p2p_escrows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    buyer_uid VARCHAR(36) NOT NULL,
    seller_uid VARCHAR(36) NOT NULL,
    amount DECIMAL(36, 15) NOT NULL,
    deposit_pin VARCHAR(10) DEFAULT NULL,
    status ENUM('locked', 'released', 'cancelled') DEFAULT 'locked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_escrow_listing FOREIGN KEY (listing_id) REFERENCES p2p_listings(id) ON DELETE CASCADE,
    CONSTRAINT fk_escrow_buyer FOREIGN KEY (buyer_uid) REFERENCES users(uid) ON DELETE CASCADE,
    CONSTRAINT fk_escrow_seller FOREIGN KEY (seller_uid) REFERENCES users(uid) ON DELETE CASCADE
);