<?php 
include __DIR__."/config/function.php"; 
$main = new main($conn);
include __DIR__."/headers/header.php"; 


if (!$main->isLoggedIn()) {
    header('Location: /');
    exit;
}
if(count($main->wallet_balance()) <  1){ $main->createAccount(6);}
?>
<style>
    .walletSwiper,
.walletSwiper .swiper-wrapper,
.walletSwiper .swiper-slide {
  width: 100% !important;
}

.walletSwiper .swiper-slide {
  display: flex;
}

.wallet-card {
  width: 100%;
}
</style>
<div class="container py-4 mb-5">

 <!-- Wallet Swiper -->
<div class="swiper walletSwiper p-0 mb-4 w-100">
  <div class="swiper-wrapper w-100 p-0">
  <?php 
   
    $gradient=[
      "linear-gradient(135deg,#43e97b,#38f9d7)",
      "linear-gradient(135deg,#ff9a9e,#fecfef)",
      "linear-gradient(135deg,#667eea,#764ba2)",
      "linear-gradient(135deg,#4facfe,#00f2fe)"
    ]; 
  ?>
  <?php foreach ($main->wallet_balance() as $value): ?>
    <div class="swiper-slide p-0">
      <div class="wallet-card p-2 rounded" style="background:<?= $gradient[rand(0,3)] ?>">
        <h6><?= currencyIdToSymbols($conn, $value['currency_id'])[1]; ?></h6>
        <div class="d-flex justify-content-between">
          <h3>
            <p class="mb-1">Balance</p>
            <small style="font-size:15px;margin-right:4px">
              <?= currencyIdToSymbols($conn, $value['currency_id'])[0] ?>
            </small><?= $value['wallet_balance'] ?>
          </h3>
          <span>
            <p class="mb-1">Wallet ID</p><?= $value['id']; ?>
          </span>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
  <div class="swiper-pagination"></div>
</div>

  <!-- Quick Actions -->
  <h5 class="mb-3"><i class="ri-menu-line me-2"></i>Quick Actions</h5>

  <div class="row text-center mb-4">
    <div class="col">
      <button id="btnTransfer" class="btn btn-outline-primary w-100">
        <i class="ri-exchange-dollar-line"></i><br>Transfer
      </button>
    </div>
    <div class="col">
      <button id="btnwithdraw" class="btn btn-outline-success w-100">
        <i class="ri-bank-card-line"></i><br>Withdraw
      </button>
    </div>
    <div class="col">
      <button id="btnBuycard" class="btn btn-outline-info w-100">
        <i class="ri-money-dollar-circle-line"></i><br>Buy Card
      </button>
    </div>
  </div>

<?php if (count($main->fetchtransactionProducts()) > 0): ?>
  <!-- Notifications -->
  <div class="d-flex justify-content-between">
    <h5 class="mb-3"><i class="ri-dashboard-line me-2"></i>Recent Activity</h5>
    <a href="/transactions.php" class="text-capitalize">see all</a>
  </div>

  <div class="bg-white p-2 rounded">
  <?php foreach (array_slice($main->fetchtransactionProducts(), 0, 2) as $tx): 
        $isOutgoing = ($tx['sender_user_id'] === $main->getUserData()['data']['id']);
        $amountSign = $isOutgoing ? '-' : '+';
        $amountClass = $isOutgoing ? 'color-red-dark' : 'color-green-dark';
  ?>
    <a href="/receipt.php?id=<?= $tx['id'] ?>" class="mb-3 d-block  w-100 notification-item text-decoration-none">
      <h6 class="mb-1 d-flex justify-content-between">
        <span>Funds <?= $tx['transaction_type'] ?></span>
        <span>
             <?= $amountSign ?>
          <small style="font-size:9px;" class="fw-bold">
            <?= currencyIdToSymbols($conn, $tx['currency_id'])[0] ?>
          </small><?= $tx['amount'] ?>
        </span>
      </h6>
      <p class="mb-0 text-muted d-none">
        <span class="<?= $amountClass ?>">
          <?= $amountSign ?>
          <small style="font-size:9px;" class="fw-bold">
            <?= currencyIdToSymbols($conn, $tx['currency_id'])[0] ?>
          </small><?= $tx['amount'] ?>
        </span>
        was transferred
      </p>
      <small class="text-muted">Unread • <?= $tx['created_at'] ?></small>
    </a>
  <?php endforeach; ?>
  </div>
