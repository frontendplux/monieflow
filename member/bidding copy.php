<?php
session_start();
include __DIR__ . '/../conn.php';

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Session Guard
$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
    header("Location: /index.php");
    exit();
}

// User Authentication Verification
$userStmt = $conn->prepare("SELECT uid, full_name, username FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
$userStmt->bind_param("ss", $suid, $token);
$userStmt->execute();
$currentUser = $userStmt->get_result()->fetch_assoc();

if (!$currentUser) {
    session_unset();
    session_destroy();
    header("Location: /index.php");
    exit();
}

// Fetch Exchange Rate (Defaults to NGN if missing)
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

$feedback = ['type' => '', 'message' => ''];

// -----------------------------------------------------------------------------
// POST Request Handlers
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $feedback = ['type' => 'error', 'message' => 'Invalid CSRF security token.'];
    } else {
        $action = $_POST['action'] ?? '';

        // Action 1: Upload a New Standalone Item
        if ($action === 'upload_item') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category = trim($_POST['category'] ?? 'Antique Relic');
            $basePrice = floatval($_POST['base_price'] ?? 0);
            $imageUrl = filter_var($_POST['image_url'] ?? '', FILTER_VALIDATE_URL) ? $_POST['image_url'] : null;

            if (!empty($title) && $basePrice > 0) {
                $stmt = $conn->prepare("INSERT INTO artifact_items (uid, title, description, category, image_url, base_price, status) VALUES (?, ?, ?, ?, ?, ?, 'available')");
                $stmt->bind_param("sssssd", $currentUser['uid'], $title, $description, $category, $imageUrl, $basePrice);
                if ($stmt->execute()) {
                    $feedback = ['type' => 'success', 'message' => 'Antique item listed successfully!'];
                } else {
                    $feedback = ['type' => 'error', 'message' => 'Failed to list antique item.'];
                }
            } else {
                $feedback = ['type' => 'error', 'message' => 'Please complete all required fields.'];
            }
        }

        // Action 2: Link Two Items into an Artifact
        elseif ($action === 'link_items') {
            $itemOneId = intval($_POST['item_one_id'] ?? 0);
            $itemTwoId = intval($_POST['item_two_id'] ?? 0);
            $artifactName = trim($_POST['artifact_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $multiplier = floatval($_POST['value_multiplier'] ?? 10.0);

            if ($itemOneId !== $itemTwoId && !empty($artifactName)) {
                $conn->begin_transaction();
                try {
                    $checkStmt = $conn->prepare("SELECT id, base_price FROM artifact_items WHERE id IN (?, ?) AND status = 'available' FOR UPDATE");
                    $checkStmt->bind_param("ii", $itemOneId, $itemTwoId);
                    $checkStmt->execute();
                    $items = $checkStmt->get_result()->fetch_all(MYSQLI_ASSOC);

                    if (count($items) === 2) {
                        $combinedBase = floatval($items[0]['base_price']) + floatval($items[1]['base_price']);
                        $estimatedValue = $combinedBase * $multiplier;

                        $artStmt = $conn->prepare("INSERT INTO artifacts (item_one_id, item_two_id, artifact_name, description, estimated_value) VALUES (?, ?, ?, ?, ?)");
                        $artStmt->bind_param("iissd", $itemOneId, $itemTwoId, $artifactName, $description, $estimatedValue);
                        $artStmt->execute();

                        $updateStmt = $conn->prepare("UPDATE artifact_items SET status = 'linked' WHERE id IN (?, ?)");
                        $updateStmt->bind_param("ii", $itemOneId, $itemTwoId);
                        $updateStmt->execute();

                        $conn->commit();
                        $feedback = ['type' => 'success', 'message' => 'Artifact synthesized and market valuation unlocked!'];
                    } else {
                        $conn->rollback();
                        $feedback = ['type' => 'error', 'message' => 'Both items must be active and available to synthesize.'];
                    }
                } catch (Exception $e) {
                    $conn->rollback();
                    $feedback = ['type' => 'error', 'message' => 'Synthesization failed: ' . $e->getMessage()];
                }
            }
        }

        // Action 3: Create Bidding Auction
        elseif ($action === 'create_auction') {
            $targetType = $_POST['target_type'] ?? '';
            $targetId = intval($_POST['target_id'] ?? 0);
            $startingBid = floatval($_POST['starting_bid'] ?? 0);
            $durationHours = intval($_POST['duration_hours'] ?? 24);

            if ($targetId > 0 && $startingBid > 0) {
                $expiresAt = date('Y-m-d H:i:s', strtotime("+{$durationHours} hours"));
                $itemId = ($targetType === 'item') ? $targetId : null;
                $artifactId = ($targetType === 'artifact') ? $targetId : null;

                $auctionStmt = $conn->prepare("INSERT INTO bounty_auctions (seller_uid, artifact_id, item_id, starting_bid, current_bid, status, expires_at) VALUES (?, ?, ?, ?, ?, 'active', ?)");
                $auctionStmt->bind_param("siidds", $currentUser['uid'], $artifactId, $itemId, $startingBid, $startingBid, $expiresAt);
                
                if ($auctionStmt->execute()) {
                    $feedback = ['type' => 'success', 'message' => 'Antique listed for live bidding!'];
                } else {
                    $feedback = ['type' => 'error', 'message' => 'Failed to initialize bidding auction.'];
                }
            }
        }

        // Action 4: Place a Bid
        elseif ($action === 'place_bid') {
            $auctionId = intval($_POST['auction_id'] ?? 0);
            $bidAmount = floatval($_POST['bid_amount'] ?? 0);

            $conn->begin_transaction();
            try {
                $aucStmt = $conn->prepare("SELECT id, current_bid, seller_uid, status, expires_at FROM bounty_auctions WHERE id = ? AND status = 'active' FOR UPDATE");
                $aucStmt->bind_param("i", $auctionId);
                $aucStmt->execute();
                $auction = $aucStmt->get_result()->fetch_assoc();

                if (!$auction) {
                    throw new Exception("Auction is inactive or has closed.");
                }

                if (strtotime($auction['expires_at']) <= time()) {
                    throw new Exception("Bidding for this antique has expired.");
                }

                if ($auction['seller_uid'] === $currentUser['uid']) {
                    throw new Exception("You cannot place bids on your own listings.");
                }

                if ($bidAmount <= floatval($auction['current_bid'])) {
                    throw new Exception("Your bid must exceed the current highest bid.");
                }

                $balStmt = $conn->prepare("SELECT balance FROM wallets WHERE uid = ? FOR UPDATE");
                $balStmt->bind_param("s", $currentUser['uid']);
                $balStmt->execute();
                $wallet = $balStmt->get_result()->fetch_assoc();

                if (!$wallet || floatval($wallet['balance']) < $bidAmount) {
                    throw new Exception("Insufficient wallet balance to place this bid.");
                }

                $bidStmt = $conn->prepare("INSERT INTO auction_bids (auction_id, bidder_uid, bid_amount) VALUES (?, ?, ?)");
                $bidStmt->bind_param("isd", $auctionId, $currentUser['uid'], $bidAmount);
                $bidStmt->execute();

                $upAucStmt = $conn->prepare("UPDATE bounty_auctions SET current_bid = ? WHERE id = ?");
                $upAucStmt->bind_param("di", $bidAmount, $auctionId);
                $upAucStmt->execute();

                $conn->commit();
                $feedback = ['type' => 'success', 'message' => 'Your bid has been recorded!'];
            } catch (Exception $e) {
                $conn->rollback();
                $feedback = ['type' => 'error', 'message' => $e->getMessage()];
            }
        }
    }
}

