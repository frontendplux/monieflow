<!DOCTYPE html>
<html lang="en">
  <?php 
include __dir__."/config/function.php"; 
$main=new Main($conn);
if($main->isLoggedin()) {
  header('location:/member/index.php');
return;
}
elseif(!isset($_SESSION['email'])){
  header('location:/');
  return;
}
?>
<head>
  <meta charset="UTF-8">
  <title>Find Your Account – Monieflow</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="/libery.js"></script>
</head>
<body class="bg-light">

<div class="bg-light vh-100">
  <!-- Header -->
  <header class="text-capitalize bg-white d-flex justify-content-between align-items-center px-3 p-2 shadow-sm">
    <h1 class="m-0 fs-4">monieflow</h1>
    <div>
      <a href="/" class="btn btn-success">log in</a>
    </div>
  </header>

  <!-- Card -->
  <div class="p-3">
    <div class="col-12 col-md-4 mx-auto my-1 my-sm-5 bg-white rounded shadow-sm">
    <div class="p-3 fw-bold fs-4">confirm Your Account</div>
    <hr>

    <p class="px-3 fs-5">
      Please enter the passcode mailed to you : <?= $_SESSION['email'] ?>
    </p>

    <div>
      <div id="err"></div>
      <div class="my-3 px-3 position-relative">
        <span class="position-absolute top-0 start-0 mt-3 ms-4 ps-2 fw-bold">MF-</span>
        <input
          type="number" 
          inputmode="numeric"
          name="identifier"
          class="form-control p-3 ps-5"
          placeholder="000000"
          id="passcode"
          required
        >
      </div>

      <hr>

      <div class="text-end my-3 pb-4 px-3">
        <a href="/" class="btn btn-light px-4 me-2">Cancel</a>
        <button onclick="cc()" type="submit" class="btn btn-primary px-4">
          Send Passcode
        </button>
      </div>
    </div>
  </div>
  </div>
</div>
</body>
</html>
<script>
  async function cc(){
    showLoading();

    const passcode = document.getElementById('passcode').value.trim();
    const err = document.getElementById('err');
    err.innerHTML = ""; // clear old error
    if(!passcode){
      err.innerHTML = `
        <div class="alert alert-danger mx-3 fw-medium">
          Invalid passcode
        </div>`;
      hideLoading();
      return;
    }

    try {
      const req = await postfileItem('/pages/req.php', {
        action: 'cp',
        code: passcode
      });

      console.log(req);

      if(req && req.success){
        window.location.href = '/member/index.php';
      } else {
        err.innerHTML = `
          <div class="alert alert-danger mx-3 fw-medium">
            ${req?.message || 'Something went wrong'}
          </div>`;
      }

    } catch (e) {
      console.error(e);
      err.innerHTML = `
        <div class="alert alert-danger mx-3 fw-medium">
          Network error. Please try again.
        </div>`;
    }

    hideLoading();
  }
</script>
