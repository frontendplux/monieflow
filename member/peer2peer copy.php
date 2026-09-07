<?php
session_start();
include __DIR__ . '/../conn.php';

// -----------------------------------------------------------------------------
// 1. Session & Token Authentication Guard
// -----------------------------------------------------------------------------
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

// Fetch Logged-in User
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
$stmt->bind_param("ss", $suid, $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
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

// Fetch User Wallet Balance
$wStmt = $conn->prepare("SELECT balance FROM wallets WHERE uid = ?");
$wStmt->bind_param("s", $user['uid']);
$wStmt->execute();
$userWallet = $wStmt->get_result()->fetch_assoc();
$userBalance = floatval($userWallet['balance'] ?? 0);

// Fetch ALL Currencies & System Exchange Rates from monieflow_coin_values
$ratesResult = $conn->query("SELECT country_code, country, currency_code, amount FROM monieflow_coin_values ORDER BY currency_code ASC");
$currencies = $ratesResult->fetch_all(MYSQLI_ASSOC);

// -----------------------------------------------------------------------------
// 2. IP Geolocation for Default Selected Local Currency
// -----------------------------------------------------------------------------
function getUserCountryCode() {
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $json = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
        if ($json) {
            $data = json_decode($json, true);
            if (!empty($data['countryCode'])) {
                return $data['countryCode'];
            }
        }
    }
    return 'NG';
}

$userCountryCode = getUserCountryCode();
$detectedCurrency = 'NGN';
foreach ($currencies as $c) {
    if ($c['country_code'] === $userCountryCode) {
        $detectedCurrency = $c['currency_code'];
        break;
    }
}

// Active Filter from URL or default to All Currencies
$selectedCurrency = $_GET['currency'] ?? 'ALL';
$filter = $_GET['filter'] ?? 'all';

