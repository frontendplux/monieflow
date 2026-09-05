<?php
session_start();
include __DIR__ . '/../conn.php';

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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

// Fetch Exchange Rate & Country Code from Session
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

// Fetch User Wallet
$walletStmt = $conn->prepare("SELECT public_id, account_number, balance FROM wallets WHERE uid = ?");
$walletStmt->bind_param("s", $user['uid']);
$walletStmt->execute();
$wallet = $walletStmt->get_result()->fetch_assoc();

$balanceMF = floatval($wallet['balance'] ?? 0.00);
$fiatEquivalent = $balanceMF * $mfExchangeRate;

// -----------------------------------------------------------------------------
// POST Handling: Instant Purchase / Buy Action
// -----------------------------------------------------------------------------
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'buy_item') {
    $postedToken = $_POST['csrf_token'] ?? '';
    $itemId = intval($_POST['item_id'] ?? 0);

    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $message = "Invalid security token.";
        $messageType = "danger";
    } else {
        $conn->begin_transaction();
        
        // Fetch item details using FOR UPDATE lock
        $itemStmt = $conn->prepare("
            SELECT ai.*, COALESCE(a.artifact_name, 'Standalone Relic') AS artifact_name 
            FROM artifact_items ai 
            LEFT JOIN artifacts a ON (ai.id = a.item_one_id OR ai.id = a.item_two_id) 
            WHERE ai.id = ? FOR UPDATE
        ");
        $itemStmt->bind_param("i", $itemId);
        $itemStmt->execute();
        $item = $itemStmt->get_result()->fetch_assoc();

        if (!$item) {
            $conn->rollback();
            $message = "Item not found.";
            $messageType = "danger";
        } elseif ($item['uid'] === $user['uid']) {
            $conn->rollback();
            $message = "You already own this item.";
            $messageType = "warning";
        } elseif (($item['status'] ?? 'available') !== 'available') {
            $conn->rollback();
            $message = "This item has already been purchased or linked.";
            $messageType = "warning";
        } else {
            $itemPrice = floatval($item['base_price'] ?? 0);

            if ($balanceMF < $itemPrice) {
                $conn->rollback();
                $message = "Insufficient balance to purchase this item.";
                $messageType = "danger";
            } else {
                // Deduct Balance from Buyer
                $deductStmt = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE uid = ?");
                $deductStmt->bind_param("ds", $itemPrice, $user['uid']);

                // Credit Balance to Seller (if not platform-owned)
                $creditStmt = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE uid = ?");
                $creditStmt->bind_param("ds", $itemPrice, $item['uid']);
                
                // Transfer Item Ownership and Mark as Sold
                $updateItemStmt = $conn->prepare("UPDATE artifact_items SET uid = ?, status = 'sold' WHERE id = ?");
                $updateItemStmt->bind_param("si", $user['uid'], $itemId);

                if ($deductStmt->execute() && $creditStmt->execute() && $updateItemStmt->execute()) {
                    $conn->commit();
                    $balanceMF -= $itemPrice; // Update local state balance
                    $fiatEquivalent = $balanceMF * $mfExchangeRate;
                    $message = "Item purchased successfully! You acquired: " . htmlspecialchars($item['title']);
                    $messageType = "success";
                } else {
                    $conn->rollback();
                    $message = "Transaction failed due to a database error.";
                    $messageType = "danger";
                }
            }
        }
    }
}

// -----------------------------------------------------------------------------
// Pagination & Items Query
// -----------------------------------------------------------------------------
$itemsPerPage = 8;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

