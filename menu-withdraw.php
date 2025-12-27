<div class="offcanvas offcanvas-bottom" id="withdrawlOffcanvas">
  <div class="offcanvas-header border-bottom">
    <h5 class="m-0">Redeem Funds</h5>
    <button class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form>
      <div class="mb-3">
        <div class="text-center"><label class="text-center mx-auto">Redeem code</label></div>
        <input type="text" id="redeemcode1" class="form-control text-center" placeholder="XXXX-XXXX-XXXX-XXXX">
      </div>
      <button class="btn btn-success w-100 fw-bold" onclick="redeemCard(1)">Send Code</button>
    </form>
  </div>
</div>

<div class="modal fade" id="withdrawModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
         <h5 class="my-2">Redeem Funds</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
            <div class="text-center"><label class="text-center mx-auto">Redeem code</label></div>
            <input type="text" id="redeemcode2" class="form-control text-center" placeholder="XXXX-XXXX-XXXX-XXXX">
          </div>
          <button class="btn btn-success w-100 fw-bold" onclick="redeemCard(2)">Send Code</button>
      </div>
    </div>
  </div>
</div>