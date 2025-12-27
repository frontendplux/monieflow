<?php include __DIR__."/compo/header.php";
   $main=new main($conn);
   if($main->isLoggedIn()) return header('location:/home.php');
 ?>
<body class="theme-light">

<div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>

<!-- Page Wrapper-->
<div id="page">

    <!-- Page Content-->
    <div class="page-content my-0 py-0">

        <div class="card bg-5 card-fixed mx-auto" style="max-width:600px">
            <div class="card-center mx-3 px-4 py-4 bg-white rounded-m">
                <h1 class="font-30 font-800 mb-0"><?= company ?></h1>
                <p>Login to your Account.</p>

                <!-- Username -->
                <div class="form-custom form-label form-border form-icon mb-3 bg-transparent">
                    <i class="bi bi-person-circle font-13"></i>
                    <input type="text" class="form-control rounded-xs" id="username" placeholder="Username"/>
                    <label for="username" class="color-theme">Username</label>
                    <span>(required)</span>
                </div>

                <!-- Password -->
                <div class="form-custom form-label form-border form-icon mb-4 bg-transparent">
                    <i class="bi bi-asterisk font-13"></i>
                    <input type="password" class="form-control rounded-xs" id="password" placeholder="Password"/>
                    <label for="password" class="color-theme">Password</label>
                    <span>(required)</span>
                </div>

                <!-- Remember -->
                <div class="form-check form-check-custom">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label font-12" for="remember">Remember this account</label>
                    <i class="is-checked color-highlight font-13 bi bi-check-circle-fill"></i>
                    <i class="is-unchecked color-highlight font-13 bi bi-circle"></i>
                </div>

                <!-- Sign In Button -->
                <button onclick="login(this)" type="button" id="loginBtn" class="btn btn-full w-100 gradient-highlight shadow-bg shadow-bg-s mt-4">
                    SIGN IN
                </button>

                <div class="row">
                    <div class="col-6 text-start">
                        <a href="page-forgot-1.html" class="font-11 color-theme opacity-40 pt-4 d-block">Forgot Password?</a>
                    </div>
                    <div class="col-6 text-end">
                        <a href="/signup.php" class="font-11 color-theme opacity-40 pt-4 d-block">Create Account</a>
                    </div>
                </div>
            </div>
            <div class="card-overlay rounded-0 m-0 bg-black opacity-70"></div>
        </div>

    </div>
    <!-- End of Page Content-->

</div>
<!-- End of Page ID-->

<?php include __DIR__."/compo/footer.php"; ?>

<script>
function login(e) {
    showLoading();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const remember = document.getElementById('remember').checked;

    if (!username || !password) {
        alert("Both fields are required!");
        hideLoading();
        return;
    }

    const fd = new FormData();
    fd.append("username", username);
    fd.append("password", password);
    fd.append("remember", remember ? "1" : "0");
    fd.append("action", "login");

    fetch("/req.php", {
        method: "POST",
        body: fd
    })
    .then(res => res.json())
    .then(data => {
        if (data[0]) {
            window.location.href = "/home.php";
        } else {
            alert(data[1] || "Login failed");
        }
    })
    .catch(err => console.error("Error:", err))
    .finally(() => hideLoading());
}
</script>

</body>
