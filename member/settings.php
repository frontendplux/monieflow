<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Token Guard
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

// Fetch User Wallet Keys
$wStmt = $conn->prepare("SELECT public_id, private_key FROM wallets WHERE uid = ?");
$wStmt->bind_param("s", $user['uid']);
$wStmt->execute();
$wallet = $wStmt->get_result()->fetch_assoc();

// -----------------------------------------------------------------------------
// AJAX ENDPOINT: Profile & Key Updates
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    // 1. Update Profile (Full Name & Username)
    if ($action === 'update_profile') {
        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');

        if (empty($fullName) || empty($username)) {
            echo json_encode(['status' => false, 'message' => 'Full Name and Username are required.']);
            exit();
        }

        // Check if username is already taken by another user
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND uid != ?");
        $checkStmt->bind_param("ss", $username, $user['uid']);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            echo json_encode(['status' => false, 'message' => 'Username is already taken.']);
            exit();
        }

        $upStmt = $conn->prepare("UPDATE users SET full_name = ?, username = ? WHERE uid = ?");
        $upStmt->bind_param("sss", $fullName, $username, $user['uid']);

        if ($upStmt->execute()) {
            echo json_encode(['status' => true, 'message' => 'Profile updated successfully.']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to update profile.']);
        }
        exit();
    }

    // 2. Regenerate API Keys
    if ($action === 'regenerate_keys') {
        $newPublicId = 'mf_pub_' . bin2hex(random_bytes(16));
        $newPrivateKey = 'mf_priv_' . bin2hex(random_bytes(24));

        $regStmt = $conn->prepare("UPDATE wallets SET public_id = ?, private_key = ? WHERE uid = ?");
        $regStmt->bind_param("sss", $newPublicId, $newPrivateKey, $user['uid']);

        if ($regStmt->execute()) {
            echo json_encode([
                'status' => true, 
                'message' => 'New API Keys generated!',
                'public_id' => $newPublicId,
                'private_key' => $newPrivateKey
            ]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to generate new API keys.']);
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
    <title>MonieFlow - Account Settings</title>
    
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

        @media (min-width: 992px) { body { padding-bottom: 20px; } }

        .settings-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            padding: 1.5rem;
        }

        .nav-pills .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            text-align: left;
        }

        .nav-pills .nav-link.active {
            background-color: var(--brand-skyblue);
            color: #ffffff;
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

        code { color: #d63384; word-break: break-all; }
        pre { background: #1e293b; color: #f8fafc; padding: 1rem; border-radius: 0.5rem; }

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

    <main class="container py-4">
        <h1 class="h3 fw-bold mb-4">Account Settings</h1>

        <div class="row g-4">
            <div class="col-12 col-md-4 col-lg-3">
                <div class="settings-card p-2">
                    <div class="nav flex-column nav-pills" id="settingsTabs" role="tablist">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile-panel" type="button">
                            <i class="bi bi-person-gear me-2"></i> Profile Info
                        </button>
                        <button class="nav-link" id="api-tab" data-bs-toggle="pill" data-bs-target="#api-panel" type="button">
                            <i class="bi bi-key me-2"></i> API Keys
                        </button>
                        <button class="nav-link" id="docs-tab" data-bs-toggle="pill" data-bs-target="#docs-panel" type="button">
                            <i class="bi bi-file-earmark-code me-2"></i> API Documentation
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-8 col-lg-9">
                <div class="tab-content" id="settingsTabContent">
                    
                    <div class="tab-pane fade show active" id="profile-panel" role="tabpanel">
                        <div class="settings-card">
                            <h5 class="fw-bold mb-3">Edit Profile</h5>
                            <div id="profileAlert"></div>

                            <form id="profileForm">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Full Name</label>
                                    <input type="text" class="form-control" id="fullNameInput" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="usernameInput" value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Email Address (Read-only)</label>
                                    <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                </div>

                                <button type="submit" class="btn btn-skyblue rounded-3 px-4 py-2" id="saveProfileBtn">
                                    Save Changes
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="api-panel" role="tabpanel">
                        <div class="settings-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">API Keys</h5>
                                <button class="btn btn-outline-danger btn-sm" id="regenKeysBtn">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Regenerate Keys
                                </button>
                            </div>
                            <p class="text-muted small">Use these keys to authenticate server-to-server requests with MonieFlow API. Never share your Private Key.</p>
                            
                            <div id="apiAlert"></div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Public Key (Client ID)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control font-monospace" id="publicKeyField" value="<?= htmlspecialchars($wallet['public_id'] ?? 'Not Generated') ?>" readonly>
                                    <button class="btn btn-outline-secondary" onclick="copyText('publicKeyField')"><i class="bi bi-copy"></i></button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Private Key (Secret Key)</label>
                                <div class="input-group">
                                    <input type="password" class="form-control font-monospace" id="privateKeyField" value="<?= htmlspecialchars($wallet['private_key'] ?? 'Not Generated') ?>" readonly>
                                    <button class="btn btn-outline-secondary" onclick="toggleSecret()"><i class="bi bi-eye" id="eyeIcon"></i></button>
                                    <button class="btn btn-outline-secondary" onclick="copyText('privateKeyField')"><i class="bi bi-copy"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="docs-panel" role="tabpanel">
                        <div class="settings-card">
                            <h5 class="fw-bold mb-3">API Integration Documentation</h5>
                            <p class="text-muted small">MonieFlow API allows developers to programmatically inspect coin rates, generate wallet deposits, and execute coin transactions.</p>
                            
                            <hr class="my-4">

                            <h6 class="fw-bold text-primary">1. Authentication Header</h6>
                            <p class="small">All HTTP requests require your <code>public_id</code> and <code>private_key</code> passed in authorization headers:</p>
                            <pre><code>X-MonieFlow-Public-ID: YOUR_PUBLIC_KEY
X-MonieFlow-Private-Key: YOUR_PRIVATE_KEY</code></pre>

                            <h6 class="fw-bold text-primary mt-4">2. Example: Checking Wallet Balance (cURL)</h6>
                            <pre><code>curl -X GET "https://yourdomain.com/api/v1/balance" \
  -H "X-MonieFlow-Public-ID: <?= htmlspecialchars($wallet['public_id'] ?? 'mf_pub_xxx') ?>" \
  -H "X-MonieFlow-Private-Key: <?= htmlspecialchars($wallet['private_key'] ?? 'mf_priv_xxx') ?>"</code></pre>

                            <h6 class="fw-bold text-primary mt-4">3. Example Response</h6>
                            <pre><code>{
  "status": true,
  "data": {
    "account_number": "1234567890",
    "balance": 1500.00,
    "currency": "MF"
  }
}</code></pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col"><a href="/member/index.php" class="nav-link"><i class="bi bi-house-door"></i><span>Home</span></a></div>
                <div class="col"><a href="/member/deposit.php" class="nav-link"><i class="bi bi-arrow-down-circle"></i><span>Deposit</span></a></div>
                <div class="col"><a href="/member/peer2peer.php" class="nav-link"><i class="bi bi-people"></i><span>P2P</span></a></div>
                <div class="col"><a href="/member/settings.php" class="nav-link active"><i class="bi bi-gear"></i><span>Settings</span></a></div>
            </div>
        </div>
    </div>

    <script>
        // Copy to clipboard helper
        function copyText(elementId) {
            const field = document.getElementById(elementId);
            field.type = 'text';
            field.select();
            document.execCommand('copy');
            alert('Copied to clipboard!');
        }

        // Toggle Secret visibility
        function toggleSecret() {
            const field = document.getElementById('privateKeyField');
            const icon = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Save Profile AJAX
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const fullName = document.getElementById('fullNameInput').value;
            const username = document.getElementById('usernameInput').value;
            const alertBox = document.getElementById('profileAlert');

            const formData = new FormData();
            formData.append('action', 'update_profile');
            formData.append('full_name', fullName);
            formData.append('username', username);

            try {
                const res = await fetch('/member/settings.php', { method: 'POST', body: formData });
                const data = await res.json();
                alertBox.innerHTML = `<div class="alert alert-${data.status ? 'success' : 'danger'}">${data.message}</div>`;
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger">Failed to save profile updates.</div>`;
            }
        });

        // Regenerate API Keys AJAX
        document.getElementById('regenKeysBtn').addEventListener('click', async function() {
            if (!confirm('Are you sure? Any integration using your existing API keys will stop working.')) return;
            const alertBox = document.getElementById('apiAlert');

            const formData = new FormData();
            formData.append('action', 'regenerate_keys');

            try {
                const res = await fetch('/member/settings.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status) {
                    document.getElementById('publicKeyField').value = data.public_id;
                    document.getElementById('privateKeyField').value = data.private_key;
                    alertBox.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger">Failed to regenerate API keys.</div>`;
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>