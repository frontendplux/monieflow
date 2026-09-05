<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Token Guard
$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
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
    header("Location: /index.php");
    exit();
}

// Support WhatsApp Number & Pre-filled Message Configuration
$supportWhatsappNumber = "2348000000000"; // Replace with your support WhatsApp phone number (in international format without +)
$defaultMessage = urlencode("Hello MonieFlow Support, I need assistance with my account (@" . $user['username'] . ").");
$whatsappLink = "https://wa.me/" . $supportWhatsappNumber . "?text=" . $defaultMessage;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Support & Contact</title>
    
    <link rel="icon" type="image/png" href="/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-whitesmoke: #f4f9fc;
            --brand-skyblue: #00a8e8;
            --brand-skyblue-hover: #008cc3;
            --whatsapp-green: #25d366;
            --whatsapp-green-hover: #1da851;
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

        @media (min-width: 992px) { body { padding-bottom: 20px; } }

        .support-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .support-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 168, 232, 0.08);
        }

        .btn-whatsapp {
            background-color: var(--whatsapp-green);
            color: #ffffff;
            font-weight: 600;
        }

        .btn-whatsapp:hover {
            background-color: var(--whatsapp-green-hover);
            color: #ffffff;
        }

        .icon-box-lg {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #ffffff;
            border-top: 1px solid rgba(0, 168, 232, 0.15);
            z-index: 1030;
        }

        .mobile-bottom-nav .nav-link { color: var(--text-muted); font-size: 0.72rem; padding: 8px 0; text-align: center; }
        .mobile-bottom-nav .nav-link.active { color: var(--brand-skyblue); }
        .mobile-bottom-nav i { font-size: 1.25rem; display: block; }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/member/index.php">
                <img src="/logo.png" alt="MonieFlow" style="width: 40px; height: 40px; background: #008cc3; border-radius: 50%; padding: 4px;">
                <span style="color: var(--brand-skyblue-hover);">MonieFlow</span>
            </a>
            <a href="/member/index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        
        <div class="text-center mb-4">
            <h1 class="h3 fw-bold mb-1">Help & Support</h1>
            <p class="text-muted small mb-0">Have an issue with a trade, deposit, or transfer? We are here to help.</p>
        </div>

        <div class="row g-4 justify-content-center">
            
            <!-- WhatsApp Support Card -->
            <div class="col-12 col-md-6 col-lg-5">
                <div class="support-card text-center h-100 d-flex flex-column justify-content-between align-items-center">
                    <div>
                        <div class="icon-box-lg bg-success-subtle text-success mx-auto">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Instant WhatsApp Support</h5>
                        <p class="text-muted small mb-4">Chat directly with a live support agent on WhatsApp for instant assistance with your account.</p>
                    </div>

                    <a href="<?= $whatsappLink ?>" target="_blank" class="btn btn-whatsapp w-100 py-2.5 rounded-3">
                        <i class="bi bi-whatsapp me-2"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>

            <!-- Email Support Card -->
            <div class="col-12 col-md-6 col-lg-5">
                <div class="support-card text-center h-100 d-flex flex-column justify-content-between align-items-center">
                    <div>
                        <div class="icon-box-lg bg-primary-subtle text-primary mx-auto">
                            <i class="bi bi-envelope-at-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Email Support</h5>
                        <p class="text-muted small mb-4">Send us an email regarding technical inquiries, bugs, or API integration support.</p>
                    </div>

                    <a href="mailto:support@monieflow.com?subject=Support%20Request%20-%20<?= urlencode($user['username']) ?>" class="btn btn-outline-primary w-100 py-2.5 rounded-3 fw-semibold">
                        <i class="bi bi-envelope me-2"></i> Email Support
                    </a>
                </div>
            </div>

        </div>

    </main>

    <!-- Small Screen Bottom Navigation Bar -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col"><a href="/member/index.php" class="nav-link"><i class="bi bi-house-door"></i><span>Home</span></a></div>
                <div class="col"><a href="/member/deposit.php" class="nav-link"><i class="bi bi-arrow-down-circle"></i><span>Deposit</span></a></div>
                <div class="col"><a href="/member/peer2peer.php" class="nav-link"><i class="bi bi-people"></i><span>P2P</span></a></div>
                <div class="col"><a href="/member/support.php" class="nav-link active"><i class="bi bi-headset"></i><span>Support</span></a></div>
                <div class="col"><a href="/member/settings.php" class="nav-link"><i class="bi bi-gear"></i><span>Settings</span></a></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>