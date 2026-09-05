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

// 2. Validate session user against database token
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
$stmt->bind_param("ss", $suid, $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// If user does not exist or token mismatch -> Force Logout
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: /index.php");
    exit();
}

// Handle Logout Action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: /index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Member Dashboard</title>
    
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
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
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

        .action-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 168, 232, 0.12);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            background-color: rgba(0, 168, 232, 0.1);
            color: var(--brand-skyblue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Activity Card Styling */
        .activity-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.12);
            border-radius: 1rem;
            padding: 1rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .activity-card:hover {
            border-color: rgba(0, 168, 232, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .activity-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .activity-icon.outgoing {
            background-color: #fee2e2;
            color: #ef4444;
        }

        .activity-icon.incoming {
            background-color: #d1fae5;
            color: #10b981;
        }

        .activity-icon.pending {
            background-color: #fef3c7;
            color: #f59e0b;
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
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="#">
                <img src="/logo.png" alt="MonieFlow">
                <span style="color: var(--brand-skyblue-hover);">MonieFlow</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Logged in as</small>
                    <span class="fw-semibold small"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <a href="?action=logout" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container py-4 px-3">

        <!-- Welcome Banner -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div class="text-center text-md-end">
                <h1 class="h3 fw-light mb-1">Welcome back, <?= htmlspecialchars($user['username']) ?>!</h1>
                <p class="text-muted mb-0">Public-ID: <span class="badge bg-light text-dark border">you  have no public id  </span> <br><span><a href="">Generate New</a> | <a href="">Copy public ID</a></span></p>
            </div> <br>
            <button class="btn btn-skyblue rounded-3 px-4 py-2">
                <i class="bi bi-plus-lg me-1"></i> New Transfer
            </button>
        </div>

        <!-- Account Balance Card & Quick Actions -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="balance-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="small opacity-75">Available Balance</span>
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <h2 class="display-6 fw-bold mb-3">$12,450.80</h2>
                    <div class="d-flex justify-content-between small opacity-75">
                        <span>Account: **** 4892</span>
                        <span>Active</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards -->
            <div class="col-12 col-md-6 col-lg-8">
                <div class="row g-3 h-100">
                    <div class="col-6 col-sm-3">
                        <div class="action-card p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="icon-box mb-2"><i class="bi bi-arrow-up-right-circle"></i></div>
                            <span class="fw-semibold small">Send</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="action-card p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="icon-box mb-2"><i class="bi bi-arrow-down-left-circle"></i></div>
                            <span class="fw-semibold small">Request</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="action-card p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="icon-box mb-2"><i class="bi bi-layers"></i></div>
                            <span class="fw-semibold small">Bulk Pay</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="action-card p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="icon-box mb-2"><i class="bi bi-gear"></i></div>
                            <span class="fw-semibold small">Settings</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Cards Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Recent Activity</h5>
            <a href="#" class="text-decoration-none small brand-logo" style="color: var(--brand-skyblue-hover);">View All</a>
        </div>

        <div class="d-flex flex-column gap-3">
            
            <!-- Activity Item 1 -->
            <div class="activity-card d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="activity-icon outgoing">
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-0">Alex Johnson</h6>
                        <small class="text-muted d-block">Transfer • Sep 02, 2026</small>
                    </div>
                </div>
                <div class="text-end">
                    <span class="fw-bold text-danger d-block">-$250.00</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Completed</span>
                </div>
            </div>

            <!-- Activity Item 2 -->
            <div class="activity-card d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="activity-icon incoming">
                        <i class="bi bi-arrow-down-left"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-0">MonieFlow Refund</h6>
                        <small class="text-muted d-block">Deposit • Sep 01, 2026</small>
                    </div>
                </div>
                <div class="text-end">
                    <span class="fw-bold text-success d-block">+$45.00</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Completed</span>
                </div>
            </div>

            <!-- Activity Item 3 -->
            <div class="activity-card d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="activity-icon pending">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-0">Vendor Batch #12</h6>
                        <small class="text-muted d-block">Multiple Transfer • Aug 29, 2026</small>
                    </div>
                </div>
                <div class="text-end">
                    <span class="fw-bold text-dark d-block">-$1,200.00</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.7rem;">Pending</span>
                </div>
            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


if user dont have account number, public id and private id generate new and it must never match with any other user in the database.
add footer nav menu to small screen and also the menu under the wallet balance  should be 4 on a row but its ok for bigscreen 
we have more fore big and small screen menu include deposit, transfer, peer2peer, history, settings, support