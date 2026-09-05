<?php
session_start();
include __DIR__ . '/conn.php';
include __DIR__ . '/mailler.php';


// Helper function to return JSON responses for AJAX
function sendJsonResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit();
}

/**
 * Generates a collision-proof unique UID formatted like 'usr-435' or 'usr-92a1'
 */
function generateUniqueUID($conn) {
    do {
        // Generates random alphanumeric string prefixed with 'usr-'
        $uid = 'usr-' . substr(bin2hex(random_bytes(3)), 0, 4);
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
    } while ($exists);

    return $uid;
}

/**
 * Generates a collision-proof 64-character hex security token
 */
function generateUniqueToken($conn) {
    do {
        $token = bin2hex(random_bytes(32));
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
    } while ($exists);

    return $token;
}

// -----------------------------------------------------------------------------
// 1. POST REQUEST: Email/Username Submission
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_email') {
    $identifier = trim($_POST['identifier'] ?? '');

    if (empty($identifier)) {
        sendJsonResponse(false, 'Email or username is required.');
    }

    // Generate a 4-digit numeric PIN
    $pin = sprintf('%04d', rand(0, 9999));
    $hashedPin = password_hash($pin, PASSWORD_DEFAULT);

    // Check if email/username exists in database
    $stmt = $conn->prepare("SELECT id, uid, email FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // User exists -> Update password field with new hashed PIN
        $uid = $row['uid'];
        $userEmail = $row['email'];

        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->bind_param("si", $hashedPin, $row['id']);
        $updateStmt->execute();
    } else {
        // User does not exist -> Generate guaranteed unique UID and Token
        $userEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? $identifier : $identifier . '@monieflow.com';
        $username = explode('@', $identifier)[0];
        
        $uid = generateUniqueUID($conn);        // Format: usr-xxxx
        $dummyToken = generateUniqueToken($conn); // Unique cryptographically secure token
        $fullName = '';
        $phone = '';                             // Phone allowed to be null

        $insertStmt = $conn->prepare("INSERT INTO users (uid, token, full_name, username, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssss", $uid, $dummyToken, $fullName, $username, $userEmail, $hashedPin);
        $insertStmt->execute();
    }

    // Send formatted HTML verification email
    $subject = "Your MonieFlow Verification Code";
    
    // Split PIN into individual digits for UI rendering
    $pinDigits = str_split($pin);
    $pinHtml = '';
    foreach ($pinDigits as $digit) {
        $pinHtml .= '<td style="padding: 0 4px;"><div style="background-color: #f4f9fc; border: 1px solid #00a8e8; border-radius: 8px; color: #00a8e8; font-size: 24px; font-weight: 700; height: 50px; line-height: 50px; text-align: center; width: 44px;">' . $digit . '</div></td>';
    }

    $emailBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verification Code</title>
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f9fc; font-family: \'Plus Jakarta Sans\', Arial, sans-serif; -webkit-font-smoothing: antialiased;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f9fc; padding: 40px 0;">
            <tr>
                <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px; background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0, 168, 232, 0.15); box-shadow: 0 10px 30px rgba(0, 168, 232, 0.05); overflow: hidden;">
                        
                        <!-- Header Banner -->
                        <tr>
                            <td align="center" style="background-color: #00a8e8; padding: 32px 20px;">
                                <img src="https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/logo.png" alt="MonieFlow Logo" width="60" height="60" style="display: block; border-radius: 50%; background-color: #ffffff; padding: 4px; margin-bottom: 12px;">
                                <h1 style="color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; letter-spacing: -0.3px;">MonieFlow Verification</h1>
                            </td>
                        </tr>

                        <!-- Content Body -->
                        <tr>
                            <td style="padding: 36px 32px; text-align: center;">
                                <h2 style="color: #1e293b; font-size: 20px; font-weight: 600; margin: 0 0 12px 0;">Authentication Required</h2>
                                <p style="color: #64748b; font-size: 14px; line-height: 22px; margin: 0 0 28px 0;">Use the 4-digit code below to complete your login. This code will expire shortly.</p>
                                
                                <!-- OTP Box Display -->
                                <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto 28px auto;">
                                    <tr>
                                        ' . $pinHtml . '
                                    </tr>
                                </table>

                                <p style="color: #64748b; font-size: 13px; line-height: 20px; margin: 0;">If you did not request this code, please ignore this email or contact support if you have security concerns.</p>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="background-color: #f8fafc; padding: 20px 32px; border-top: 1px solid #e2e8f0; text-align: center;">
                                <p style="color: #94a3b8; font-size: 12px; margin: 0; line-height: 18px;">&copy; ' . date('Y') . ' MonieFlow Inc. All rights reserved.<br>Perform seamless financial transactions with ease.</p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    sendMail($userEmail, $subject, $emailBody);
    
    // Set initial session
    $_SESSION['suid'] = $uid;
    $_SESSION['email'] = $userEmail;

    sendJsonResponse(true, 'Verification PIN sent to your email.', [
        'email' => $userEmail,
        'uid' => $uid
    ]);
}

