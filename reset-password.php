<!DOCTYPE html>
<html lang="en">
<head>
    <?php
session_start();
header('Content-Type: application/json');
require __DIR__ . "/conn/conn.php";

$token = $_GET['token'] ?? '';

if (!$token) {
    echo json_encode(["status" => false, "message" => "Missing token"]);
    exit;
}

// Look up user by token stored in profile JSON
$stmt = $conn->prepare("SELECT id, profile FROM users WHERE JSON_UNQUOTE(JSON_EXTRACT(profile, '$.reset_token')) = ? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["status" => false, "message" => "Invalid or expired token"]);
    exit;
}

$user = $res->fetch_assoc();
$profile = json_decode($user['profile'], true);

// Validate expiry
if (empty($profile['reset_expires']) || strtotime($profile['reset_expires']) < time()) {
    echo json_encode(["status" => false, "message" => "Token expired"]);
    exit;
}

// At this point, token is valid and not expired.
// You can now show a form to let the user set a new password.
// Example: handle POST with new password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['password'] ?? '';
    if (strlen($newPass) < 6) {
        echo json_encode(["status" => false, "message" => "Password too short"]);
        exit;
    }

    $hashed = password_hash($newPass, PASSWORD_BCRYPT);

    // Clear reset fields from profile
    unset($profile['reset_token'], $profile['reset_expires']);
    $profileJson = json_encode($profile);

    $up = $conn->prepare("UPDATE users SET password = ?, profile = ? WHERE id = ?");
    $up->bind_param("ssi", $hashed, $profileJson, $user['id']);
    $up->execute();

    echo json_encode(["status" => true, "message" => "Password reset successful"]);
    exit;
}
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(45deg, #6366f1, #a855f7);
            --bg-color: #f3f4f7;
        }

        body {
            background: var(--bg-color);
            background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%), 
                              radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }

        .reset-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 28px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            margin: auto;
        }

        .brand-logo {
            font-weight: 800; font-size: 1.5rem; text-align: center;
            background: var(--primary-gradient);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 12px; padding: 12px 16px; border: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.5); font-size: 0.95rem;
        }

        .btn-reset {
            background: var(--primary-gradient); border: none; border-radius: 12px;
            padding: 14px; font-weight: 700; color: white; transition: all 0.3s;
        }

        .btn-reset:disabled { opacity: 0.6; cursor: not-allowed; }

        .pass-requirement { font-size: 0.75rem; color: #9ca3af; margin-top: 8px; }
        
        /* Success State View */
        .success-view { display: none; text-align: center; }
        .success-icon { 
            font-size: 4rem; color: #10b981; 
            animation: scaleUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes scaleUp { from { transform: scale(0); } to { transform: scale(1); } }
    </style>
</head>
<body>

<div class="container">
    <div class="reset-card" id="mainCard">
        <div class="brand-logo">monieFlow</div>
        
        <div id="formView">
            <h4 class="fw-bold mb-2">New Password</h4>
            <p class="text-muted small mb-4">Almost there! Please choose a strong password you haven't used before.</p>

            <form id="resetForm">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">NEW PASSWORD</label>
                    <div class="position-relative">
                        <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                        <i class="ri-eye-line position-absolute end-0 top-50 translate-middle-y me-3 text-muted" style="cursor: pointer;" id="togglePass"></i>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">CONFIRM PASSWORD</label>
                    <input type="password" id="confirm_password" class="form-control" placeholder="••••••••" required>
                    <div class="pass-requirement">Minimum 6 characters required.</div>
                </div>

                <button type="submit" class="btn btn-reset w-100" id="submitBtn">
                    <span id="btnText">Update Password</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </form>
        </div>

        <div id="successView" class="success-view">
            <i class="ri-checkbox-circle-fill success-icon"></i>
            <h4 class="fw-bold mt-3">All Done!</h4>
            <p class="text-muted small mb-4">Your password has been successfully updated. You can now log in with your new credentials.</p>
            <a href="login.php" class="btn btn-dark w-100 rounded-3">Go to Login</a>
        </div>
    </div>
</div>

<script>
    // Include your droppySammy function here
    // ... (Your DroppySammy function code) ...

    const resetForm = document.getElementById('resetForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    // Toggle Password Visibility
    document.getElementById('togglePass').onclick = function() {
        const passInput = document.getElementById('password');
        this.classList.toggle('ri-eye-line');
        this.classList.toggle('ri-eye-off-line');
        passInput.type = passInput.type === 'password' ? 'text' : 'password';
    };

    resetForm.onsubmit = async (e) => {
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;

        // Basic Validation
        if (password !== confirm) {
            droppySammy('danger', 'Error', 'Passwords do not match!');
            return;
        }

        if (password.length < 6) {
            droppySammy('warning', 'Notice', 'Password is too short.');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');

        // Get token from URL
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');

        try {
            const formData = new FormData();
            formData.append('password', password);

            const response = await fetch(`reset-password.php?token=${token}`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status) {
                document.getElementById('formView').style.display = 'none';
                document.getElementById('successView').style.display = 'block';
            } else {
                droppySammy('danger', 'Reset Failed', result.message);
                submitBtn.disabled = false;
                btnText.classList.remove('d-none');
                btnSpinner.classList.add('d-none');
            }
        } catch (error) {
            droppySammy('danger', 'System Error', 'Something went wrong. Try again.');
            submitBtn.disabled = false;
            btnText.classList.remove('d-none');
            btnSpinner.classList.add('d-none');
        }
    };
</script>
</body>
</html>