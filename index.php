<!DOCTYPE html>
<html lang="en">
<?php 
include __dir__."/config/function.php"; 
$main=new Main($conn);
if($main->isLoggedin()) {
  header('location:/member/index.php');
return;
}
?>
<head>
  <meta charset="UTF-8">
  <title>Monieflow – Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Remix Icon -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
  <script src="/libery.js"></script>
</head>
<body class="bg-light">

<div
  class="d-flex align-items-center justify-content-center overflow-auto bg-light"
  style="height: 100vh;"
>
  <div
    class="d-sm-flex p-3 text-center text-sm-start gap-2 w-100"
    style="max-width: 1000px;"
  >
    <!-- Left text -->
    <div class="w-100 mb-4 mb-sm-0">
      <h1 class="fw-bold text-capitalize fs-1">Monieflow</h1>
      <div class="fs-4 fw-normal">
        Monieflow helps you connect and share with the people in your life.
      </div>
    </div>

    <!-- Login Card -->
    <div class="col-12 col-sm-5 mx-auto">
      <div class="w-100 bg-white p-4 shadow rounded">

        <!-- Login container without form tag -->
        <div id="loginForm">

          <!-- Email / Phone -->
          <div class="mb-2">
            <input
              type="text"
              id="loginEmail"
              class="form-control p-3"
              placeholder="Email or phone number"
            >
            <div class="text-danger small mt-1" id="errLoginEmail"></div>
          </div>

          <!-- Password -->
          <!-- <div class="mb-2">
            <input
              type="password"
              id="loginPassword"
              class="form-control p-3"
              placeholder="Password"
            >
            <div class="text-danger small mt-1" id="errLoginPassword"></div>
          </div> -->


          <div class="mb-2 position-relative">
  <input
    type="password"
    id="loginPassword"
    class="form-control p-3"
    placeholder="Password"
  >
  <span
    id="togglePassword"
    class="position-absolute top-0 mt-4 pt-3 end-0 me-3 translate-middle-y ri-eye-close-fill"
    style="cursor: pointer; font-size: 1.2rem;"
  >
  </span>
  <div class="text-danger small mt-1" id="errLoginPassword"></div>
</div>

          <!-- Login Button -->
          <button
            type="button"
            class="btn btn-primary w-100 p-3 mb-3"
            onclick="validateLogin()"
          >
            Log In
          </button>

          <div class="text-center mb-3">
            <a href="/fp.php" class="text-decoration-none">
              Forgot password?
            </a>
          </div>

          <hr>

          <div class="text-center">
            <a href="/signup.php" class="btn btn-success px-4 p-3">
              Create new account
            </a>
          </div>

        </div>

      </div>

      <div class="text-center my-2">
        <a
          href="/create-page"
          class="text-dark fw-bold text-decoration-none me-1"
        >
          Create a Page
        </a>
        for a celebrity, brand or business.
      </div>
    </div>
  </div>
</div>

<script>

// Toggle password visibility
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('loginPassword');

togglePassword.addEventListener('click', () => {
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    togglePassword.classList.add('ri-eye-close-fill')
    togglePassword.classList.remove('ri-eye-fill')
  } else {
    passwordInput.type = "password";
    togglePassword.classList.add('ri-eye-fill')
    togglePassword.classList.remove('ri-eye-close-fill')
  }
});

// Helper: detect email or phone
function isEmailOrPhone(value) {
  value = value.trim();
  if (!value) return false;
  if (value.includes("@")) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ? "email" : false;
  } else {
    return /^\+?\d{8,15}$/.test(value) ? "phone" : false;
  }
}

// Main login validation
async function validateLogin() {
  showLoading()
  const emailInput = document.getElementById('loginEmail');
  const passwordInput = document.getElementById('loginPassword');
  const errEmail = document.getElementById('errLoginEmail');
  const errPassword = document.getElementById('errLoginPassword');

  // Reset errors
  errEmail.textContent = "";
  errPassword.textContent = "";
  emailInput.classList.remove('is-invalid');
  passwordInput.classList.remove('is-invalid');

  let hasError = false;

  // Email / Phone validation
  const type = isEmailOrPhone(emailInput.value);
  if (!type) {
    errEmail.textContent = emailInput.value.includes("@") ?
      "Enter a valid email address" : "Enter a valid phone number";
    emailInput.classList.add('is-invalid');
    hasError = true;
  }

  // Password required
  if (!passwordInput.value.trim()) {
    errPassword.textContent = "Password is required";
    passwordInput.classList.add('is-invalid');
    hasError = true;
  }

  if (!hasError) {
    try {
      const req = await postfileItem('/pages/req.php', {
        action: "login",
        emailOrPhone: emailInput.value.trim(),
        password: passwordInput.value
      });

      if (req.success) {
        window.location.href = "/member/index.php";
      } else {
        alert(req.error || "Login failed. Check your credentials.");
      }
    } catch (e) {
      console.error(e);
      alert("Something went wrong. Please try again.");
    }
  }
  hideLoading();
}
</script>

</body>
</html>
