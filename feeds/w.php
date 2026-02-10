<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-red: #ff4d6d;
            --bg-light: #f7f8fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1a1a1a;
        }

        .settings-container {
            max-width: 500px;
            margin: 0 auto;
            padding-bottom: 50px;
        }

        .settings-group {
            background: #fff;
            border-radius: 20px;
            margin-bottom: 25px;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .settings-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s;
            border-bottom: 1px solid #f8f9fa;
        }

        .settings-item:last-child { border-bottom: none; }
        .settings-item:active { background: #f0f2f5; }

        .icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        /* Group Icon Colors */
        .bg-blue { background: #eef5ff; color: var(--mflow-blue); }
        .bg-gold { background: #fff9e6; color: #ffcc00; }
        .bg-purple { background: #f5eeff; color: #7b2ff7; }
        .bg-green { background: #e6f7ed; color: #198754; }
        .bg-red { background: #fff0f3; color: var(--mflow-red); }

        .settings-label { flex-grow: 1; font-weight: 500; font-size: 0.95rem; }
        .settings-meta { font-size: 0.85rem; color: #888; margin-right: 5px; }

        .group-title {
            padding-left: 20px;
            margin-bottom: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logout-btn {
            color: var(--mflow-red) !important;
            font-weight: 600;
        }
    </style>
</head>
<body>

<header class="bg-white p-3 border-bottom sticky-top">
    <div class="container d-flex align-items-center">
        <a href="/profile" class="text-dark me-3"><i class="ri-arrow-left-s-line fs-3"></i></a>
        <h5 class="mb-0 fw-bold">Settings</h5>
    </div>
</header>

<div class="container mt-4 settings-container">
    
    <div class="group-title">Account & Profile</div>
    <div class="settings-group shadow-sm">
        <a href="/edit-profile" class="settings-item">
            <div class="icon-box bg-blue"><i class="ri-user-settings-line"></i></div>
            <div class="settings-label">Edit Profile</div>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </a>
        <a href="/notifications" class="settings-item">
            <div class="icon-box bg-purple"><i class="ri-notification-3-line"></i></div>
            <div class="settings-label">Notifications</div>
            <span class="settings-meta">On</span>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </a>
    </div>

    <div class="group-title">Monetization</div>
    <div class="settings-group shadow-sm">
        <a href="/payout-settings" class="settings-item">
            <div class="icon-box bg-gold"><i class="ri-bank-card-line"></i></div>
            <div class="settings-label">Payout Methods</div>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </a>
        <a href="/subscription-tiers" class="settings-item">
            <div class="icon-box bg-green"><i class="ri-vip-crown-line"></i></div>
            <div class="settings-label">Subscription Tiers</div>
            <span class="settings-meta">2 Active</span>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </a>
    </div>

    <div class="group-title">Security</div>
    <div class="settings-group shadow-sm">
        <a href="/privacy" class="settings-item">
            <div class="icon-box bg-blue"><i class="ri-lock-2-line"></i></div>
            <div class="settings-label">Privacy Center</div>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </a>
        <a href="/security" class="settings-item">
            <div class="icon-box bg-red"><i class="ri-shield-keyhole-line"></i></div>
            <div class="settings-label">Two-Factor Auth</div>
            <span class="settings-meta text-danger">Disabled</span>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </a>
    </div>

    <div class="group-title">More</div>
    <div class="settings-group shadow-sm">
        <a href="/help" class="settings-item">
            <div class="icon-box bg-blue"><i class="ri-question-line"></i></div>
            <div class="settings-label">Help Center</div>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </a>
        <a href="/logout" class="settings-item logout-btn">
            <div class="icon-box bg-red"><i class="ri-logout-box-r-line"></i></div>
            <div class="settings-label">Log Out</div>
        </a>
    </div>

    <div class="text-center mt-4">
        <small class="text-muted">monieFlow v2.4.0 (2026)</small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>