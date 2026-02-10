<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SocialFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(45deg, #6366f1, #a855f7);
            --bg-color: #f3f4f7;
        }

        body {
            background: var(--bg-color);
            /* Optional: Add a subtle abstract background pattern */
            background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%), 
                              radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            margin: auto;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 2rem;
            letter-spacing: -1.5px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #4b5563;
            margin-left: 5px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            color: white;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
            opacity: 0.95;
        }

        .social-login-btn {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: white;
            padding: 10px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #374151;
            transition: background 0.2s;
        }

        .social-login-btn:hover {
            background: #f9fafb;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #9ca3af;
            font-size: 0.8rem;
            margin: 1.5rem 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .divider:not(:empty)::before { margin-right: .5em; }
        .divider:not(:empty)::after { margin-left: .5em; }

        .forgot-password {
            font-size: 0.85rem;
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container"><div class="brand-logo mb-0">monieFlow</div>
        <p class="text-center text-muted mb-4">Welcome back! Please login.</p>
    <div class="login-card">
        

        <form>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" id="email" class="form-control" placeholder="name@example.com" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Password</label>
                <div class="position-relative">
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <a href="/forget-password.php" class="forgot-password">Forgot password?</a>
            </div>

            <!-- <button type="submit" class="btn btn-login w-100 mb-3">Sign In</button> -->
             <button type="submit" class="btn btn-login w-100 mb-3" id="loginBtn">
                <span class="btn-text">Sign In</span>
                <span class="spinner-border spinner-border-sm d-none" id="btnSpinner"></span>
            </button>

  <!-- 
              <div class="divider">OR CONTINUE WITH</div>

              <div class="d-flex flex-column gap-2">
                  <button type="button" class="social-login-btn">
                      <i class="ri-google-fill text-danger"></i> Google
                  </button>
                  <button type="button" class="social-login-btn">
                      <i class="ri-facebook-box-fill text-primary"></i> Facebook
                  </button>
              </div> -->
        </form>

        <p class="text-center mt-4 mb-0 small text-muted">
            Don't have an account? <a href="/signup.php" class="fw-bold text-primary text-decoration-none">Sign up</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/api.js"></script>
<script>
//   // A dangerous error
// droppySammy('danger', 'Auth Error', 'Invalid passcode. Please try again.');

// // A successful action
// droppySammy('success', 'Published', 'Your post is now visible to followers.');

// // A general info alert
// droppySammy('info', 'Update', 'New features have been added to your feed.');
const form = document.querySelector("form");
const loginBtn = document.getElementById("loginBtn");
const spinner = document.getElementById("btnSpinner");
const btnText = document.querySelector(".btn-text");

form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const pass  = document.getElementById("password").value.trim();

    if (!email || !pass) {
        alert("Please fill in all fields");
        return;
    }

    // Button loading state
    loginBtn.disabled = true;
    spinner.classList.remove("d-none");
    btnText.textContent = "Signing in...";

    try {
        const res = await fetch("/req.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "login",
                email: email,
                pass: pass
            })
        });

        const req = await res.json();

        if (req.status === true) {
            window.location.href = "/feeds/";
        } else {
            droppySammy('danger', 'Auth Error',req.message || "Invalid login details");
        }

    } catch (error) {
      droppySammy('info', 'Auth Error', 'Network error. Please try again.');
        console.error(error);
    } finally {
        // Reset button
        loginBtn.disabled = false;
        spinner.classList.add("d-none");
        btnText.textContent = "Sign In";
    }
});
</script>

</body>
</html>