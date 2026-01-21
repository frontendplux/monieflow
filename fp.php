<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Find Your Account – Monieflow</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="/libery.js"></script>
</head>
<body class="bg-light">

<div class="bg-light vh-100">
  <!-- Header -->
  <header class="text-capitalize bg-white d-flex justify-content-between align-items-center px-3 p-2 shadow-sm">
    <h1 class="m-0 fs-4">monieflow</h1>
    <div>
      <a href="/" class="btn btn-success">Log In</a>
    </div>
  </header>

  <!-- Card -->
  <div class="col-12 col-md-4 mx-auto my-5 bg-white rounded shadow-sm">
    <div class="p-3 fw-bold fs-4">Find Your Account</div>
    <hr>

    <p class="px-3 fs-5">
      Please enter your email address or mobile number to search for your account.
    </p>

    <!-- Use a container instead of form tag -->
    <div id="findAccountForm">
      <div class="my-3 px-3">
        <input
          type="text"
          id="identifier"
          class="form-control p-3"
          placeholder="Email address or phone number"
        >
        <div class="text-danger small mt-1" id="errIdentifier"></div>
      </div>

      <hr>

      <div class="text-end my-3 pb-4 px-3">
        <a href="/" class="btn btn-light px-4 me-2">Cancel</a>
        <button type="button" class="btn btn-primary px-4" onclick="findAccount()">
          Search
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Helper: validate email or phone
function isEmailOrPhone(value) {
  value = value.trim();
  if (!value) return false;
  if (value.includes("@")) {
    // Email regex
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ? "email" : false;
  } else {
    // Phone regex: digits only, 8-15 digits, optional + at start
    return /^\+?\d{8,15}$/.test(value) ? "phone" : false;
  }
}

// Main function
async function findAccount() {
  const input = document.getElementById('identifier');
  const err = document.getElementById('errIdentifier');

  // Reset error
  err.textContent = "";
  input.classList.remove('is-invalid');

  const type = isEmailOrPhone(input.value);
  if(input.value === ''){
    err.textContent ="Enter a valid email address or phone number";
    input.classList.add('is-invalid');
    return;
  }

  if (!type) {
    err.textContent = input.value.includes("@") ? 
      "Enter a valid email address" : "Enter a valid phone number";
    input.classList.add('is-invalid');
    return;
  }

  // If valid, send AJAX request
  showLoading();
  try {
    const response = await postfileItem('/pages/req.php', {
      action: "findAccount",
      email: input.value.trim()
    });

    if (response.success) {
      // Redirect to next step, e.g., reset password page
      window.location.href ="/cp.php";
    } else {
      err.textContent = response.message || "Account not found.";
      input.classList.add('is-invalid');
    }
  } catch (e) {
    console.error(e);
    err.textContent = "Something went wrong. Please try again.";
    input.classList.add('is-invalid');
  }
  hideLoading();
}
</script>

</body>
</html>
