<!-- Offcanvas (Mobile) -->
<div class="offcanvas offcanvas-bottom h-50" id="transferOffcanvas">
  <div class="offcanvas-header border-bottom">
    <h5 class="m-0">Transfer Funds</h5>
    <button class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body h-100">
      <div class="mb-3">
        <label>Wallet Account</label>
        <select id="recipientSelectMobile" class="form-control"
          onchange="document.getElementById('moniefont').innerHTML=`( Currency: ${this.value.split('/')[1]})`">
          <?php foreach ($main->wallet_balance() as $value): ?>
          <option value="<?= $value['id']; ?> / <?= currencyIdToSymbols($conn, $value['currency_id'])[0]?>">
            <?= currencyIdToSymbols($conn, $value['currency_id'])[1]; ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label>Wallet ID</label>
        <input type="number" id="walletIdMobile" class="form-control" placeholder="0000000000">
      </div>
      <div class="mb-3">
        <label>Amount <span id="moniefont">(<?= currencyIdToSymbols($conn, $main->wallet_balance()[0]['currency_id'])[0] ?>)</span></label>
        <input type="number" id="amountMobile" class="form-control" placeholder="0.00">
      </div>
      <button id="submitTransferMobile" class="btn btn-success w-100">Submit</button>
  </div>
</div>

<!-- Modal (Desktop) -->
<div class="modal fade" id="transferModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Transfer Funds</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Recipient ID</label>
          <select id="recipientSelectDesktop" class="form-control"
            onchange="document.getElementById('moniefontDesktop').innerHTML=`( Currency: ${this.value.split('/')[1]})`">
            <?php foreach ($main->wallet_balance() as $value): ?>
            <option value="<?= $value['id']; ?> / <?= currencyIdToSymbols($conn, $value['currency_id'])[0]?>">
              <?= currencyIdToSymbols($conn, $value['currency_id'])[1]; ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Wallet ID</label>
          <input type="number" id="walletIdDesktop" class="form-control" placeholder="0000000000">
        </div>
        <div class="mb-3">
          <label>Amount <span id="moniefontDesktop">(<?= currencyIdToSymbols($conn, $main->wallet_balance()[0]['currency_id'])[0] ?>)</span></label>
          <input type="number" id="amountDesktop" class="form-control" placeholder="0.00">
        </div>
        <button id="submitTransferDesktop" class="btn btn-success w-100">Submit</button>
      </div>
    </div>
  </div>
</div>

