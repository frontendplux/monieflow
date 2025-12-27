<?php 
include __DIR__."/headers/header.php"; 

$main = new main($conn);

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

<?php if (count($main->fetchtransactionProducts()) > 0): ?>
  <!-- Notifications -->
  <div class="d-flex sticky-top bg-white py-3 justify-content-between">
    <h5 class="m-0"><i onclick="history.back()" class="ri-arrow-left-s-line me-2" style="cursor: pointer"></i>Recent Activity</h5>
    <!-- <a href="/transactions.php" class="text-capitalize">see all</a> -->
  </div>

  <div class="bg-white p-2 rounded">
  <?php foreach ($main->fetchtransactionProducts($data=0) as $tx): 
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
      <small class="text-muted">created At• <?= $tx['created_at'] ?></small>
    </a>
  <?php endforeach; ?>
  </div>
<?php endif; ?>
</div>


<?php include __DIR__."/menu-transfer.php"; ?>
<?php include __DIR__."/menu-withdraw.php"; ?>


<?php include __DIR__."/headers/footer.php"; ?>
