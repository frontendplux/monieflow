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
$sellerPayload = json_decode($isSeller ? $user['payloads'] : ($partnerUser['payloads'] ?? '{}'), true);
$sellerBanks   = $sellerPayload['bank'] ?? [];

// -----------------------------------------------------------------------------
// Uploads dir for payment-proof screenshots (files only, not DB structure)
// -----------------------------------------------------------------------------
$proofUploadDir = __DIR__ . '/../uploads/p2p_proofs/';
if (!is_dir($proofUploadDir)) {
    @mkdir($proofUploadDir, 0755, true);
}
$proofPublicBase = '/uploads/p2p_proofs/';

// -----------------------------------------------------------------------------
// Escrow release gas fee: 2.5% total, split 1.2% (MF coin value) / 1.3% (company)
// -----------------------------------------------------------------------------
define('GAS_FEE_PCT', 0.025);
define('MF_VALUE_PCT', 0.012);
define('COMPANY_PROFIT_PCT', 0.013);

// Auto-provision the company profit ledger the same way p2p_chats.seen was
// added via ALTER ... IF NOT EXISTS — no manual migration step needed.
$conn->query("CREATE TABLE IF NOT EXISTS profit_mf_company (
    id INT AUTO_INCREMENT PRIMARY KEY,
    escrow_id INT NOT NULL,
    listing_id INT NOT NULL,
    amount DECIMAL(36, 15) NOT NULL,
    source VARCHAR(50) DEFAULT 'p2p_release_gas_fee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_profit_escrow FOREIGN KEY (escrow_id) REFERENCES p2p_escrows(id) ON DELETE CASCADE,
    CONSTRAINT fk_profit_listing FOREIGN KEY (listing_id) REFERENCES p2p_listings(id) ON DELETE CASCADE
)");

// -----------------------------------------------------------------------------
// Helper: mark incoming messages in this conversation as seen by current user
// -----------------------------------------------------------------------------
function markConversationSeen($conn, $listingId, $partnerUid, $myUid) {
    $s = $conn->prepare("UPDATE p2p_chats SET seen = 1 WHERE listing_id = ? AND sender_uid = ? AND receiver_uid = ? AND seen = 0");
    $s->bind_param("iss", $listingId, $partnerUid, $myUid);
    $s->execute();
}

