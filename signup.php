<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join SocialFlow | Sign Up</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
            padding: 40px 0;
        }

        .signup-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 28px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 480px;
            margin: auto;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -1px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 12px;
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-signup {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            color: white;
            transition: all 0.3s;
        }

        .btn-signup:hover {
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Password Strength Meter */
        .strength-meter {
            height: 4px;
            background-color: #e5e7eb;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 30%; /* Dynamic */
            background: #f87171; /* Red for weak */
            transition: width 0.3s ease;
        }

        .terms-text {
            font-size: 0.75rem;
            color: #9ca3af;
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="signup-card">
        <div class="text-center mb-4">
            <div class="brand-logo mb-1">monieFlow</div>
            <h4 class="fw-bold text-dark">Create your account</h4>
            <p class="text-muted small">Join our community of creators today.</p>
        </div>

        <form id="signupForm">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">profile picture</label>
                    <div class="input-group" style="cursor:pointer">
                        <input type="file"  id="img" accept="image/*" class="form-control" required>
                        <!-- <span class="input-group-text bg-transparent ri-user-fill border-end-0 text-muted" style="border-radius: 12px 0 0 12px;"></span>
                        <span type="text" class="form-control border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="johndoe">Profile Picture</span> -->
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" id="first_name" class="form-control" placeholder="John" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" id="last_name" class="form-control" placeholder="Doe" required>
                </div>
                
                <div class="col-12">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px;">@</span>
                        <input type="text" id="username" class="form-control border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="johndoe" required>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="john@example.com" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Create a strong password" required>
                    <div class="strength-meter">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span id="strengthText" style="font-size: 0.7rem;">Password strength</span>
                    </div>

                </div>
            </div>

            <div class="form-check my-4">
                <input class="form-check-input" type="checkbox" id="terms" required>
                <label class="form-check-label small text-muted" for="terms">
                    I agree to the <a href="#" class="text-primary text-decoration-none fw-semibold">Terms of Service</a>
                </label>
            </div>
            <button type="submit" class="btn btn-signup w-100" id="signupBtn">
                <span class="btn-text">Create Account</span>
                <span class="spinner-border spinner-border-sm d-none" id="btnSpinner"></span>
            </button>

            <!-- <button type="submit" class="btn btn-signup w-100">Create Account</button> -->
        </form>

        <p class="terms-text">
            Already have an account? <a href="/" class="text-primary fw-bold text-decoration-none">Log in</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/api.js"></script>
<script>
const passwordInput = document.getElementById("password");
const strengthBar   = document.getElementById("strengthBar");
const strengthText  = document.getElementById("strengthText");

passwordInput.addEventListener("input", function () {
    const password = this.value;
    let strength = 0;

    // Rules
    if (password.length >= 8) strength++;                 // length
    if (/[a-z]/.test(password)) strength++;               // lowercase
    if (/[A-Z]/.test(password)) strength++;               // uppercase
    if (/\d/.test(password)) strength++;                  // number
    if (/[^a-zA-Z0-9]/.test(password)) strength++;        // special char

    // Reset
    strengthBar.style.width = "0%";
    strengthBar.style.background = "#e5e7eb";
    strengthText.style.color = "#9ca3af";

    switch (true) {

        case (strength <= 2):
            strengthBar.style.width = "25%";
            strengthBar.style.background = "#f87171"; // red
            strengthText.textContent = "Weak password";
            strengthText.style.color = "#f87171";
            break;

        case (strength === 3):
            strengthBar.style.width = "50%";
            strengthBar.style.background = "#fbbf24"; // yellow
            strengthText.textContent = "Medium strength";
            strengthText.style.color = "#fbbf24";
            break;

        case (strength === 4):
            strengthBar.style.width = "75%";
            strengthBar.style.background = "#34d399"; // green
            strengthText.textContent = "Strong password";
            strengthText.style.color = "#34d399";
            break;

        case (strength >= 5):
            strengthBar.style.width = "100%";
            strengthBar.style.background = "#22c55e"; // deep green
            strengthText.textContent = "Very strong password";
            strengthText.style.color = "#22c55e";
            break;
    }
});
</script>

<script>
const form = document.getElementById("signupForm");
const btn = document.getElementById("signupBtn");
const spinner = document.getElementById("btnSpinner");
const btnText = btn.querySelector(".btn-text");

form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const img = document.getElementById("img").files[0];
    const fname = document.getElementById("first_name").value.trim();
    const lname = document.getElementById("last_name").value.trim();
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const terms = document.getElementById("terms").checked;

    // ===== VALIDATION =====
    if (!img) return   droppySammy('info', 'Auth Error', 'Please upload a profile picture');
    if (!fname || !lname) return droppySammy('info', 'Auth Error',"Enter your full name");
    if (!/^[a-zA-Z0-9_]{3,20}$/.test(username))
        return droppySammy('info', 'Auth Error',"Username must be 3–20 characters (letters, numbers, _)");

    if (!/^\S+@\S+\.\S+$/.test(email))
        return droppySammy('info', 'Auth Error',"Enter a valid email address");

    if (password.length < 6)
        return droppySammy('info', 'Auth Error',"Password must be at least 6 characters");
    if (!terms)
        return droppySammy('info', 'Auth Error',"You must accept the terms");

    // ===== PREPARE DATA =====
    const formData = new FormData();
    formData.append("action", "signup");
    formData.append("image", img);
    formData.append("first_name", fname);
    formData.append("last_name", lname);
    formData.append("username", username);
    formData.append("email", email);
    formData.append("password", password);

    // ===== BUTTON LOADING =====
    btn.disabled = true;
    spinner.classList.remove("d-none");
    btnText.textContent = "Creating account...";

    try {
        const res = await fetch("/req.php", {
            method: "POST",
            body: formData
        });

        const req = await res.json();

        if (req.status === true) {
            window.location.href = "/feeds/";
        } else {
            droppySammy('danger', 'Auth Error',req.message || "Signup failed");
        }

    } catch (err) {
        droppySammy('danger', 'Auth Error',"Network error. Try again.");
        console.error(err);
    } finally {
        btn.disabled = false;
        spinner.classList.add("d-none");
        btnText.textContent = "Create Account";
    }
});
</script>

</body>
</html>