// Total items count
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM artifact_items WHERE status = 'available' AND uid != ?");
$countStmt->bind_param("s", $user['uid']);
$countStmt->execute();
$totalItems = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch available items joining linked artifacts metadata if present
$itemsStmt = $conn->prepare("
    SELECT ai.*, 
           COALESCE(a.artifact_name, 'Standalone Artifact') AS artifact_name, 
           COALESCE(a.description, ai.description) AS artifact_description
    FROM artifact_items ai
    LEFT JOIN artifacts a ON (ai.id = a.item_one_id OR ai.id = a.item_two_id)
    WHERE ai.status = 'available' AND ai.uid != ?
    ORDER BY ai.id DESC
    LIMIT ? OFFSET ?
");
$itemsStmt->bind_param("sii", $user['uid'], $itemsPerPage, $offset);
$itemsStmt->execute();
$items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch User's Purchased Artifact Items
$myPurchasesStmt = $conn->prepare("
    SELECT ai.*, COALESCE(a.artifact_name, 'Standalone Relic') AS artifact_name 
    FROM artifact_items ai
    LEFT JOIN artifacts a ON (ai.id = a.item_one_id OR ai.id = a.item_two_id)
    WHERE ai.uid = ?
    ORDER BY ai.id DESC
    LIMIT 5
");
$myPurchasesStmt->bind_param("s", $user['uid']);
$myPurchasesStmt->execute();
$myPurchases = $myPurchasesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Artifact Item Marketplace</title>

    <link rel="icon" type="image/png" href="/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            width: 40px; height: 40px;
            background: #008cc3;
            border-radius: 50%;
            padding: 4px;
        }

        .balance-card {
            background: linear-gradient(135deg, #00a8e8 0%, #00719e 100%);
            color: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 168, 232, 0.25);
        }

        .artifact-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }

        .artifact-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 22px rgba(0, 168, 232, 0.12);
        }

        .activity-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.12);
            border-radius: 1rem;
            padding: 1rem;
        }

        .activity-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
            background-color: #e0f2fe; color: #0284c7;
        }

        .btn-skyblue {
            background-color: var(--brand-skyblue);
            color: #ffffff; font-weight: 600;
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

        .mobile-bottom-nav .nav-link.active,
        .mobile-bottom-nav .nav-link:hover {
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
                <div class="text-end d-none d-sm-block">
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Logged in as</small>
                    <span class="fw-semibold small"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <a href="/member/index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container py-4 px-3">

        <!-- Banner & Action Button -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1"><i class="bi bi-puzzle text-primary me-2"></i>Artifact Marketplace</h1>
                <p class="text-muted mb-0 small">
                    Buy individual artifact items, combine matching pieces, and unlock higher valuations.
                </p>
            </div>
            <div>
                <a href="/member/deposit.php" class="btn btn-skyblue rounded-3 px-4 py-2 w-100 w-md-auto">
                    <i class="bi bi-plus-lg me-1"></i> Top Up Balance
                </a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show rounded-3" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Balance Section -->
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="balance-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small opacity-75">Available Balance</span>
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <h2 class="display-6 fw-bold mb-1"><?= number_format($balanceMF, 2) ?> <span class="fs-5">MF</span></h2>
                    <small class="opacity-75 d-block">
                        ≈ <?= htmlspecialchars($currencyCode) ?> <?= number_format($fiatEquivalent, 2) ?>
                        <span class="badge bg-white text-dark ms-1" style="font-size: 0.65rem; opacity: 0.9;"><?= htmlspecialchars($userCountryCode) ?></span>
                    </small>
                </div>
            </div>
        </div>

        <!-- Artifact Items Grid -->
        <h5 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Available Artifact Pieces</h5>

        <div class="row g-3 mb-4">
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): 
                    $itemPrice = floatval($item['base_price'] ?? 0);
                ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="artifact-card p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <?= htmlspecialchars($item['category'] ?? 'Artifact') ?>
                                    </span>
                                    <span class="badge bg-light text-muted border">ID: #<?= (int)$item['id'] ?></span>
                                </div>

                                <h6 class="fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($item['title']) ?>
                                </h6>
                                <p class="text-primary small fw-semibold mb-2">
                                    <?= htmlspecialchars($item['artifact_name']) ?>
                                </p>
                                <p class="text-muted small mb-3 text-truncate">
                                    <?= htmlspecialchars($item['description'] ?? 'Collectible piece required to assemble full artifact set.') ?>
                                </p>

                                <div class="bg-light p-2 rounded-3 mb-3 border text-center">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                                    <span class="fw-bold fs-5 text-dark"><?= number_format($itemPrice, 2) ?> MF</span>
                                </div>
                            </div>

                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="buy_item">
                                <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                                
                                <button type="submit" class="btn btn-skyblue w-100 py-2 rounded-3" onclick="return confirm('Confirm purchase for <?= number_format($itemPrice, 2) ?> MF?');">
                                    <i class="bi bi-cart-check me-1"></i> Buy Piece
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="activity-card text-center py-5">
                        <i class="bi bi-box-seam fs-1 text-muted mb-2 d-block"></i>
                        <p class="text-muted mb-0">No individual artifact pieces are available right now. Please check back later.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Artifact Pagination" class="mb-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Previous</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">Next &raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

        <!-- Owned Artifact Items Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Your Artifact Collection</h5>
        </div>

        <div class="d-flex flex-column gap-3 mb-4">
            <?php if (!empty($myPurchases)): ?>
                <?php foreach ($myPurchases as $bought): ?>
                    <div class="activity-card d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="activity-icon">
                                <i class="bi bi-puzzle-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0"><?= htmlspecialchars($bought['title']) ?></h6>
                                <small class="text-muted d-block">Artifact Association: <?= htmlspecialchars($bought['artifact_name']) ?></small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i> Owned
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-card text-center py-4">
                    <i class="bi bi-collection fs-2 text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-0 small">You do not own any artifact items yet.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Mobile Bottom Navigation -->
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
                    <a href="/member/chart.php" class="nav-link">
                        <i class="bi bi-graph-up"></i>
                        <span>Chart</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/tasks.php" class="nav-link">
                        <i class="bi bi-check2-square"></i>
                        <span>Tasks</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/bidding.php" class="nav-link active">
                        <i class="bi bi-hammer"></i>
                        <span>Bidding</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/settings.php" class="nav-link">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>