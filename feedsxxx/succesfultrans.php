<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Status | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .status-card { background: #fff; border-radius: 24px; padding: 40px; width: 100%; max-width: 400px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        .icon-circle { width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 45px; margin: 0 auto 25px; }
        
        /* Success Theme */
        .status-success .icon-circle { background: #e7f6ed; color: #28a745; }
        .status-success .btn-main { background: #28a745; color: #fff; border: none; }
        
        /* Failed Theme */
        .status-failed .icon-circle { background: #fdeaea; color: #dc3545; }
        .status-failed .btn-main { background: #dc3545; color: #fff; border: none; }
        
        /* Pending Theme */
        .status-pending .icon-circle { background: #fff4e5; color: #ff9800; }
        .status-pending .btn-main { background: #ff9800; color: #fff; border: none; }

        .btn-main { width: 100%; padding: 14px; border-radius: 12px; font-weight: 700; margin-top: 20px; transition: 0.3s; }
        .btn-main:hover { opacity: 0.9; transform: translateY(-2px); }
        .btn-outline { width: 100%; padding: 14px; border-radius: 12px; font-weight: 700; color: #65676b; border: 1px solid #ddd; margin-top: 10px; background: none; text-decoration: none; display: block; }
    </style>
</head>
<body>

<div class="status-card status-success" id="success-view">
    <div class="icon-circle"><i class="ri-checkbox-circle-fill"></i></div>
    <h3 class="fw-bold">Payment Successful</h3>
    <p class="text-secondary">Your wallet has been credited with <b>2,000 MC</b> instantly. Happy spending!</p>
    <div class="bg-light p-3 rounded-3 my-4">
        <div class="d-flex justify-content-between small">
            <span class="text-muted">Transaction ID</span>
            <span class="fw-bold">MF-99283471</span>
        </div>
    </div>
    <button class="btn-main" onclick="location.href='/wallet'">View Wallet</button>
    <a href="/home" class="btn-outline">Back to Home</a>
</div>

<div class="status-card status-failed d-none" id="failed-view">
    <div class="icon-circle"><i class="ri-error-warning-fill"></i></div>
    <h3 class="fw-bold">Payment Failed</h3>
    <p class="text-secondary">Your bank declined the transaction. No funds were deducted from your account.</p>
    <button class="btn-main" onclick="location.href='/buy'">Try Again</button>
    <a href="/wallet" class="btn-outline">Go to Wallet</a>
</div>

<div class="status-card status-pending d-none" id="pending-view">
    <div class="icon-circle"><i class="ri-time-fill"></i></div>
    <h3 class="fw-bold">Transfer Pending</h3>
    <p class="text-secondary">We've received your receipt! Our team is verifying the transfer. This usually takes 5-15 mins.</p>
    <div class="spinner-border text-warning my-3" role="status"></div>
    <button class="btn-main" onclick="location.href='/wallet'">Check History</button>
    <a href="/home" class="btn-outline">Home</a>
</div>

<script>
    // URL Logic: Use this to test different views (e.g., status.php?type=failed)
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');

    if(type === 'failed') {
        document.getElementById('success-view').classList.add('d-none');
        document.getElementById('failed-view').classList.remove('d-none');
    } else if(type === 'pending') {
        document.getElementById('success-view').classList.add('d-none');
        document.getElementById('pending-view').classList.remove('d-none');
    }
</script>

</body>
</html>