// -----------------------------------------------------------------------------
// Helper: build the WhatsApp-style list of everyone this user is transacting
// with, across every listing, using only data already in p2p_chats.
// -----------------------------------------------------------------------------
function getConversationsForUser($conn, $myUid, $activeListingId = null, $activePartnerUid = null) {
    $convos = [];

    $q = "SELECT listing_id,
                 CASE WHEN sender_uid = ? THEN receiver_uid ELSE sender_uid END AS partner_uid,
                 MAX(created_at) AS last_time
          FROM p2p_chats
          WHERE sender_uid = ? OR receiver_uid = ?
          GROUP BY listing_id, partner_uid
          ORDER BY last_time DESC";
    $st = $conn->prepare($q);
    $st->bind_param("sss", $myUid, $myUid, $myUid);
    $st->execute();
    $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC);

    $seenPairs = [];

    foreach ($rows as $row) {
        $lid = (int) $row['listing_id'];
        $partner = $row['partner_uid'];
        $seenPairs[$lid . '|' . $partner] = true;

        // Last message preview
        $lm = $conn->prepare("SELECT message, type, created_at FROM p2p_chats 
                               WHERE listing_id = ? AND ((sender_uid = ? AND receiver_uid = ?) OR (sender_uid = ? AND receiver_uid = ?))
                               ORDER BY created_at DESC LIMIT 1");
        $lm->bind_param("issss", $lid, $myUid, $partner, $partner, $myUid);
        $lm->execute();
        $lastMsg = $lm->get_result()->fetch_assoc();

        // Unseen count from this partner on this listing
        $uc = $conn->prepare("SELECT COUNT(*) AS cnt FROM p2p_chats WHERE listing_id = ? AND sender_uid = ? AND receiver_uid = ? AND seen = 0");
        $uc->bind_param("iss", $lid, $partner, $myUid);
        $uc->execute();
        $unseenCount = (int) ($uc->get_result()->fetch_assoc()['cnt'] ?? 0);

        // Partner + listing display info
        $info = $conn->prepare("SELECT u.username, u.full_name, l.type AS listing_type, l.currency, l.rate, l.status AS listing_status
                                 FROM users u JOIN p2p_listings l ON l.id = ?
                                 WHERE u.uid = ?");
        $info->bind_param("is", $lid, $partner);
        $info->execute();
        $infoRow = $info->get_result()->fetch_assoc();

        if (!$infoRow) continue;

        $convos[] = [
            'listing_id' => $lid,
            'partner_uid' => $partner,
            'partner_username' => $infoRow['username'],
            'partner_full_name' => $infoRow['full_name'],
            'listing_type' => $infoRow['listing_type'],
            'currency' => $infoRow['currency'],
            'rate' => $infoRow['rate'],
            'last_message' => $lastMsg['message'] ?? null,
            'last_message_type' => $lastMsg['type'] ?? 'text',
            'last_time' => $lastMsg['created_at'] ?? $row['last_time'],
            'unseen_count' => $unseenCount,
            'is_active' => ($activeListingId !== null && $lid === (int) $activeListingId && $partner === $activePartnerUid),
        ];
    }

    // Make sure the conversation currently open on the page always appears,
    // even if no messages have been exchanged yet.
    if ($activeListingId && $activePartnerUid && !isset($seenPairs[$activeListingId . '|' . $activePartnerUid])) {
        $info = $conn->prepare("SELECT u.username, u.full_name, l.type AS listing_type, l.currency, l.rate, l.status AS listing_status
                                 FROM users u JOIN p2p_listings l ON l.id = ?
                                 WHERE u.uid = ?");
        $info->bind_param("is", $activeListingId, $activePartnerUid);
        $info->execute();
        $infoRow = $info->get_result()->fetch_assoc();

        if ($infoRow) {
            array_unshift($convos, [
                'listing_id' => (int) $activeListingId,
                'partner_uid' => $activePartnerUid,
                'partner_username' => $infoRow['username'],
                'partner_full_name' => $infoRow['full_name'],
                'listing_type' => $infoRow['listing_type'],
                'currency' => $infoRow['currency'],
                'rate' => $infoRow['rate'],
                'last_message' => null,
                'last_message_type' => 'text',
                'last_time' => date('Y-m-d H:i:s'),
                'unseen_count' => 0,
                'is_active' => true,
            ]);
        }
    }

    return $convos;
}

// -----------------------------------------------------------------------------
// AJAX ACTIONS
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // 1. Fetch Chat Messages, Active Escrow & Bank Info
    if ($action === 'fetch_messages') {
        // Mark anything the partner sent us as seen the moment we open/poll this thread
        markConversationSeen($conn, $listingId, $partnerUid, $user['uid']);

        $cStmt = $conn->prepare("SELECT * FROM p2p_chats WHERE listing_id = ? AND ((sender_uid = ? AND receiver_uid = ?) OR (sender_uid = ? AND receiver_uid = ?)) ORDER BY created_at ASC");
        $cStmt->bind_param("issss", $listingId, $user['uid'], $partnerUid, $partnerUid, $user['uid']);
        $cStmt->execute();
        $chats = $cStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fetch Escrow Status
        $eStmt = $conn->prepare("SELECT * FROM p2p_escrows WHERE listing_id = ? AND buyer_uid = ? AND seller_uid = ? ORDER BY id DESC LIMIT 1");
        $eStmt->bind_param("iss", $listingId, $buyerUid, $sellerUid);
        $eStmt->execute();
        $escrow = $eStmt->get_result()->fetch_assoc();

        // Has the buyer already submitted proof for the current locked escrow?
        $proofSubmitted = false;
        if ($escrow && $escrow['status'] === 'locked') {
            foreach ($chats as $c) {
                if (!empty($c['payloads'])) {
                    $p = json_decode($c['payloads'], true);
                    if (!empty($p['proof']) && strtotime($c['created_at']) >= strtotime($escrow['created_at'])) {
                        $proofSubmitted = true;
                    }
                }
            }
        }

        echo json_encode([
            'status' => true, 
            'chats' => $chats, 
            'escrow' => $escrow, 
            'my_uid' => $user['uid'],
            'is_seller' => $isSeller,
            'is_buyer' => $isBuyer,
            'proof_submitted' => $proofSubmitted,
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
            $sysMsg = "🔒 New Order Placed! Fiat: ₦" . number_format($fiatAmount, 2) . " | Crypto: " . number_format($flowAmount, 4) . " FLOW locked in escrow. Funds stay locked until the seller confirms payment and releases them manually — this protects both sides from scams.";
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

            $grossAmount   = floatval($escrow['amount']);
            $gasFee        = $grossAmount * GAS_FEE_PCT;        // 2.5% total
            $mfShare       = $grossAmount * MF_VALUE_PCT;       // 1.2% -> coin value
            $companyShare  = $grossAmount * COMPANY_PROFIT_PCT; // 1.3% -> company profit
            $netAmount     = $grossAmount - $gasFee;            // 97.5% -> buyer

            $upE = $conn->prepare("UPDATE p2p_escrows SET status = 'released', deposit_pin = ? WHERE id = ?");
            $upE->bind_param("si", $pinCode, $escrowId);
            $upE->execute();

            // Buyer's voucher is funded with the net amount, after the gas fee
            $insDep = $conn->prepare("INSERT INTO deposits (uid, pin_code, amount, status) VALUES (?, ?, ?, 'pending')");
            $insDep->bind_param("ssd", $escrow['buyer_uid'], $pinCode, $netAmount);
            $insDep->execute();

            // 1.2% appreciates the MF coin's own reference value
            $mfUpdate = $conn->prepare("UPDATE monieflow_coin_values SET amount = amount + ? WHERE currency_code = 'MF'");
            $mfUpdate->bind_param("d", $mfShare);
            $mfUpdate->execute();

            // 1.3% is logged to the company profit ledger
            $profitStmt = $conn->prepare("INSERT INTO profit_mf_company (escrow_id, listing_id, amount, source) VALUES (?, ?, ?, 'p2p_release_gas_fee')");
            $profitStmt->bind_param("iid", $escrowId, $listingId, $companyShare);
            $profitStmt->execute();

            $sysMsg = "Escrow Released!\nGross: " . number_format($grossAmount, 4) . " FLOW\nGas Fee (2.5%): " . number_format($gasFee, 4) . " FLOW\nYou receive: " . number_format($netAmount, 4) . " FLOW\nDeposit Voucher Code: " . $pinCode;
            $payload = json_encode([
                'pin_code' => $pinCode,
                'gross_amount' => $grossAmount,
                'gas_fee' => $gasFee,
                'mf_value_share' => $mfShare,
                'company_share' => $companyShare,
                'net_amount' => $netAmount,
            ]);
            $sysStmt = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message, type, payloads) VALUES (?, ?, ?, ?, 'escrow_released', ?)");
            $sysStmt->bind_param("issss", $listingId, $user['uid'], $partnerUid, $sysMsg, $payload);
            $sysStmt->execute();

            $conn->commit();
            echo json_encode(['status' => true, 'message' => 'Redemption code generated! Buyer receives ' . number_format($netAmount, 4) . ' FLOW after the 2.5% gas fee.']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // 7. Buyer submits payment proof (reference and/or screenshot) — bot nudges the seller
    if ($action === 'send_proof') {
        if (!$isBuyer) {
            echo json_encode(['status' => false, 'message' => 'Only the buyer can submit payment proof.']);
            exit();
        }

        // Must have a locked escrow open for this trade
        $eStmt = $conn->prepare("SELECT * FROM p2p_escrows WHERE listing_id = ? AND buyer_uid = ? AND seller_uid = ? AND status = 'locked' ORDER BY id DESC LIMIT 1");
        $eStmt->bind_param("iss", $listingId, $buyerUid, $sellerUid);
        $eStmt->execute();
        $escrow = $eStmt->get_result()->fetch_assoc();

        if (!$escrow) {
            echo json_encode(['status' => false, 'message' => 'No active locked escrow found for this trade.']);
            exit();
        }

        $reference = trim($_POST['reference'] ?? '');
        $imagePath = null;

        if (!empty($_FILES['proof_image']['name'])) {
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt)) {
                echo json_encode(['status' => false, 'message' => 'Only JPG, PNG or WEBP screenshots are allowed.']);
                exit();
            }

            if ($_FILES['proof_image']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['status' => false, 'message' => 'Image must be under 5MB.']);
                exit();
            }

            $fileName = 'proof_' . $listingId . '_' . uniqid() . '.' . $ext;
            $destPath = $proofUploadDir . $fileName;

            if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $destPath)) {
                $imagePath = $proofPublicBase . $fileName;
            } else {
                echo json_encode(['status' => false, 'message' => 'Failed to upload screenshot. Please try again.']);
                exit();
            }
        }

        if (empty($reference) && empty($imagePath)) {
            echo json_encode(['status' => false, 'message' => 'Add a transaction reference or a payment screenshot.']);
            exit();
        }

        $conn->begin_transaction();
        try {
            // The proof message itself (visible in chat, tagged via payloads so the bot/UI can find it)
            $proofMsgText = "📎 Payment proof submitted" . (!empty($reference) ? "\nReference: {$reference}" : "");
            $proofPayload = json_encode(['proof' => true, 'reference' => $reference, 'image' => $imagePath, 'escrow_id' => $escrow['id']]);

            $pStmt2 = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message, type, payloads) VALUES (?, ?, ?, ?, 'text', ?)");
            $pStmt2->bind_param("issss", $listingId, $user['uid'], $partnerUid, $proofMsgText, $proofPayload);
            $pStmt2->execute();

            // The bot's automatic nudge to the seller — notifies, does NOT auto-release funds.
            // Manual seller confirmation stays required so a faked reference/screenshot can't drain escrow.
            $botMsg = "🤖 Bot: Buyer has submitted payment proof for this order (₦" . number_format($escrow['amount'] * floatval($listing['rate']), 2) . "). "
                    . "Please check your bank account and only tap \"Release Voucher Code\" once you've confirmed the money has actually landed.";
            $botStmt = $conn->prepare("INSERT INTO p2p_chats (listing_id, sender_uid, receiver_uid, message, type) VALUES (?, ?, ?, ?, 'system')");
            $botStmt->bind_param("isss", $listingId, $user['uid'], $partnerUid, $botMsg);
            $botStmt->execute();

            $conn->commit();
            echo json_encode(['status' => true, 'message' => 'Proof sent to seller!']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => false, 'message' => 'Failed to submit proof. Please try again.']);
        }
        exit();
    }

    // 8. Fetch sidebar conversation list (polled for live updates, WhatsApp-style)
    if ($action === 'fetch_conversations') {
        $convos = getConversationsForUser($conn, $user['uid'], $listingId, $partnerUid);
        echo json_encode(['status' => true, 'conversations' => $convos]);
        exit();
    }
}

