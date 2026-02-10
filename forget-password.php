<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SocialFlow</title>
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

        .recovery-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 28px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: auto;
            text-align: center;
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.5);
        }

        .btn-recover {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            color: white;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn-recover:hover {
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
            transform: translateY(-1px);
        }

        .back-to-login {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 1.5rem;
            transition: color 0.2s;
        }

        .back-to-login:hover {
            color: #6366f1;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="recovery-card">
        <div class="icon-box">
            <i class="ri-lock-password-line"></i>
        </div>

        <h4 class="fw-bold mb-2">Forgot Password?</h4>
        <p class="text-muted small mb-4">No worries, it happens. Enter your email below and we'll send you reset instructions.</p>

        <form id="recoveryForm">
            <div class="text-start mb-3">
                <label class="form-label fw-bold small text-muted ms-1">EMAIL ADDRESS</label>
                <input type="email"  id="recoveryEmail" class="form-control" placeholder="Enter your email" required>
            </div>

            <!-- <button type="submit" class="btn btn-recover w-100">Send Reset Link</button> -->
             <button type="submit" class="btn btn-recover w-100" id="recoveryBtn">
                <span class="btn-text">Send Reset Link</span>
                <span class="spinner-border spinner-border-sm d-none" id="recoverySpinner"></span>
            </button>
        </form>

        <div id="successMessage" class="d-none">
            <div class="alert alert-success border-0 rounded-4 py-3" style="background: rgba(34, 197, 94, 0.1); color: #16a34a;">
                <i class="ri-checkbox-circle-fill me-1"></i> Email sent successfully!
            </div>
            <p class="small text-muted">Please check your inbox (and spam folder) for the reset link.</p>
        </div>

        <a href="login.html" class="back-to-login">
            <i class="ri-arrow-left-line"></i> Back to Login
        </a>
    </div>
</div>
<script src="/api.js"></script>
<script>
    // Simple UI Toggle for demonstration
    document.getElementById('recoveryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        this.classList.add('d-none'); // Hide form
        document.getElementById('successMessage').classList.remove('d-none'); // Show success
    });
</script>
<script>
const form = document.getElementById("recoveryForm");
const emailInput = document.getElementById("recoveryEmail");
const btn = document.getElementById("recoveryBtn");
const spinner = document.getElementById("recoverySpinner");
const btnText = btn.querySelector(".btn-text");

form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const email = emailInput.value.trim();

    // ===== VALIDATION =====
    if (!email) {
        return droppySammy('info', 'Auth Error', 'Email is required');
    }

    if (!/^\S+@\S+\.\S+$/.test(email)) {
        return droppySammy('info', 'Auth Error', 'Enter a valid email address');
    }

    // ===== BUTTON STATE =====
    btn.disabled = true;
    spinner.classList.remove("d-none");
    btnText.textContent = "Sending link...";

    try {
        const res = await fetch("/req.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "forgot_password",
                email: email
            })
        });

        const req = await res.json();

        if (req.status === true) {
            form.classList.add("d-none");
            document.getElementById("successMessage").classList.remove("d-none");
        } else {
            droppySammy('danger', 'Auth Error', req.message || "Request failed");
        }

    } catch (err) {
        droppySammy('danger', 'Auth Error', "Network error. Try again.");
        console.error(err);
    } finally {
        btn.disabled = false;
        spinner.classList.add("d-none");
        btnText.textContent = "Send Reset Link";
    }
});
</script>

</body>
</html>