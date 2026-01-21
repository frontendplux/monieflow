
document.addEventListener('DOMContentLoaded', () => {
  checkLoginStatus();
});

async function checkLoginStatus() {
  const response = await fetch("/pages/req.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "status" })
  });
  const result = await response.json();

  if (result.success) {
    // Hide login modal
    const loginModalEl = document.getElementById('staticBackdrop');
    const loginModal = bootstrap.Modal.getInstance(loginModalEl);
    if (loginModal) loginModal.hide();
    console.log(result.data);
  } else {
    // Show login modal
    forceLogin();
  }
}


// Show modal that cannot be dismissed
function forceLogin() {
  const loginModalEl = document.getElementById('staticBackdrop');
  const loginModal = new bootstrap.Modal(loginModalEl, {
    backdrop: 'static',
    keyboard: false
  });
  loginModal.show();
}


// Login function
async function login() {
    showLoading()
  const emailInput = document.getElementById('loginEmail');
  const emailError = document.getElementById('emailError');
  const email = emailInput.value.trim();

  // Simple validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    emailError.textContent = "Please enter a valid email address.";
    emailError.classList.remove("d-none");
    hideLoading()
    return;
  } else {
    emailError.classList.add("d-none");
  }

  try {
    const response = await fetch("/pages/req.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "login", email: email })
    });

    const result = await response.json();
    if (result.success) {
      if (result.type === 1) {
        // Existing user: show confirm code input
        const modalBody = document.querySelector("#staticBackdrop .modal-body");
        modalBody.innerHTML = `
          <input type="text" id="confirmCode" class="form-control text-center p-3" placeholder="Enter 6-digit code">
          <small id="confirmError" class="text-danger d-none">Invalid code</small>
        `;
        const modalFooter = document.querySelector("#staticBackdrop .modal-footer");
        modalFooter.innerHTML = `
          <button type="button" class="btn btn-primary text-uppercase w-100 p-3" onclick="confirmLogin()">Confirm</button>
        `;
      } else {
        // New user: close modal
        const loginModalEl = document.getElementById('staticBackdrop');
        const loginModal = bootstrap.Modal.getInstance(loginModalEl);
        loginModal.hide();
        alert(result.message);
      }
    } else {
      alert(result.message);
    }
  } catch (error) {
    console.error("Login error:", error);
    alert("Something went wrong. Please try again later.");
  }
  hideLoading()
}

// Confirm step
async function confirmLogin() {
    showLoading()
  const codeInput = document.getElementById('confirmCode');
  const code = codeInput.value.trim();

  if (!code) {
    document.getElementById('confirmError').classList.remove("d-none");
    hideLoading()
    return;
  }

  try {
    const response = await fetch("/pages/req.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "verifyCode", code: code })
    });

    const result = await response.json();
    console.log(result);
    if (result.success) {
      const loginModalEl = document.getElementById('staticBackdrop');
      const loginModal = bootstrap.Modal.getInstance(loginModalEl);
      loginModal.hide();
      alert("Login confirmed! Welcome back.");
    } else {
      document.getElementById('confirmError').textContent = result.message;
      document.getElementById('confirmError').classList.remove("d-none");
    }
  } catch (error) {
    console.error("Confirm error:", error);
    alert("Something went wrong. Please try again later.");
  }
  hideLoading()
}