// Mark this conversation seen on normal (non-AJAX) page load too
markConversationSeen($conn, $listingId, $partnerUid, $user['uid']);

// Initial sidebar data for first paint
$initialConversations = getConversationsForUser($conn, $user['uid'], $listingId, $partnerUid);

// Fetch current escrow up front so the Release button renders immediately on
// page load instead of waiting for the first AJAX poll to populate it.
$curEStmt = $conn->prepare("SELECT * FROM p2p_escrows WHERE listing_id = ? AND buyer_uid = ? AND seller_uid = ? ORDER BY id DESC LIMIT 1");
$curEStmt->bind_param("iss", $listingId, $buyerUid, $sellerUid);
$curEStmt->execute();
$currentEscrow = $curEStmt->get_result()->fetch_assoc();
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
        .chat-box { height: 400px; overflow-y: auto; background: #ffffff; border-radius: 1rem; border: 1px solid rgba(0, 168, 232, 0.15); }
        .msg-bubble { max-width: 78%; border-radius: 1rem; padding: 0.75rem 1rem; margin-bottom: 0.6rem; font-size: 0.9rem; white-space: pre-line; }
        .msg-me { background-color: #00a8e8; color: #fff; align-self: flex-end; border-bottom-right-radius: 0.2rem; }
        .msg-partner { background-color: #f1f5f9; color: #1e293b; align-self: flex-start; border-bottom-left-radius: 0.2rem; }
        .msg-system { background-color: #e0f2fe; color: #0369a1; width: 100%; text-align: center; font-weight: 600; border-radius: 0.5rem; }
        .msg-proof-img { max-width: 220px; border-radius: 0.5rem; margin-top: 0.4rem; display: block; }
        .bot-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; }

        /* Conversations sidebar (WhatsApp-style) */
        .convo-list { max-height: 560px; overflow-y: auto; }
        .convo-item {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.65rem 0.75rem; border-radius: 0.75rem;
            text-decoration: none; color: inherit; margin-bottom: 0.35rem;
            border: 1px solid transparent;
        }
        .convo-item:hover { background: #f1f5f9; }
        .convo-item.active { background: #e6f6fd; border-color: rgba(0,168,232,0.35); }
        .convo-avatar {
            width: 42px; height: 42px; border-radius: 50%; background: #00a8e8;
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; flex-shrink: 0; font-size: 0.95rem;
        }
        .convo-meta { flex: 1; min-width: 0; }
        .convo-name { font-weight: 600; font-size: 0.88rem; color: #1e293b; }
        .convo-preview { font-size: 0.78rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
        .convo-unseen { background: #dc3545; color: #fff; border-radius: 999px; font-size: 0.7rem; min-width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; padding: 0 5px; flex-shrink: 0; }

        /* Locked-funds reassurance banner */
        .escrow-lock-banner {
            background: linear-gradient(135deg, #ecfdf5, #f0fdfa);
            border: 1px solid #10b98133; border-radius: 0.75rem;
            padding: 0.6rem 0.9rem; font-size: 0.82rem; color: #065f46;
        }

        /* Pulse to draw the seller's eye once proof has been submitted */
        @keyframes releasePulse {
            0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.55); }
            70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
            100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }
        .pulse-release { animation: releasePulse 1.6s infinite; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a href="/member/peer2peer.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-arrow-left me-1"></i> Back</a>
            <span class="fw-bold text-primary">P2P Escrow Trade</span>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-light border d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#convoOffcanvas">
                    <i class="bi bi-people"></i>
                </button>
                <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="collapse" data-bs-target="#tradeInfoPanel"><i class="bi bi-info-circle"></i></button>
            </div>
        </div>
    </nav>

    <main class="container py-3">
        <div class="row g-3">

            <!-- Desktop conversations sidebar -->
            <div class="col-lg-4 d-none d-lg-block">
                <div class="card border-0 shadow-sm p-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-1"></i> Your Trades</h6>
                    <div class="convo-list" id="convoListDesktop"></div>
                </div>
            </div>

            <!-- Mobile offcanvas conversations -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="convoOffcanvas">
                <div class="offcanvas-header">
                    <h6 class="offcanvas-title fw-bold"><i class="bi bi-people-fill text-primary me-1"></i> Your Trades</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="convo-list" id="convoListMobile"></div>
                </div>
            </div>

            <div class="col-lg-8">

                <!-- Trade Details Card -->
                <div class="collapse show mb-3" id="tradeInfoPanel">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <small class="text-muted d-block">Trade with <?= htmlspecialchars($partnerUser['username'] ?? 'Partner') ?></small>
                                <strong class="text-dark">Unit Rate: ₦<?= number_format($listing['rate'], 2) ?> / FLOW</strong>
                            </div>
                            <div id="escrowBtnContainer">
                                <?php if ($currentEscrow && $currentEscrow['status'] === 'locked'): ?>
                                    <?php if ($isSeller): ?>
                                        <button onclick="releaseEscrow(<?= (int) $currentEscrow['id'] ?>)" class="btn btn-success btn-sm"><i class="bi bi-key me-1"></i> Release Voucher Code</button>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Payment Pending</span>
                                    <?php endif; ?>
                                <?php elseif ($currentEscrow && $currentEscrow['status'] === 'released'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Code Released: <?= htmlspecialchars($currentEscrow['deposit_pin']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No Active Escrow</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anti-scam reassurance banner -->
                <div class="escrow-lock-banner mb-3">
                    <i class="bi bi-shield-lock-fill me-1"></i>
                    Funds are locked in escrow the moment an order is placed and can only be released by the seller, after they've personally confirmed payment. Nobody — including the bot — can bypass that manual confirmation.
                    <br><i class="bi bi-fuel-pump-fill me-1"></i>
                    A 2.5% gas fee applies when funds are released: the buyer receives 97.5%, 1.2% strengthens the MF coin's value, and 1.3% funds platform operations.
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

                    <!-- Payment proof trigger, shown once escrow is locked -->
                    <div id="proofPrompt" class="mt-3 d-none">
                        <hr class="my-2">
                        <button class="btn btn-outline-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#proofModal">
                            <i class="bi bi-receipt me-1"></i> I've Paid — Send Proof to Seller
                        </button>
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

    <!-- Modal: Buyer submits payment proof -->
    <?php if ($isBuyer): ?>
    <div class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-receipt me-1"></i> Send Payment Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Our bot will notify the seller instantly, but for your protection they must still confirm the payment landed before releasing funds.</p>
                    <div class="mb-2">
                        <label class="form-label small">Transaction Reference (optional)</label>
                        <input type="text" id="proofReferenceInput" class="form-control" placeholder="e.g. bank transfer reference / session ID">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Screenshot of Transfer (optional)</label>
                        <input type="file" id="proofImageInput" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    </div>
                    <div id="proofAlert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm" id="sendProofBtn" onclick="sendProof()"><i class="bi bi-send-check me-1"></i> Notify Seller</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const listingId = <?= $listingId ?>;
        const partnerUid = "<?= $partnerUid ?>";
        const myUid = "<?= $user['uid'] ?>";
        const unitRate = <?= floatval($listing['rate']) ?>;
        const initialConversations = <?= json_encode($initialConversations) ?>;

        function toggleBotFlow() {
            document.getElementById('botWorkflow').classList.toggle('d-none');
        }

        function calculateFlow() {
            const fiat = parseFloat(document.getElementById('fiatInput').value || 0);
            const calculated = fiat > 0 ? (fiat / unitRate).toFixed(4) : "0.0000";
            document.getElementById('calculatedFlow').innerText = `${calculated} FLOW`;
        }

        function timeAgo(dateStr) {
            const diffMs = Date.now() - new Date(dateStr.replace(' ', 'T')).getTime();
            const mins = Math.floor(diffMs / 60000);
            if (mins < 1) return 'now';
            if (mins < 60) return `${mins}m`;
            const hrs = Math.floor(mins / 60);
            if (hrs < 24) return `${hrs}h`;
            return `${Math.floor(hrs / 24)}d`;
        }

        function initials(name) {
            if (!name) return '?';
            return name.trim().split(/\s+/).slice(0, 2).map(w => w[0].toUpperCase()).join('');
        }

        function renderConversations(list) {
            const html = list.map(c => {
                const previewRaw = c.last_message_type === 'system' ? '🤖 System update' :
                                    (c.last_message || 'Say hello 👋');
                const preview = previewRaw.length > 42 ? previewRaw.slice(0, 42) + '…' : previewRaw;
                const activeClass = c.is_active ? 'active' : '';
                const badge = c.unseen_count > 0 ? `<span class="convo-unseen">${c.unseen_count}</span>` : '';
                const label = (c.listing_type === 'buy' ? 'Buying from' : 'Selling to');

                return `<a href="/member/chat.php?listing_id=${c.listing_id}&partner=${encodeURIComponent(c.partner_uid)}" class="convo-item ${activeClass}">
                            <div class="convo-avatar">${initials(c.partner_full_name || c.partner_username)}</div>
                            <div class="convo-meta">
                                <div class="d-flex justify-content-between">
                                    <span class="convo-name">${c.partner_full_name || c.partner_username}</span>
                                    <small class="text-muted">${timeAgo(c.last_time)}</small>
                                </div>
                                <div class="convo-preview">${label} · ${preview}</div>
                            </div>
                            ${badge}
                        </a>`;
            }).join('') || `<p class="text-muted small text-center py-4">No trades yet. Start a chat from the marketplace.</p>`;

            document.getElementById('convoListDesktop').innerHTML = html;
            document.getElementById('convoListMobile').innerHTML = html;
        }

        async function refreshConversations() {
            const formData = new FormData();
            formData.append('action', 'fetch_conversations');
            try {
                const res = await fetch('', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status) renderConversations(data.conversations);
            } catch (err) {}
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

                            let payload = null;
                            if (c.payloads) {
                                try { payload = JSON.parse(c.payloads); } catch (e) {}
                            }

                            const textNode = document.createElement('div');
                            textNode.innerText = c.message;
                            div.appendChild(textNode);

                            if (payload && payload.proof && payload.image) {
                                const img = document.createElement('img');
                                img.src = payload.image;
                                img.className = 'msg-proof-img';
                                img.alt = 'Payment proof screenshot';
                                div.appendChild(img);
                            }
                        }
                        box.appendChild(div);
                    });
                    box.scrollTop = box.scrollHeight;

                    // Manage Escrow Release Controls
                    const btnBox = document.getElementById('escrowBtnContainer');
                    if (data.escrow && data.escrow.status === 'locked') {
                        if (data.is_seller) {
                            const pulseClass = data.proof_submitted ? 'pulse-release' : '';
                            btnBox.innerHTML = `<button onclick="releaseEscrow(${data.escrow.id})" class="btn btn-success btn-sm ${pulseClass}"><i class="bi bi-key me-1"></i> Release Voucher Code</button>`;
                            if (data.proof_submitted) {
                                btnBox.innerHTML += `<div class="small text-success mt-1"><i class="bi bi-check-circle-fill me-1"></i>Buyer submitted payment proof</div>`;
                            }
                        } else {
                            btnBox.innerHTML = `<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Payment Pending</span>`;
                        }
                    } else if (data.escrow && data.escrow.status === 'released') {
                        btnBox.innerHTML = `<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Code Released: ${data.escrow.deposit_pin}</span>`;
                    } else {
                        btnBox.innerHTML = `<span class="badge bg-secondary">No Active Escrow</span>`;
                    }

                    // Show/hide the buyer's "I've paid" proof prompt
                    const proofPrompt = document.getElementById('proofPrompt');
                    if (proofPrompt) {
                        if (data.is_buyer && data.escrow && data.escrow.status === 'locked') {
                            proofPrompt.classList.remove('d-none');
                        } else {
                            proofPrompt.classList.add('d-none');
                        }
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
            if (!confirm('Are you sure payment has been received? A 2.5% gas fee is deducted on release (1.2% to MF coin value, 1.3% to platform), and the buyer gets a redeemable code for the remaining 97.5%.')) return;

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

        async function sendProof() {
            const reference = document.getElementById('proofReferenceInput').value.trim();
            const imageFile = document.getElementById('proofImageInput').files[0];
            const alertBox = document.getElementById('proofAlert');
            const btn = document.getElementById('sendProofBtn');

            if (!reference && !imageFile) {
                alertBox.innerHTML = `<div class="alert alert-danger py-2 small mt-2">Add a reference or a screenshot first.</div>`;
                return;
            }

            btn.disabled = true;

            const formData = new FormData();
            formData.append('action', 'send_proof');
            formData.append('listing_id', listingId);
            formData.append('partner', partnerUid);
            formData.append('reference', reference);
            if (imageFile) formData.append('proof_image', imageFile);

            try {
                const res = await fetch('', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.status) {
                    alertBox.innerHTML = `<div class="alert alert-success py-2 small mt-2">${data.message}</div>`;
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('proofModal')).hide();
                        loadChat();
                    }, 800);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger py-2 small mt-2">${data.message}</div>`;
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger py-2 small mt-2">An error occurred.</div>`;
            } finally {
                btn.disabled = false;
            }
        }

        // Initial paint from server-rendered data, then keep both in sync
        renderConversations(initialConversations);
        setInterval(refreshConversations, 8000);

        setInterval(loadChat, 3000);
        loadChat();
    </script>
</body>
</html>
provide a release btn and also create make sure there is loading indication when bank is been created or selected by 
seller or buyer for transaction 