// -----------------------------------------------------------------------------
// 2. POST REQUEST: PIN Submission & Verification
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_pin') {
    $pin = trim($_POST['pin'] ?? '');
    $suid = $_SESSION['suid'] ?? '';
    $sessionEmail = $_SESSION['email'] ?? '';

    if (empty($pin) || empty($suid) || empty($sessionEmail)) {
        sendJsonResponse(false, 'Invalid session or missing verification parameters.');
    }

    // Validate PIN against stored password hash
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE uid = ? AND email = ?");
    $stmt->bind_param("ss", $suid, $sessionEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pin, $row['password'])) {
            // Generate a fresh, guaranteed non-colliding session token
            $newToken = generateUniqueToken($conn);
            
            $updateStmt = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");
            $updateStmt->bind_param("si", $newToken, $row['id']);
            $updateStmt->execute();

            // Set final secure session variables
            $_SESSION['suid'] = $suid;
            $_SESSION['token'] = $newToken;

            sendJsonResponse(true, 'PIN verified successfully. Redirecting...', [
                'redirect' => '/member/index.php'
            ]);
        } else {
            sendJsonResponse(false, 'Invalid verification code. Please try again.');
        }
    } else {
        sendJsonResponse(false, 'User record not found.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>MonieFlow - Welcome Back!</title>
    <meta name="description" content="Welcome to MonieFlow. Perform seamless financial transactions, make multiple transfers, and manage your finances with ease.">
    <meta name="keywords" content="MonieFlow, finance app, seamless transfers, manage finances, online banking">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#00a8e8">

    <meta property="og:type" content="website">
    <meta property="og:title" content="MonieFlow - Welcome Back!">
    <meta property="og:description" content="Welcome back to MonieFlow. Perform seamless financial transactions and manage your money with ease.">
    <meta property="og:image" content="/logo.png">

    <link rel="icon" type="image/svg+xml" href="/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }   

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-whitesmoke);
            color: var(--text-dark);
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1.25rem;
            box-shadow: 0 12px 32px rgba(0, 168, 232, 0.08);
            width: 100%;
        }

        .brand-logo {
            color: var(--brand-skyblue);
            font-weight: 700;
        }

        .btn-skyblue {
            background-color: var(--brand-skyblue);
            color: #ffffff;
            font-weight: 600;
            border: none;
            transition: background-color 0.2s ease-in-out;
        }

        .btn-skyblue:hover, .btn-skyblue:focus {
            background-color: var(--brand-skyblue-hover);
            color: #ffffff;
        }

        .form-control:focus {
            border-color: var(--brand-skyblue);
            box-shadow: 0 0 0 0.25rem rgba(0, 168, 232, 0.25);
        }

        .pin-input-group .form-control {
            width: 50px;
            height: 55px;
            font-size: 1.5rem;
            font-weight: 700;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>

    <main class="container">
        <div class="mx-auto p-2 p-sm-4" style="max-width: 480px;">
            
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle mb-3" style="width: 56px; height: 56px; background-color: rgba(0, 168, 232, 0.1) !important;">
                    <img src="/logo.png" alt="" class="p-2 rounded-circle" style="width: 60px; height: 60px; background: #008cc3;">
                </div>
                <p class="fw-semibold text-dark mb-1" id="headerTitle">Welcome to MonieFlow</p>
                <p class="text-muted small" id="headerSubtitle">Do multiple transfers and manage your finances with ease.</p>
            </div>

            <div class="login-card p-4 p-sm-5">
                
                <!-- Step 1 Form -->
                <form id="step1Form" onsubmit="handleEmailSubmit(event)">
                    <div class="mb-3">
                        <label for="userIdentifier" class="form-label text-muted small mb-1">Email address or username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control border-start-0" 
                                id="userIdentifier" 
                                placeholder="e.g. alex@example.com or alex99" 
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" id="btnStep1" class="btn btn-skyblue w-100 py-2.5 rounded-3 mb-3">
                        Continue <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>

                <!-- Step 2 Form -->
                <form id="step2Form" style="display: none;" onsubmit="handlePinSubmit(event)">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-2" style="width: 48px; height: 48px; background-color: rgba(0, 168, 232, 0.1) !important;">
                            <i class="bi bi-envelope-check fs-4 brand-logo"></i>
                        </div>
                        <p class="small text-muted mb-0">We sent a 4-digit code to</p>
                        <strong class="text-dark id-display" id="userDisplay">user@example.com</strong>
                        <button type="button" onclick="goToStep1()" class="btn btn-link p-0 ms-1 small text-decoration-none brand-logo" style="font-size: 0.85rem;">Edit</button>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small text-center d-block mb-3">Enter the 4-digit PIN code</label>
                        <div class="d-flex justify-content-center gap-2 pin-input-group">
                            <input type="text" maxlength="1" class="form-control text-center pin-digit" inputmode="numeric" pattern="[0-9]*" required autocomplete="off">
                            <input type="text" maxlength="1" class="form-control text-center pin-digit" inputmode="numeric" pattern="[0-9]*" required autocomplete="off">
                            <input type="text" maxlength="1" class="form-control text-center pin-digit" inputmode="numeric" pattern="[0-9]*" required autocomplete="off">
                            <input type="text" maxlength="1" class="form-control text-center pin-digit" inputmode="numeric" pattern="[0-9]*" required autocomplete="off">
                        </div>
                    </div>

                    <button type="submit" id="btnStep2" class="btn btn-skyblue w-100 py-2.5 rounded-3 mb-3">
                        Verify & Sign In <i class="bi bi-shield-check ms-1"></i>
                    </button>

                    <div class="text-center">
                        <p class="text-muted small mb-0">Didn't receive the code? 
                            <a href="#" onclick="resendCode(event)" class="text-decoration-none brand-logo fw-semibold">Resend Code</a>
                        </p>
                    </div>
                </form>

            </div>

            <div class="text-center mt-3 pt-3 border-top col-8 mx-auto">
                <p class="text-muted small mb-0">Need help? <a href="#" class="text-decoration-none brand-logo">Contact Support</a></p>
            </div>

        </div>
    </main>

    <script>
        function showDropdownAlert(message, type = 'info', duration = 4000) {
            const themes = {
                success: { bg: '#10b981', border: '#059669', icon: '✓' },
                error:   { bg: '#ef4444', border: '#dc2626', icon: '✕' },
                warning: { bg: '#f59e0b', border: '#d97706', icon: '⚠' },
                info:    { bg: '#008cc3', border: '#00719e', icon: 'ℹ' }
            };
            const theme = themes[type] || themes.info;

            const alertBox = document.createElement('div');
            alertBox.innerHTML = `
                <span style="font-size: 1.1rem; font-weight: bold;">${theme.icon}</span>
                <span style="flex-grow: 1;">${message}</span>
                <button style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; padding: 0 0 0 10px; line-height: 1; opacity: 0.8;" onclick="this.parentElement.removeAlert()">&times;</button>
            `;

            Object.assign(alertBox.style, {
                position: 'fixed',
                top: '-100px',
                left: '50%',
                transform: 'translateX(-50%)',
                backgroundColor: theme.bg,
                color: '#ffffff',
                padding: '12px 20px',
                borderRadius: '12px',
                boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.2)',
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                fontSize: '0.95rem',
                fontWeight: '500',
                display: 'flex',
                alignItems: 'center',
                gap: '12px',
                zIndex: '99999',
                minWidth: '280px',
                maxWidth: '90%',
                border: `1px solid ${theme.border}`,
                transition: 'top 0.4s ease, opacity 0.3s ease',
                opacity: '0'
            });

            document.body.appendChild(alertBox);

            alertBox.removeAlert = () => {
                alertBox.style.top = '-100px';
                alertBox.style.opacity = '0';
                setTimeout(() => alertBox.remove(), 400);
            };

            requestAnimationFrame(() => {
                alertBox.style.top = '20px';
                alertBox.style.opacity = '1';
            });

            if (duration > 0) setTimeout(() => alertBox.removeAlert(), duration);
        }

        async function handleEmailSubmit(event) {
            event.preventDefault();
            const identifier = document.getElementById('userIdentifier').value;
            const btn = document.getElementById('btnStep1');
            btn.disabled = true;

            const formData = new FormData();
            formData.append('action', 'submit_email');
            formData.append('identifier', identifier);

            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status) {
                    showDropdownAlert(result.message, 'success');
                    document.getElementById('userDisplay').innerText = result.data.email;
                    document.getElementById('headerTitle').innerText = "Security Check";
                    document.getElementById('headerSubtitle').innerText = "Please verify your identity to proceed.";

                    document.getElementById('step1Form').style.display = 'none';
                    document.getElementById('step2Form').style.display = 'block';

                    const pinInputs = document.querySelectorAll('.pin-digit');
                    pinInputs[0].focus();
                } else {
                    showDropdownAlert(result.message, 'error');
                }
            } catch (err) {
                showDropdownAlert('An unexpected error occurred.', 'error');
            } finally {
                btn.disabled = false;
            }
        }

        async function handlePinSubmit(event) {
            event.preventDefault();
            const pinInputs = document.querySelectorAll('.pin-digit');
            let pin = '';
            pinInputs.forEach(i => pin += i.value);

            if (pin.length < 4) {
                showDropdownAlert('Please enter the full 4-digit code.', 'warning');
                return;
            }

            const btn = document.getElementById('btnStep2');
            btn.disabled = true;

            const formData = new FormData();
            formData.append('action', 'submit_pin');
            formData.append('pin', pin);

            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status) {
                    showDropdownAlert(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.data.redirect;
                    }, 1000);
                } else {
                    showDropdownAlert(result.message, 'error');
                }
            } catch (err) {
                showDropdownAlert('Verification failed. Try again.', 'error');
            } finally {
                btn.disabled = false;
            }
        }

        function resendCode(e) {
            e.preventDefault();
            document.getElementById('step1Form').dispatchEvent(new Event('submit'));
        }

        function goToStep1() {
            document.getElementById('headerTitle').innerText = "Welcome to MonieFlow";
            document.getElementById('headerSubtitle').innerText = "Do multiple transfers and manage your finances with ease.";
            document.getElementById('step2Form').style.display = 'none';
            document.getElementById('step1Form').style.display = 'block';
        }

        const pinInputs = document.querySelectorAll('.pin-digit');
        pinInputs.forEach((input, index) => {
            input.addEventListener('keyup', (e) => {
                if (e.key >= 0 && e.key <= 9) {
                    if (index < pinInputs.length - 1) pinInputs[index + 1].focus();
                } else if (e.key === 'Backspace') {
                    if (index > 0) pinInputs[index - 1].focus();
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>