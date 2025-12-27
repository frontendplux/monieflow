<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Crypto Transactions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Remix Icons -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="bg-white text-dark">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">mo</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Community</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Members</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Events</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- BALANCE -->
<div class="container mt-4">
  <div class="card bg-light">
    <div class="card-body text-center">
      <small class="text-muted">Available Balance</small>
      <h3>3,450 CC</h3>
      <p class="text-success mb-0">≈ $1,250.00</p>
    </div>
  </div>
</div>

<!-- ACTIONS -->
<div class="container mt-4">
  <div class="row text-center g-3">

    <div class="col-6 col-md-4 mb-3">
      <div class="card bg-whitesmoke">
        <div class="card-body">
          <i class="ri-send-plane-fill fs-2 text-success"></i>
          <p class="mt-2 mb-1">Transfer</p>
          <small class="text-muted">Send crypto</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 mb-3">
      <div class="card bg-whitesmoke">
        <div class="card-body">
          <i class="ri-bank-card-fill fs-2 text-warning"></i>
          <p class="mt-2 mb-1">Withdraw</p>
          <small class="text-muted">To bank</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 mb-3">
      <div class="card bg-whitesmoke">
        <div class="card-body">
          <i class="ri-gift-fill fs-2 text-info"></i>
          <p class="mt-2 mb-1">Redeem Card</p>
          <small class="text-muted">Gift cards</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 mb-3">
      <div class="card bg-whitesmoke">
        <div class="card-body">
          <i class="ri-swap-fill fs-2 text-primary"></i>
          <p class="mt-2 mb-1">Swap</p>
          <small class="text-muted">Exchange</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 mb-3">
      <div class="card bg-whitesmoke">
        <div class="card-body">
          <i class="ri-safe-fill fs-2 text-success"></i>
          <p class="mt-2 mb-1">Stake</p>
          <small class="text-muted">Earn rewards</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 mb-3">
      <div class="card bg-whitesmoke">
        <div class="card-body">
          <i class="ri-history-fill fs-2 text-secondary"></i>
          <p class="mt-2 mb-1">History</p>
          <small class="text-muted">All activity</small>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- RECENT ACTIVITY (CARD LIST) -->
<div class="container mt-4 mb-5">
  <h5 class="mb-3">Recent Activity</h5>

  <div class="card bg-whitesmoke mb-2">
    <div class="card-body d-flex justify-content-between">
      <div>
        <i class="ri-send-plane-fill text-success"></i> Transfer
        <div class="small text-muted">To 0x92A...F3E</div>
      </div>
      <div class="text-end">
        <strong>-120 CC</strong>
        <div class="small text-success">Completed</div>
      </div>
    </div>
  </div>

  <div class="card bg-whitesmoke mb-2">
    <div class="card-body d-flex justify-content-between">
      <div>
        <i class="ri-bank-card-fill text-warning"></i> Withdraw
        <div class="small text-muted">Bank payout</div>
      </div>
      <div class="text-end">
        <strong>-300 CC</strong>
        <div class="small text-warning">Pending</div>
      </div>
    </div>
  </div>

</div>

<!-- FOOTER -->
<footer class="text-center text-muted py-3 bg-white">
  ⛓ CryptoChain © 2026
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
