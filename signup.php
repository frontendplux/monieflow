<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
include __dir__."/config/function.php"; 
$main=new Main($conn);
if($main->isLoggedin()) {
  header('location:/member/index.php');
return;
}
?>
  <meta charset="UTF-8">
  <title>Monieflow – Sign Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Remix Icon -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
  <script src="/libery.js"></script>
</head>

<body class="bg-light">

<div class="d-flex justify-content-center align-items-center vh-100 p-2">
  <div>
    <h1 class="text-center text-capitalize my-4 text-primary">monieflow</h1>

    <div class="bg-white shadow rounded p-3" style="max-width: 450px; width: 100%;">
      <h3 class="text-center">Create a new account</h3>
      <div class="text-center">It's quick and easy.</div>
      <hr>

      <!-- START SIGNUP CONTAINER -->
      <div id="signupForm">

        <!-- First & Last Name -->
        <div class="d-flex gap-2 mb-1">
          <input type="text" id="firstName" class="form-control" placeholder="First name">
          <input type="text" id="lastName" class="form-control" placeholder="Last name">
        </div>
        <div class="text-danger small mb-2" id="errName"></div>

        <!-- Date of Birth -->
        <div class="mb-1">
          Date of birth <i class="ri-question-fill"></i>
        </div>
        <div class="d-flex gap-2 mb-1">
          <select id="day" class="form-control">
            <option value="">Day</option>
          </select>
          <select id="month" class="form-control">
            <option value="">Month</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
          <select id="year" class="form-control">
            <option value="">Year</option>
          </select>
        </div>
        <div class="text-danger small mb-2" id="errDOB"></div>

        <!-- Gender -->
        <label class="mb-1">Gender</label>
        <div class="d-flex justify-content-between mb-1 gap-3">
          <label class="d-flex justify-content-between w-100 border p-2 rounded">
            Female <input type="radio" name="sex" value="Female">
          </label>
          <label class="d-flex justify-content-between w-100 border p-2 rounded">
            Male <input type="radio" name="sex" value="Male">
          </label>
          <label class="d-flex justify-content-between w-100 border p-2 rounded">
            Custom <input type="radio" name="sex" value="Custom">
          </label>
        </div>
        <div class="text-danger small mb-2" id="errGender"></div>

        <!-- Email -->
        <input type="email" id="email" class="form-control mb-1"
               placeholder="Mobile number or email address">
        <div class="text-danger small mb-2" id="errEmail"></div>

        <!-- Password -->
        <input type="password" id="password" class="form-control mb-1"
               placeholder="New password">
        <div class="text-danger small mb-2" id="errPassword"></div>

        <!-- Info Text -->
        <p style="font-size: small;">
          People who use our service may have uploaded your contact information.
          <a href="#">Learn more.</a>
        </p>
        <p style="font-size: small;">
          By clicking Sign up, you agree to our Terms, Privacy Policy and Cookies Policy.
        </p>

        <!-- ACTION -->
        <div class="text-center">
          <button type="button" onclick="validateSignup()"
                  class="btn btn-success px-5 fw-bold py-2">
            Sign Up
          </button>
        </div>

        <div class="text-center my-3">
          <a class="text-decoration-none" href="/">Already have an account?</a>
        </div>

      </div>
      <!-- END SIGNUP CONTAINER -->

    </div>
  </div>
</div>

<script>
/* Populate Day & Year */
const daySelect = document.getElementById('day');
const yearSelect = document.getElementById('year');

for (let i = 1; i <= 31; i++) {
  daySelect.innerHTML += `<option value="${i}">${i}</option>`;
}

const thisYear = new Date().getFullYear();
for (let i = 0; i < 100; i++) {
  yearSelect.innerHTML += `<option value="${thisYear - i}">${thisYear - i}</option>`;
}

/* Helper function to detect email or phone */
function isEmailOrPhone(value) {
  value = value.trim();
  if (value.includes("@")) {
    // Email regex
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ? "email" : false;
  } else {
    // Phone regex: digits only, 8-15 digits, optional + at start
    return /^\+?\d{8,15}$/.test(value) ? "phone" : false;
  }
}

/* Main Validation Function */
async function validateSignup() {
  const firstName = document.getElementById('firstName');
  const lastName = document.getElementById('lastName');
  const day = document.getElementById('day').value;
  const month = document.getElementById('month').value;
  const year = document.getElementById('year').value;
  const gender = document.querySelector('input[name="sex"]:checked');
  const email = document.getElementById('email');
  const password = document.getElementById('password');

  const errName = document.getElementById('errName');
  const errDOB = document.getElementById('errDOB');
  const errGender = document.getElementById('errGender');
  const errEmail = document.getElementById('errEmail');
  const errPassword = document.getElementById('errPassword');

  // Reset errors
  errName.textContent = "";
  errDOB.textContent = "";
  errGender.textContent = "";
  errEmail.textContent = "";
  errPassword.textContent = "";

  [firstName, lastName, email, password].forEach(i => i.classList.remove('is-invalid'));

  let hasError = false;

  // Name validation
  if (!firstName.value.trim() || !lastName.value.trim()) {
    errName.textContent = "First and last name are required";
    firstName.classList.add('is-invalid');
    lastName.classList.add('is-invalid');
    hasError = true;
  }

  // DOB validation (age must be above 8)
  if (!day || !month || !year) {
    errDOB.textContent = "Date of birth is required";
    hasError = true;
  } else {
    const dob = new Date(year, month - 1, day);
    const today = new Date();

    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;

    if (age <= 8) {
      errDOB.textContent = "You must be above 8 years old";
      hasError = true;
    }
  }

  // Gender validation
  if (!gender) {
    errGender.textContent = "Please select your gender";
    hasError = true;
  }

  // Email or phone validation
  const type = isEmailOrPhone(email.value);
  if (!type) {
    errEmail.textContent = email.value.includes("@") ? 
      "Enter a valid email address" : "Enter a valid phone number";
    email.classList.add('is-invalid');
    hasError = true;
  }

  // Password validation
  if (password.value.length < 6) {
    errPassword.textContent = "Password must be at least 6 characters";
    password.classList.add('is-invalid');
    hasError = true;
  }

  // Submit if no errors
  if (!hasError) {
    try {
      const req = await postfileItem('/pages/req.php', {
        action: "signup",
        firstname: firstName.value.trim(),
        lastname: lastName.value.trim(),
        dob: `${day}-${month}-${year}`,
        gender: gender.value,
        email: email.value.trim(),
        password: password.value
      });
      console.log(req);
      
      if (req.success) {
        window.location.href = "/member/index.php";
      } else {
        alert(req.error || "Signup failed. Try again!");
      }
    } catch (e) {
      console.error(e);
      alert("Something went wrong. Please try again.");
    }
  }
}
</script>


</body>
</html>
