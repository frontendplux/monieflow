<!-- SIGN IN MODAL -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">

      <div class="modal-header d-block">
        <h2 class="modal-title fs-6 text-uppercase" id="staticBackdropLabel">sign in / register</h2>
        <div class="text-secondary">Welcome to Monieflow!</div>
        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> -->
      </div>

      <div class="modal-body border-bottom-0">
        <input type="email" id="loginEmail" class="form-control text-center p-3" placeholder="Enter Email Address">
        <small id="emailError" class="text-danger d-none">Invalid email</small>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary text-uppercase w-100 p-3" onclick="login()">continue</button>
      </div>

    </div>
  </div>
</div>