// -----------------------------------------------------------------------------
// 3. AJAX ENDPOINTS: Create, Edit & Cancel P2P Listing
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // --- CREATE NEW LISTING ---
    if ($action === 'create_listing') {
        $type = trim($_POST['type'] ?? '');
        $currency = trim($_POST['currency'] ?? '');
        $rate = floatval($_POST['rate'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);

        if (!in_array($type, ['buy', 'sell'])) {
            echo json_encode(['status' => false, 'message' => 'Invalid listing type.']);
            exit();
        }

        if ($rate <= 0 || $amount <= 0) {
            echo json_encode(['status' => false, 'message' => 'Rate and Amount must be greater than zero.']);
            exit();
        }

        $checkStmt = $conn->prepare("SELECT id FROM p2p_listings WHERE uid = ? AND type = ? AND status = 'active'");
        $checkStmt->bind_param("ss", $user['uid'], $type);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            echo json_encode(['status' => false, 'message' => "You already have an active {$type} offer. Please edit or cancel it before creating a new one."]);
            exit();
        }

        if ($type === 'sell' && $amount > $userBalance) {
            echo json_encode(['status' => false, 'message' => 'Insufficient wallet balance. Available: ' . number_format($userBalance, 2) . ' MF']);
            exit();
        }

        $insertStmt = $conn->prepare("INSERT INTO p2p_listings (uid, type, rate, currency, amount) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssdd", $user['uid'], $type, $rate, $currency, $amount);

        if ($insertStmt->execute()) {
            echo json_encode(['status' => true, 'message' => 'P2P Offer posted successfully!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to post offer. Please try again.']);
        }
        exit();
    }

    // --- EDIT EXISTING LISTING ---
    if ($action === 'edit_listing') {
        $id = intval($_POST['id'] ?? 0);
        $currency = trim($_POST['currency'] ?? '');
        $rate = floatval($_POST['rate'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);

        if ($id <= 0 || $rate <= 0 || $amount <= 0) {
            echo json_encode(['status' => false, 'message' => 'Invalid inputs provided.']);
            exit();
        }

        // ESCROW CHECK: Cannot edit if there is an active locked escrow
        $escrowCheck = $conn->prepare("SELECT id FROM p2p_escrows WHERE listing_id = ? AND status = 'locked'");
        $escrowCheck->bind_param("i", $id);
        $escrowCheck->execute();
        if ($escrowCheck->get_result()->num_rows > 0) {
            echo json_encode(['status' => false, 'message' => 'Cannot edit offer: You have an active locked escrow trade on this listing.']);
            exit();
        }

        $existStmt = $conn->prepare("SELECT type FROM p2p_listings WHERE id = ? AND uid = ? AND status = 'active'");
        $existStmt->bind_param("is", $id, $user['uid']);
        $existStmt->execute();
        $listing = $existStmt->get_result()->fetch_assoc();

        if (!$listing) {
            echo json_encode(['status' => false, 'message' => 'Active listing not found or unauthorized.']);
            exit();
        }

        if ($listing['type'] === 'sell' && $amount > $userBalance) {
            echo json_encode(['status' => false, 'message' => 'Insufficient wallet balance. Available: ' . number_format($userBalance, 2) . ' MF']);
            exit();
        }

        $updateStmt = $conn->prepare("UPDATE p2p_listings SET rate = ?, currency = ?, amount = ? WHERE id = ? AND uid = ?");
        $updateStmt->bind_param("dsdis", $rate, $currency, $amount, $id, $user['uid']);

        if ($updateStmt->execute()) {
            echo json_encode(['status' => true, 'message' => 'Offer updated successfully!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to update offer.']);
        }
        exit();
    }

    // --- CANCEL / DELETE LISTING ---
    if ($action === 'cancel_listing') {
        $id = intval($_POST['id'] ?? 0);

        // ESCROW CHECK: Cannot delete/cancel if an active locked escrow exists
        $escrowCheck = $conn->prepare("SELECT id FROM p2p_escrows WHERE listing_id = ? AND status = 'locked'");
        $escrowCheck->bind_param("i", $id);
        $escrowCheck->execute();
        if ($escrowCheck->get_result()->num_rows > 0) {
            echo json_encode(['status' => false, 'message' => 'Cannot delete offer: There is currently an active locked trade in escrow for this offer.']);
            exit();
        }

        $cancelStmt = $conn->prepare("UPDATE p2p_listings SET status = 'cancelled' WHERE id = ? AND uid = ? AND status = 'active'");
        $cancelStmt->bind_param("is", $id, $user['uid']);

        if ($cancelStmt->execute() && $cancelStmt->affected_rows > 0) {
            echo json_encode(['status' => true, 'message' => 'Offer cancelled successfully!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to cancel offer or listing is no longer active.']);
        }
        exit();
    }
}

// -----------------------------------------------------------------------------
// 4. Fetch User's Own Active Offers
// -----------------------------------------------------------------------------
$myStmt = $conn->prepare("SELECT * FROM p2p_listings WHERE uid = ? AND status = 'active'");
$myStmt->bind_param("s", $user['uid']);
$myStmt->execute();
$myListings = $myStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$hasActiveBuy = false;
$hasActiveSell = false;
foreach ($myListings as $ml) {
    if ($ml['type'] === 'buy') $hasActiveBuy = true;
    if ($ml['type'] === 'sell') $hasActiveSell = true;
}

// -----------------------------------------------------------------------------
// 5. Fetch Active Listings across ALL Currencies (or filtered)
// -----------------------------------------------------------------------------
$whereClauses = ["l.status = 'active'"];

if ($selectedCurrency !== 'ALL') {
    $whereClauses[] = "l.currency = '" . $conn->real_escape_string($selectedCurrency) . "'";
}

if ($filter === 'buy' || $filter === 'sell') {
    $whereClauses[] = "l.type = '" . $conn->real_escape_string($filter) . "'";
}

$whereSQL = implode(" AND ", $whereClauses);
$query = "SELECT l.*, u.username, u.full_name, u.phone 
          FROM p2p_listings l 
          JOIN users u ON l.uid = u.uid 
          WHERE {$whereSQL} 
          ORDER BY l.created_at DESC";

