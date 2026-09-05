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

// -----------------------------------------------------------------------------
// AJAX ENDPOINT: Create New P2P Listing
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_listing') {
    header('Content-Type: application/json');

    $type = trim($_POST['type'] ?? '');
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

    // Check balance if user wants to sell
    if ($type === 'sell' && $amount > $userBalance) {
        echo json_encode(['status' => false, 'message' => 'Insufficient balance. Your available balance is ' . number_format($userBalance, 2) . ' MF']);
        exit();
    }

    // Insert listing into database
    $insertStmt = $conn->prepare("INSERT INTO p2p_listings (uid, type, rate, amount) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssdd", $user['uid'], $type, $rate, $amount);

    if ($insertStmt->execute()) {
        echo json_encode(['status' => true, 'message' => 'P2P Offer posted successfully!']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to post offer. Please try again.']);
    }
    exit();
}

// Fetch Active Listings with User Info
$filter = $_GET['filter'] ?? 'all';
$query = "SELECT l.*, u.username, u.full_name, u.phone 
          FROM p2p_listings l 
          JOIN users u ON l.uid = u.uid 
          WHERE l.status = 'active'";

if ($filter === 'buy' || $filter === 'sell') {
    $query .= " AND l.type = '" . $conn->real_escape_string($filter) . "'";
}

$query .= " ORDER BY l.created_at DESC";
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
            width: 40px;
            height: 40px;
            background: #008cc3;
            border-radius: 50%;
            padding: 4px;
        }

        .p2p-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            padding: 1.25rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .p2p-card:hover {
            border-color: var(--brand-skyblue);
            box-shadow: 0 4px 15px rgba(0, 168, 232, 0.08);
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

        <!-- Top Action & Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Peer2Peer Marketplace</h1>
                <p class="text-muted small mb-0">Buy & Sell MonieFlow coins directly with verified users.</p>
            </div>
            <button class="btn btn-skyblue rounded-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#createListingModal">
                <i class="bi bi-plus-lg me-1"></i> Post New Offer
            </button>
        </div>

        <!-- Filter Tabs -->
        <div class="d-flex gap-2 mb-4 border-bottom pb-2">
            <a href="?filter=all" class="btn btn-sm <?= $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">All Offers</a>
            <a href="?filter=buy" class="btn btn-sm <?= $filter === 'buy' ? 'btn-success' : 'btn-outline-success' ?> rounded-pill px-3">Buying Coins</a>
            <a href="?filter=sell" class="btn btn-sm <?= $filter === 'sell' ? 'btn-danger' : 'btn-outline-danger' ?> rounded-pill px-3">Selling Coins</a>
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
                                        <strong class="text-dark">1 MF = ₦<?= number_format($item['rate'], 2) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Available Amount:</span>
                                        <strong class="text-primary"><?= number_format($item['amount'], 2) ?> MF</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Escrow Chat Direct Link -->
                            <a href="/member/chat.php?listing_id=<?= $item['id'] ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill py-2">
                                <i class="bi bi-chat-dots me-1"></i> Start Escrow Chat
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-people fs-1 text-muted mb-2 d-block"></i>
                    <p class="text-muted">No active P2P offers found for this filter.</p>
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
                                <option value="buy">I want to BUY MonieFlow Coins</option>
                                <option value="sell">I want to SELL MonieFlow Coins</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Rate per 1 MF (₦)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="listingRate" placeholder="e.g. 1.00" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Amount (MF Coins)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="listingAmount" placeholder="e.g. 500" required>
                            <div class="form-text d-flex justify-content-between mt-1" id="balanceNotice">
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

    <!-- Small Screen Bottom Navigation Bar -->
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
                    <a href="/member/transfer.php" class="nav-link">
                        <i class="bi bi-arrow-up-right-circle"></i>
                        <span>Transfer</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/peer2peer.php" class="nav-link active">
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

    <!-- Script -->
    <script>
        const userBalance = <?= $userBalance ?>;

        // Toggle Balance/Minimum Rules Dynamic UI
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
            const rate = document.getElementById('listingRate').value;
            const amount = parseFloat(document.getElementById('listingAmount').value);
            const submitBtn = document.getElementById('submitListingBtn');
            const spinner = document.getElementById('listingSpinner');
            const alertContainer = document.getElementById('modalAlert');

            if (type === 'sell' && amount > userBalance) {
                alertContainer.innerHTML = `<div class="alert alert-danger">You cannot sell more than your available balance (${userBalance.toFixed(2)} MF).</div>`;
                return;
            }

            submitBtn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData();
            formData.append('action', 'create_listing');
            formData.append('type', type);
            formData.append('rate', rate);
            formData.append('amount', amount);

            try {
                const response = await fetch('/member/peer2peer.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status) {
                    alertContainer.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => location.reload(), 1200);
                } else {
                    alertContainer.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    submitBtn.disabled = false;
                }
            } catch (err) {
                alertContainer.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
                submitBtn.disabled = false;
            } finally {
                spinner.classList.add('d-none');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>