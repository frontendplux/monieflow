<?php include __DIR__."/compo/header.php"; ?>
<body class="theme-light">

<div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>

<!-- Page Wrapper-->
<div id="page">

    <!-- Page Content - Only Page Elements Here-->
    <div class="page-content my-0 py-0">
        <div class="card bg-5 card-fixed">
            <div class="card-center mx-3 px-4 py-4 bg-white rounded-m">
                <h1 class="font-30 font-800 mb-0 text-center"><?= company ?></h1>
                <p class="text-center">Enter passcode mailed to you <span class="text-info"><?= $_SESSION['user_save'] ?></span></p>
                <div class="form-custom form-label form-border form-icon mb-3 bg-transparent"> <i class="bi bi-lock font-16"></i> <input type="text" class="form-control rounded-xs" id="confirmCode" placeholder="Enter Confirmation Code"/> <label for="confirmCode" class="color-theme">Confirmation Code</label> <span>(required)</span> </div>
                <button onclick="confirmPasscode(this)" type="button" id="confirmBtn" class="btn btn-full gradient-highlight shadow-bg shadow-bg-s w-100 mt-4">confirm password</button>
                <div class="d-flex justify-content-center">
                    <div class="text-start">
                        <a href="/signup.php" class="font-16 color-theme opacity-40 pt-4 d-block">Return Back?</a>
                    </div>
                </div>
            </div>
            <div class="card-overlay rounded-0 m-0 bg-black opacity-70"></div>
        </div>

    </div>

    <div id="menu-sidebar"
        data-menu-active="nav-pages"
        data-menu-load="menu-sidebar.html"
        class="offcanvas offcanvas-start offcanvas-detached rounded-m">
    </div>

	<div id="menu-highlights"
		data-menu-load="menu-highlights.html"
		class="offcanvas offcanvas-bottom offcanvas-detached rounded-m">
	</div>
</div>

<?php include __DIR__."/compo/footer.php"; ?>
<script>
function confirmPasscode(e) {
    showLoading();
    const code = document.getElementById('confirmCode').value.trim();
    if (!code) {
        alert("Confirmation code is required!");
        hideLoading();
        return;
    }
    const fd = new FormData();
    fd.append("code", code);
    fd.append("action", "confirm");

    fetch("/req.php", {
        method: "POST",
        body: fd
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = "/home.php"; // or wherever you want to redirect
        } else {
            alert(data.message || "Invalid confirmation code");
        }
    })
    .catch(err => console.error("Error:", err))
    .finally(() => hideLoading());
}
</script>
</body>