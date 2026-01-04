<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Alister Bank</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
/* ===== RESET ===== */
body {
  font-family: "Segoe UI", Arial, sans-serif;
}

/* ===== TOP SOCIAL BAR ===== */
.social-bar {
  background: #efefef;
  border-bottom: 1px solid #ddd;
}
.social-bar a {
  color: #999;
  font-size: 14px;
  margin-left: 12px;
}
.social-bar a:hover {
  color: #d9534f;
}

/* ===== LOGO BAR ===== */
.logo-bar {
  background: #fff;
  padding: 18px 0;
}
.logo-text {
  font-size: 22px;
  font-weight: 600;
}
.logo-icon {
  width: 42px;
  height: 42px;
  margin-right: 10px;
}

/* ===== UTILITY LINKS ===== */
.utility-links a {
  color: #333;
  font-size: 14px;
  margin-left: 15px;
  text-decoration: none;
}
.utility-links i {
  margin-right: 6px;
}
.utility-links a:hover {
  color: #d9534f;
}

/* ===== MAIN NAV ===== */
.main-nav {
  border-top: 1px solid #ddd;
}
.main-nav .nav-link {
  color: #000;
  font-weight: 500;
  padding: 16px 18px;
}
.main-nav .nav-link.active {
  border-bottom: 3px solid #d9534f;
  color: #d9534f;
}
.main-nav .nav-link:hover {
  color: #d9534f;
}
</style>
</head>

<body class="bg-light">
 <div class="header bg-white sticky-top">
   <!-- 🔹 TOP SOCIAL BAR -->
  <div class="social-bar py-2">
    <div class="container d-flex justify-content-end">
      <a href="#"><i class="bi bi-twitter"></i></a>
      <a href="#"><i class="bi bi-facebook"></i></a>
      <a href="#"><i class="bi bi-youtube"></i></a>
      <a href="#"><i class="bi bi-instagram"></i></a>
      <a href="#"><i class="bi bi-google"></i></a>
    </div>
  </div>

  <div class="logo-bar">
    <div class="container d-flex justify-content-between align-items-center">

      <!-- Logo -->
      <div class="d-flex align-items-center">
        <span class="logo-text text-capitalize" style="font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">monieFlow</span>
      </div>

      <!-- Utilities -->
      <div class="utility-links d-flex align-items-center">
        <a href="#"><i class="bi bi-geo-alt"></i> <span class="d-nonev d-sm-inline">Ng</span></a>
        <a href="#"><i class="bi bi-wallet"></i> <span class="">MF0.00</span></a>
        <?php if($main->isLoggedIn()): ?>
        <a href="/home.php"><i class="bi bi-person"></i> <span class=""><?= $main->getUserData()['data']['user'] ?></span></a>
        <?php else: ?>
        <a href="/login.php"><i class="bi bi-person"></i> <span class="">Sign In</span></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <hr class="my-0">
  <div class="container d-flex overflow-auto gap-3 py-1 text-capitalize">
    <a class="nav-link active" href="/">Home</a>
    <a class="nav-link" href="/create-wallet.php">create&nbsp;wallet</a>
    <a class="nav-link" href="/community.php">community</a>
    <a class="nav-link" href="/transfer.php">transfer</a>
  </div>
 </div>