<?php endif; ?>
</div>


<?php include __DIR__."/menu-transfer.php"; ?>
<?php include __DIR__."/menu-withdraw.php"; ?>
<?php include __DIR__."/menu-buycard.php"; ?>
<!-- Footer -->
<div class="footer-nav">
  <a href="/fx.php"><i class="ri-stock-line"></i>FX</a>
  <a href="#"><i class="ri-home-5-line"></i>Home</a>
  <a href="#"><i class="ri-group-line"></i>Community</a>
  <a href="#"><i class="ri-menu-line"></i>Menu</a>
  <a href="#"><i class="ri-more-2-line"></i>More</a>
</div>
<div id="alertContainer" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999; width: 90%; max-width: 500px;"></div>



<?php include __DIR__."/headers/footer.php"; ?>

<script>
 // Swiper
  new Swiper(".walletSwiper", {
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true
    },
    spaceBetween: 20
  });


document.getElementById("btnTransfer").addEventListener("click", () => {
  if (window.innerWidth < 768) {
    new bootstrap.Offcanvas("#transferOffcanvas").show();
  } else {
    new bootstrap.Modal("#transferModal").show();
  }
});

document.getElementById("btnwithdraw").addEventListener("click", () => {
  if (window.innerWidth < 768) {
    new bootstrap.Offcanvas("#withdrawlOffcanvas").show();
  } else {
    new bootstrap.Modal("#withdrawModal").show();
  }
});



document.getElementById("btnBuycard").addEventListener("click", () => {
  if (window.innerWidth < 768) {
    new bootstrap.Offcanvas("#buyCardOffcanvas").show();
  } else {
    new bootstrap.Modal("#buyCardModal").show();
  }
});

</script>


<script>
function redeemCard(source) {
  // prevent form submission reload if inside a form
  event.preventDefault();

  // pick the right input based on source
  const code = document.getElementById(`redeemcode${source}`).value.trim();

  if (!code) {
    showAlert("warning", "Redeem Failed", "Please enter a redeem code");
    return;
  }

  const fd = new FormData();
  fd.append("action", "redeem");
  fd.append("card_code", code);

  fetch("/req.php", {
    method: "POST",
    body: fd
  })
  .then(res => res.json()) // expect JSON from backend
  .then(data => {
    console.log(data);
    
    if (data.success) {
      showAlert("success", "Redeem Successful", data.message, () => {
        window.location.href = "/wallet.php";
      });
    } else {
      showAlert("error", "Redeem Failed", data.message);
    }
  })
  .catch(err => {
    console.error("Error:", err);
    showAlert("error", "Network Error", "Something went wrong redeeming the code.");
  });
}
</script>



<script>
function submitTransfer(source) {
  showLoading();

  const recipient = document.getElementById(`recipientSelect${source}`).value.split('/')[0].trim();
  const walletId = document.getElementById(`walletId${source}`).value;
  const amount = document.getElementById(`amount${source}`).value;

  const fd = new FormData();
  fd.append("recipient_id", recipient);
  fd.append("wallet_id", walletId);
  fd.append("amount", amount);
  fd.append("action", "transfer");

  fetch("/req.php", { method: "POST", body: fd })
    .then(res => res.json())
    .then(
        data => {
            if(data.success){
                showTransferAlert(data.message, "success");
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }
            else{
                 showTransferAlert(data.message, "danger");
            }
        }
    )
    .catch(() => alert("Failed to submit transfer."))
    .finally(() => hideLoading());
}


const m = document.getElementById("submitTransferMobile");
if (m) m.addEventListener("click", () => submitTransfer("Mobile"));

const d = document.getElementById("submitTransferDesktop");
if (d) d.addEventListener("click", () => submitTransfer("Desktop"));
</script>
</body>
</html>
