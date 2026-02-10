<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__."/../main-function.php"; ?>
    <?php 
        $main = new main($conn);
        if($main->isLoggedIn() === false) header('location:/');
        $userData = $main->getUserData()['data'];
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transfer | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-bg: #f8f9fa;
        }

        body { background-color: var(--mflow-bg); font-family: 'Segoe UI', sans-serif; }

        .transfer-card {
            background: #fff; border-radius: 20px; padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .account-box {
            background: #f0f2f5; border-radius: 12px; padding: 20px;
            position: relative; border: 1px dashed #ccc;
        }

        .copy-btn {
            position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
            background: var(--mflow-blue); color: #fff; border: none;
            padding: 5px 12px; border-radius: 8px; font-size: 12px;
        }

        .upload-zone {
            border: 2px dashed #ddd; border-radius: 15px;
            padding: 40px 20px; text-align: center;
            cursor: pointer; transition: 0.3s;
            background: #fff;
        }
        .upload-zone:hover { border-color: var(--mflow-blue); background: #f0f7ff; }

        .timer-badge {
            background: #fff5f5; color: #e53e3e;
            padding: 8px 15px; border-radius: 20px;
            font-weight: bold; font-size: 14px;
            display: inline-flex; align-items: center; gap: 8px;
        }

        #preview-img {
            max-width: 100%; border-radius: 10px; display: none; margin-top: 15px;
        }
    </style>
</head>
<body>

<header class="bg-white border-bottom p-3 d-flex align-items-center sticky-top">
    <a href="/buy" class="text-dark me-3"><i class="ri-arrow-left-line fs-4"></i></a>
    <h5 class="mb-0 fw-bold">Manual Bank Transfer</h5>
</header>

<div class="container py-4">
    <div class="text-center mb-4">
        <div class="timer-badge mb-3">
            <i class="ri-time-line"></i> Expires in <span id="timer">29:59</span>
        </div>
        <h4 class="fw-bold">₦25,000.00</h4>
        <p class="text-secondary small">Transfer exactly the amount above to the account below</p>
    </div>

    <div class="transfer-card mb-4">
        <h6 class="fw-bold mb-3 text-muted">PAYMENT DETAILS</h6>
        
        <div class="mb-3">
            <small class="text-muted d-block">Bank Name</small>
            <div class="fw-bold fs-5">Wema Bank / monieFlow</div>
        </div>

        <div class="mb-3">
            <small class="text-muted d-block">Account Number</small>
            <div class="account-box">
                <span class="fw-bold fs-4" id="accNum">0123456789</span>
                <button class="copy-btn" onclick="copyText()">COPY</button>
            </div>
        </div>

        <div class="mb-0">
            <small class="text-muted d-block">Reference Code</small>
            <div class="fw-bold text-primary">MF-TRX-<?= strtoupper(substr(md5(time()), 0, 8)) ?></div>
        </div>
    </div>

    <div class="mb-4">
        <h6 class="fw-bold mb-3">Upload Receipt</h6>
        <input type="file" id="fileInput" hidden accept="image/*" onchange="handleUpload(this)">
        <div class="upload-zone" onclick="document.getElementById('fileInput').click()">
            <i class="ri-upload-cloud-2-line fs-1 text-primary"></i>
            <p class="mb-0 mt-2 fw-bold">Tap to upload proof of payment</p>
            <small class="text-muted">Supports JPG, PNG or PDF</small>
            <img id="preview-img" src="">
        </div>
    </div>

    <button class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow" onclick="submitTransfer()">
        Confirm I've Paid
    </button>
</div>

<script>
    // Timer Logic
    let time = 1799;
    const timerEl = document.getElementById('timer');
    setInterval(() => {
        let mins = Math.floor(time / 60);
        let secs = time % 60;
        timerEl.innerHTML = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
        if(time > 0) time--;
    }, 1000);

    // Copy Function
    function copyText() {
        const text = document.getElementById('accNum').innerText;
        navigator.clipboard.writeText(text);
        alert("Account number copied!");
    }

    // Image Preview Logic
    function handleUpload(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview-img');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function submitTransfer() {
        const file = document.getElementById('fileInput').files[0];
        if(!file) {
            alert("Please upload your receipt first.");
            return;
        }
        alert("Receipt submitted! Our team will verify it within 10-30 minutes.");
        window.location.href = "/wallet";
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>