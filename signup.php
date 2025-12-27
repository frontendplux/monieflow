<?php include __DIR__."/compo/header.php"; ?>
<body class="theme-light">

<div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>

<!-- Page Wrapper-->
<div id="page">

    <!-- Page Content - Only Page Elements Here-->
    <div class="page-content my-0 py-0">

        <div class="card bg-5 card-fixed">
            <div class="card-center mx-3 px-4 py-4 bg-white rounded-m">
                <h1 class="font-30 font-800 mb-0"><?= company ?></h1>
                <p>Create an account</p>
                <div class="form-custom form-label form-border form-icon mb-3 bg-transparent">
                    <i class="bi bi-person-circle font-13"></i>
                    <input type="text" class="form-control rounded-xs" id="c1a" placeholder="Username"/>
                    <label for="c1a" class="color-theme">Username</label>
                    <span>(required)</span>
                </div>
                <div class="form-custom form-label form-border form-icon mb-3 bg-transparent">
                    <i class="bi bi-at font-16"></i>
                    <input type="email" class="form-control rounded-xs" id="c1" placeholder="Email Address"/>
                    <label for="c1" class="color-theme">Email Address</label>
                    <span>(required)</span>
                </div>
                <div class="form-custom form-label form-border form-icon mb-3 bg-transparent">
                    <i class="bi bi-asterisk font-13"></i>
                    <input type="password" class="form-control rounded-xs" id="c2" placeholder="Choose Password"/>
                    <label for="c2" class="color-theme">Choose Password</label>
                    <span>(required)</span>
                </div>
                <div class="form-custom form-label form-border form-icon mb-4 bg-transparent">
                    <i class="bi bi-asterisk font-13"></i>
                    <input type="password" class="form-control rounded-xs" id="c3" placeholder="Confirm Password"/>
                    <label for="c3" class="color-theme">Choose Password</label>
                    <span>(required)</span>
                </div>
                <div class="form-check form-check-custom">
                    <input class="form-check-input" type="checkbox" name="type" value="" id="c2a">
                    <label class="form-check-label font-12" for="c2a">I agree with the <a href="#">Terms and Conditions</a>.</label>
                    <i class="is-checked color-highlight font-13 bi bi-check-circle-fill"></i>
                    <i class="is-unchecked color-highlight font-13 bi bi-circle"></i>
                </div>
                <button onclick="signup(this)" type="button" id="signupBtn" class="btn btn-full w-100 gradient-highlight shadow-bg shadow-bg-s mt-4"> Create Account </button>
                <!-- <a href="index.html" class="btn btn-full gradient-highlight shadow-bg shadow-bg-s mt-4">Create Account</a> -->
                <div class="row">
                    <div class="col-6 text-start">
                        <a href="/forget.php" class="font-11 color-theme opacity-40 pt-4 d-block">Forgot Password?</a>
                    </div>
                    <div class="col-6 text-end">
                        <a href="/index.php" class="font-11 color-theme opacity-40 pt-4 d-block">Sign In Account</a>
                    </div>
                </div>
            </div>
            <div class="card-overlay rounded-0 m-0 bg-black opacity-70"></div>
        </div>
    </div>
</div>

<?php include __DIR__."/compo/footer.php"; ?>

<script>
function signup(e) {
    showLoading();
    const username = document.getElementById('c1a').value.trim();
    const email    = document.getElementById('c1').value.trim();
    const password = document.getElementById('c2').value.trim();
    const confirm  = document.getElementById('c3').value.trim();
    const agree    = document.getElementById('c2a').checked;

    // Validation
    if (!username || !email || !password || !confirm) {
        alert("All fields are required!");
        hideLoading();
        return;
    }
    if (password !== confirm) {
        alert("Passwords do not match!");
        hideLoading();
        return;
    }
    if (!agree) {
        alert("You must agree to the Terms and Conditions.");
        hideLoading();
        return;
    }

    // Build FormData
    const fd = new FormData();
    fd.append("username", username);
    fd.append("email", email);
    fd.append("password", password);
    fd.append("action", "signup");

    // Send POST request
    fetch("/req.php", {
        method: "POST",
        body: fd
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = "/confirm-pass.php"; // redirect to login
        } else {
            alert(data.message || "Signup failed");
        }
    })
    .catch(err => console.error("Error:", err))
    .finally(() => hideLoading());
}
</script>

</body>