$listingsResult = $conn->query($query);
$listings = $listingsResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Peer2Peer Market</title>
    
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
            width: 40px; height: 40px;
            background: #008cc3; border-radius: 50%; padding: 4px;
        }

        .p2p-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem; padding: 1.25rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .p2p-card:hover {
            border-color: var(--brand-skyblue);
            box-shadow: 0 4px 15px rgba(0, 168, 232, 0.08);
        }

        .btn-skyblue {
            background-color: var(--brand-skyblue); color: #ffffff; font-weight: 600;
        }

        .btn-skyblue:hover {
            background-color: var(--brand-skyblue-hover); color: #ffffff;
        }

        .mobile-bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #ffffff; border-top: 1px solid rgba(0, 168, 232, 0.15);
            z-index: 1030; box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }

        .mobile-bottom-nav .nav-link {
            color: var(--text-muted); font-size: 0.72rem; padding: 8px 0; text-align: center;
        }

        .mobile-bottom-nav .nav-link.active { color: var(--brand-skyblue); }
        .mobile-bottom-nav i { font-size: 1.25rem; display: block; margin-bottom: 2px; }
    </style>
</head>
<body>

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

    <main class="container py-4 px-3">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Peer2Peer Marketplace</h1>
                <p class="text-muted small mb-0">Buy & Sell MonieFlow coins directly across multiple currencies.</p>
            </div>
            <button class="btn btn-skyblue rounded-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#createListingModal">
                <i class="bi bi-plus-lg me-1"></i> Post New Offer
            </button>
        </div>

        <!-- My Active Offers -->
        <?php if (!empty($myListings)): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-1"></i> Your Active Listings</h6>
                    <div class="row g-3">
                        <?php foreach ($myListings as $my): ?>
                            <div class="col-12 col-md-6">
                                <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-<?= $my['type'] === 'buy' ? 'success' : 'danger' ?> mb-1">
                                            <?= strtoupper($my['type']) ?> OFFER
                                        </span>
                                        <div class="fw-bold small">1 MF = <?= htmlspecialchars($my['currency']) ?> <?= number_format($my['rate'], 2) ?></div>
                                        <div class="text-muted small">Amount: <?= number_format($my['amount'], 2) ?> MF</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle" onclick='openEditModal(<?= json_encode($my) ?>)' title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="cancelListing(<?= $my['id'] ?>)" title="Cancel/Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Filters & Currency Selection -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 border-bottom pb-3">
            <div class="d-flex gap-2">
                <a href="?filter=all&currency=<?= urlencode($selectedCurrency) ?>" class="btn btn-sm <?= $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">All Offers</a>
                <a href="?filter=buy&currency=<?= urlencode($selectedCurrency) ?>" class="btn btn-sm <?= $filter === 'buy' ? 'btn-success' : 'btn-outline-success' ?> rounded-pill px-3">Buying Coins</a>
                <a href="?filter=sell&currency=<?= urlencode($selectedCurrency) ?>" class="btn btn-sm <?= $filter === 'sell' ? 'btn-danger' : 'btn-outline-danger' ?> rounded-pill px-3">Selling Coins</a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <label class="small fw-semibold text-muted mb-0"><i class="bi bi-funnel me-1"></i> Currency:</label>
                <select class="form-select form-select-sm rounded-pill" style="width: auto;" onchange="location = this.value;">
                    <option value="?filter=<?= $filter ?>&currency=ALL" <?= $selectedCurrency === 'ALL' ? 'selected' : '' ?>>All Currencies</option>
                    <?php foreach ($currencies as $c): ?>
                        <option value="?filter=<?= $filter ?>&currency=<?= urlencode($c['currency_code']) ?>" <?= $selectedCurrency === $c['currency_code'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['currency_code']) ?> (<?= htmlspecialchars($c['country']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Listings Grid -->
        <div class="row g-3">
            <?php if (!empty($listings)): ?>
                <?php foreach ($listings as $item): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="p2p-card h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-<?= $item['type'] === 'buy' ? 'success' : 'danger' ?>-subtle text-<?= $item['type'] === 'buy' ? 'success' : 'danger' ?> border border-<?= $item['type'] === 'buy' ? 'success' : 'danger' ?>-subtle px-3 py-1 rounded-pill fw-bold text-uppercase">
                                        I WANT TO <?= strtoupper($item['type']) ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?= date('M d, H:i', strtotime($item['created_at'])) ?></small>
                                </div>

                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['full_name']) ?></h6>
                                <p class="text-muted small mb-3">@<?= htmlspecialchars($item['username']) ?></p>

                                <div class="bg-light p-3 rounded-3 mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">Rate:</span>
                                        <strong class="text-dark">1 MF = <?= htmlspecialchars($item['currency']) ?> <?= number_format($item['rate'], 2) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Available Amount:</span>
                                        <strong class="text-primary"><?= number_format($item['amount'], 2) ?> MF</strong>
                                    </div>
                                </div>
                            </div>

                            <?php if ($item['uid'] === $user['uid']): ?>
                                <button class="btn btn-secondary btn-sm w-100 rounded-pill py-2" disabled>
                                    <i class="bi bi-person-check me-1"></i> Your Listing
                                </button>
                            <?php else: ?>
                                <a href="/member/chat.php?listing_id=<?= $item['id'] ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill py-2">
                                    <i class="bi bi-chat-dots me-1"></i> Start Escrow Chat
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-people fs-1 text-muted mb-2 d-block"></i>
                    <p class="text-muted">No active P2P offers found for the selected filters.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Create Listing Modal -->
    <div class="modal fade" id="createListingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Post New P2P Offer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalAlert"></div>

                    <form id="createListingForm">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Offer Type</label>
                            <select class="form-select" id="listingType" required>
                                <option value="buy" <?= $hasActiveBuy ? 'disabled' : '' ?>>I want to BUY MonieFlow Coins <?= $hasActiveBuy ? '(Active offer exists)' : '' ?></option>
                                <option value="sell" <?= $hasActiveSell ? 'disabled' : '' ?>>I want to SELL MonieFlow Coins <?= $hasActiveSell ? '(Active offer exists)' : '' ?></option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Currency</label>
                            <select class="form-select" id="listingCurrency" required>
                                <?php foreach ($currencies as $c): ?>
                                    <option value="<?= htmlspecialchars($c['currency_code']) ?>" <?= $c['currency_code'] === $detectedCurrency ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['currency_code']) ?> - <?= htmlspecialchars($c['country']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Rate per 1 MF</label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="listingRate" placeholder="e.g. 1.00" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Amount (MF Coins)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="listingAmount" placeholder="e.g. 500" required>
                            <div class="form-text d-flex justify-content-between mt-1">
                                <span>Wallet Balance: <strong><?= number_format($userBalance, 2) ?> MF</strong></span>
                                <span class="text-primary fw-bold" id="minSellNotice" style="display:none;">Max Sell: <?= number_format($userBalance, 2) ?> MF</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-skyblue w-100 py-2 rounded-3" id="submitListingBtn">
                            <span id="listingSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                            Post Offer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Listing Modal -->
    <div class="modal fade" id="editListingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Edit P2P Offer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editModalAlert"></div>

                    <form id="editListingForm">
                        <input type="hidden" id="editListingId">
                        <input type="hidden" id="editListingType">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Currency</label>
                            <select class="form-select" id="editListingCurrency" required>
                                <?php foreach ($currencies as $c): ?>
                                    <option value="<?= htmlspecialchars($c['currency_code']) ?>">
                                        <?= htmlspecialchars($c['currency_code']) ?> - <?= htmlspecialchars($c['country']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Rate per 1 MF</label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="editListingRate" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Amount (MF Coins)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="editListingAmount" required>
                            <div class="form-text mt-1">
                                Wallet Balance: <strong><?= number_format($userBalance, 2) ?> MF</strong>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-skyblue w-100 py-2 rounded-3" id="editSubmitBtn">
                            <span id="editSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const userBalance = <?= $userBalance ?>;

        document.getElementById('listingType').addEventListener('change', function() {
            const isSell = this.value === 'sell';
            const amountInput = document.getElementById('listingAmount');
            const minNotice = document.getElementById('minSellNotice');

            if (isSell) {
                amountInput.max = userBalance;
                minNotice.style.display = 'inline';
            } else {
                amountInput.removeAttribute('max');
                minNotice.style.display = 'none';
            }
        });

        document.getElementById('createListingForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const type = document.getElementById('listingType').value;
            const currency = document.getElementById('listingCurrency').value;
            const rate = document.getElementById('listingRate').value;
            const amount = parseFloat(document.getElementById('listingAmount').value);
            const submitBtn = document.getElementById('submitListingBtn');
            const spinner = document.getElementById('listingSpinner');
            const alertContainer = document.getElementById('modalAlert');

            if (type === 'sell' && amount > userBalance) {
                alertContainer.innerHTML = `<div class="alert alert-danger">You cannot sell more than your balance (${userBalance.toFixed(2)} MF).</div>`;
                return;
            }

            submitBtn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData();
            formData.append('action', 'create_listing');
            formData.append('type', type);
            formData.append('currency', currency);
            formData.append('rate', rate);
            formData.append('amount', amount);

            try {
                const response = await fetch('/member/peer2peer.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status) {
                    alertContainer.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alertContainer.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    submitBtn.disabled = false;
                }
            } catch (err) {
                alertContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
                submitBtn.disabled = false;
            } finally {
                spinner.classList.add('d-none');
            }
        });

        function openEditModal(listing) {
            document.getElementById('editListingId').value = listing.id;
            document.getElementById('editListingType').value = listing.type;
            document.getElementById('editListingCurrency').value = listing.currency;
            document.getElementById('editListingRate').value = listing.rate;
            
            const amountInput = document.getElementById('editListingAmount');
            amountInput.value = listing.amount;
            if (listing.type === 'sell') {
                amountInput.max = userBalance;
            } else {
                amountInput.removeAttribute('max');
            }

            const modal = new bootstrap.Modal(document.getElementById('editListingModal'));
            modal.show();
        }

        document.getElementById('editListingForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const id = document.getElementById('editListingId').value;
            const type = document.getElementById('editListingType').value;
            const currency = document.getElementById('editListingCurrency').value;
            const rate = document.getElementById('editListingRate').value;
            const amount = parseFloat(document.getElementById('editListingAmount').value);
            const submitBtn = document.getElementById('editSubmitBtn');
            const spinner = document.getElementById('editSpinner');
            const alertContainer = document.getElementById('editModalAlert');

            if (type === 'sell' && amount > userBalance) {
                alertContainer.innerHTML = `<div class="alert alert-danger">You cannot sell more than your balance (${userBalance.toFixed(2)} MF).</div>`;
                return;
            }

            submitBtn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData();
            formData.append('action', 'edit_listing');
            formData.append('id', id);
            formData.append('currency', currency);
            formData.append('rate', rate);
            formData.append('amount', amount);

            try {
                const response = await fetch('/member/peer2peer.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status) {
                    alertContainer.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alertContainer.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    submitBtn.disabled = false;
                }
            } catch (err) {
                alertContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
                submitBtn.disabled = false;
            } finally {
                spinner.classList.add('d-none');
            }
        });

        async function cancelListing(id) {
            if (!confirm("Are you sure you want to cancel/delete this active offer?")) return;

            const formData = new FormData();
            formData.append('action', 'cancel_listing');
            formData.append('id', id);

            try {
                const response = await fetch('/member/peer2peer.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert("An error occurred while deleting the offer.");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
make sure to count unseen message from client 
CREATE TABLE IF NOT EXISTS p2p_chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    sender_uid VARCHAR(36) NOT NULL,
    receiver_uid VARCHAR(36) NOT NULL,
    message TEXT DEFAULT NULL,
    type ENUM('text', 'escrow_init', 'escrow_released', 'system') DEFAULT 'text',
    payloads JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_chat_listing FOREIGN KEY (listing_id) REFERENCES p2p_listings(id) ON DELETE CASCADE,
    CONSTRAINT fk_chat_sender FOREIGN KEY (sender_uid) REFERENCES users(uid) ON DELETE CASCADE,
    CONSTRAINT fk_chat_receiver FOREIGN KEY (receiver_uid) REFERENCES users(uid) ON DELETE CASCADE
);
ALTER TABLE p2p_chats
ADD COLUMN IF NOT EXISTS seen tinyint DEFAULT 0;