// -----------------------------------------------------------------------------
// Data Fetching
// -----------------------------------------------------------------------------

// Fetch Active Marketplace Auctions
$auctionsQuery = "
    SELECT ba.*, 
           ai.title AS item_title, ai.description AS item_desc, ai.image_url AS item_image, ai.category,
           a.artifact_name, a.description AS artifact_desc, a.estimated_value,
           u.username AS seller_name
    FROM bounty_auctions ba
    JOIN users u ON ba.seller_uid = u.uid
    LEFT JOIN artifact_items ai ON ba.item_id = ai.id
    LEFT JOIN artifacts a ON ba.artifact_id = a.id
    WHERE ba.status = 'active' AND ba.expires_at > NOW()
    ORDER BY ba.created_at DESC
";
$auctions = $conn->query($auctionsQuery)->fetch_all(MYSQLI_ASSOC);

// User's Available Items
$myItemsStmt = $conn->prepare("SELECT id, title, base_price FROM artifact_items WHERE uid = ? AND status = 'available'");
$myItemsStmt->bind_param("s", $currentUser['uid']);
$myItemsStmt->execute();
$userAvailableItems = $myItemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// All Available Items for synthesization
$allItemsStmt = $conn->query("SELECT id, title, base_price FROM artifact_items WHERE status = 'available'");
$allAvailableItems = $allItemsStmt->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antique Marketplace & Bidding</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: #1e293b; border-radius: 8px; padding: 20px; margin-bottom: 24px; border: 1px solid #334155; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        .btn { background: #2563eb; color: #fff; border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; font-weight: 600; width: 100%; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 6px; margin-bottom: 12px; background: #0f172a; border: 1px solid #334155; color: #fff; border-radius: 4px; box-sizing: border-box; }
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 16px; }
        .alert-error { background: #991b1b; color: #fecaca; }
        .alert-success { background: #166534; color: #bbf7d0; }
        
        /* Marketplace & Item Display Styling */
        .marketplace-card { display: flex; flex-direction: column; justify: space-between; overflow: hidden; }
        .img-container { width: 100%; height: 200px; background: #0f172a; border-radius: 6px; overflow: hidden; margin-bottom: 12px; border: 1px solid #334155; display: flex; align-items: center; justify-content: center; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; }
        .img-placeholder { color: #64748b; font-size: 0.9rem; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge-artifact { background: #8b5cf6; color: #fff; }
        .badge-item { background: #0284c7; color: #fff; }
        .price-box { background: #0f172a; padding: 12px; border-radius: 6px; border: 1px solid #334155; margin: 12px 0; }
        .fiat-conversion { font-size: 0.85rem; color: #94a3b8; margin-top: 2px; }
        .seller-tag { font-size: 0.85rem; color: #94a3b8; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Antique Marketplace & Bidding</h2>

    <?php if (!empty($feedback['message'])): ?>
        <div class="alert alert-<?= $feedback['type'] ?>"><?= htmlspecialchars($feedback['message']) ?></div>
    <?php endif; ?>

    <!-- 1. Upload Antique Item Form -->
    <div class="card">
        <h3>List Antique Item</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="upload_item">
            <label>Antique Title</label>
            <input type="text" name="title" required placeholder="e.g. Ancient Bronze Coin">
            <label>Category</label>
            <select name="category">
                <option value="Antique Relic">Antique Relic</option>
                <option value="Rare Coin">Rare Coin</option>
                <option value="Sculpture & Art">Sculpture & Art</option>
                <option value="Historical Document">Historical Document</option>
            </select>
            <label>Description</label>
            <textarea name="description" rows="2" placeholder="Provide background history or details..."></textarea>
            <label>Base Price (MF Coins)</label>
            <input type="number" step="0.0000000001" name="base_price" required>
            <label>Image URL</label>
            <input type="url" name="image_url" placeholder="https://example.com/image.jpg">
            <button type="submit" class="btn">Add to Marketplace</button>
        </form>
    </div>

    <!-- 2. Synthesize Two Items into High-Value Artifact -->
    <div class="card">
        <h3>Synthesize Artifact Pair</h3>
        <p>Combine two distinct items to unlock a high-value artifact entry.</p>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="link_items">
            <label>Primary Antique (Your Items)</label>
            <select name="item_one_id" required>
                <option value="">Select Item 1</option>
                <?php foreach ($userAvailableItems as $item): ?>
                    <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?> (<?= $item['base_price'] ?> MF)</option>
                <?php endforeach; ?>
            </select>
            <label>Secondary Antique (Global Pool)</label>
            <select name="item_two_id" required>
                <option value="">Select Item 2</option>
                <?php foreach ($allAvailableItems as $item): ?>
                    <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?> (<?= $item['base_price'] ?> MF)</option>
                <?php endforeach; ?>
            </select>
            <label>Synthesized Artifact Name</label>
            <input type="text" name="artifact_name" required placeholder="e.g. Crown of Dynasty">
            <label>Description</label>
            <textarea name="description" rows="2"></textarea>
            <label>Valuation Multiplier</label>
            <input type="number" step="0.1" name="value_multiplier" value="10.0" min="1.5" required>
            <button type="submit" class="btn">Synthesize Artifact</button>
        </form>
    </div>

    <!-- 3. Put Items/Artifacts up for Auction -->
    <div class="card">
        <h3>Start Bidding Auction</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="create_auction">
            <label>Listing Type</label>
            <select name="target_type" required>
                <option value="item">Single Antique Item</option>
                <option value="artifact">Synthesized Artifact</option>
            </select>
            <label>Target ID</label>
            <input type="number" name="target_id" placeholder="Enter Item or Artifact ID" required>
            <label>Starting Bid (MF Coins)</label>
            <input type="number" step="0.0000000001" name="starting_bid" required>
            <label>Duration (Hours)</label>
            <input type="number" name="duration_hours" value="24" min="1" required>
            <button type="submit" class="btn">Launch Auction</button>
        </form>
    </div>

    <!-- 4. Marketplace Auction Grid -->
    <h3>Live Antique Auctions</h3>
    <div class="grid">
        <?php if (empty($auctions)): ?>
            <div class="card" style="grid-column: 1 / -1;">
                <p style="color: #94a3b8; text-align: center;">No live auctions currently available.</p>
            </div>
        <?php else: ?>
            <?php foreach ($auctions as $auc): ?>
                <?php 
                    $title = $auc['artifact_name'] ?? $auc['item_title'];
                    $description = $auc['artifact_desc'] ?? $auc['item_desc'];
                    $imageUrl = $auc['item_image'] ?? null;
                    $isArtifact = !empty($auc['artifact_id']);
                    $currentBidMF = floatval($auc['current_bid']);
                    $currentBidFiat = $currentBidMF * $mfExchangeRate;
                ?>
                <div class="card marketplace-card">
                    <div>
                        <div class="img-container">
                            <?php if ($imageUrl): ?>
                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($title) ?>">
                            <?php else: ?>
                                <span class="img-placeholder">No Preview Available</span>
                            <?php endif; ?>
                        </div>

                        <div style="margin-bottom: 8px;">
                            <span class="badge <?= $isArtifact ? 'badge-artifact' : 'badge-item' ?>">
                                <?= $isArtifact ? 'Artifact' : htmlspecialchars($auc['category'] ?? 'Antique') ?>
                            </span>
                        </div>

                        <h4 style="margin: 0 0 4px 0;"><?= htmlspecialchars($title) ?></h4>
                        <div class="seller-tag">Seller: @<?= htmlspecialchars($auc['seller_name']) ?></div>
                        
                        <?php if ($description): ?>
                            <p style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 12px;"><?= htmlspecialchars(mb_strimwidth($description, 0, 90, "...")) ?></p>
                        <?php endif; ?>

                        <div class="price-box">
                            <div style="font-size: 0.8rem; color: #94a3b8;">Current Bid</div>
                            <div style="font-size: 1.25rem; font-weight: bold; color: #38bdf8;">
                                <?= number_format($currentBidMF, 4) ?> MF
                            </div>
                            <div class="fiat-conversion">
                                ≈ <?= number_format($currentBidFiat, 2) ?> <?= htmlspecialchars($currencyCode) ?>
                            </div>
                        </div>

                        <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 12px;">
                            Closes: <?= date('M d, Y H:i', strtotime($auc['expires_at'])) ?>
                        </div>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="place_bid">
                        <input type="hidden" name="auction_id" value="<?= $auc['id'] ?>">
                        <input type="number" step="0.0000000001" name="bid_amount" placeholder="Higher bid (MF)" required>
                        <button type="submit" class="btn">Place Bid</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>