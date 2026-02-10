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
    <title>Buy monieCoins | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-gold: #ffcc00;
            --fb-bg: #f0f2f5;
        }

        body { background-color: var(--fb-bg); font-family: 'Segoe UI', sans-serif; }

        /* Package Cards */
        .coin-package {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            border: 2px solid transparent;
            transition: 0.3s;
            cursor: pointer;
            position: relative;
        }

        .coin-package:hover { border-color: var(--mflow-blue); transform: translateY(-5px); }
        .coin-package.popular { border-color: var(--mflow-gold); }
        
        .popular-badge {
            position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
            background: var(--mflow-gold); color: #000;
            padding: 2px 15px; border-radius: 20px; font-size: 12px; font-weight: 800;
        }

        .coin-icon { font-size: 40px; color: var(--mflow-gold); margin-bottom: 10px; }
        .price-tag { font-size: 1.2rem; font-weight: 800; color: #000; }
        
        /* Custom Amount Input */
        .custom-amount-box {
            background: #fff; border-radius: 15px; padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Payment Methods */
        .method-btn {
            background: #fff; border: 1px solid #ddd; border-radius: 12px;
            padding: 15px; display: flex; align-items: center; width: 100%;
            margin-bottom: 10px; transition: 0.2s;
        }
        .method-btn:hover { background: #f8f9fa; border-color: var(--mflow-blue); }
        .method-btn i { font-size: 24px; margin-right: 15px; color: var(--mflow-blue); }
    </style>
</head>
<body>

<header class="bg-white border-bottom p-3 d-flex align-items-center">
    <a href="/wallet" class="text-dark me-3"><i class="ri-arrow-left-line fs-4"></i></a>
    <h5 class="mb-0 fw-bold">Top Up Coins</h5>
</header>

<div class="container py-4">
    <div class="text-center mb-5">
        <h3 class="fw-bold">Select a Package</h3>
        <p class="text-secondary">Get monieCoins to tip creators or buy in the market</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-6 col-md-4">
            <div class="coin-package" onclick="selectPkg(500, 25000)">
                <div class="coin-icon"><i class="ri-copper-coin-fill"></i></div>
                <div class="fw-bold">500 MC</div>
                <div class="price-tag mt-2">₦25,000</div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="coin-package popular shadow-sm" onclick="selectPkg(2000, 95000)">
                <div class="popular-badge">BEST VALUE</div>
                <div class="coin-icon"><i class="ri-coins-fill"></i></div>
                <div class="fw-bold">2,000 MC</div>
                <div class="price-tag mt-2">₦95,000</div>
                <small class="text-success fw-bold">Save 5%</small>
            </div>
        </div>

        <div class="col-6 col-md-4 mx-auto">
            <div class="coin-package" onclick="selectPkg(5000, 230000)">
                <div class="coin-icon"><i class="ri-stack-fill"></i></div>
                <div class="fw-bold">5,000 MC</div>
                <div class="price-tag mt-2">₦230,000</div>
            </div>
        </div>
    </div>

    <div class="custom-amount-box mb-5">
        <label class="small fw-bold text-muted mb-2">CUSTOM AMOUNT</label>
        <div class="d-flex align-items-center border-bottom pb-2">
            <span class="fs-4 fw-bold me-2">MC</span>
            <input type="number" id="customCoin" class="form-control border-0 fs-4 fw-bold p-0" placeholder="0" oninput="calcPrice(this.value)">
        </div>
        <div class="mt-2 small text-muted">You will pay: <span id="customNaira" class="fw-bold text-dark">₦0</span></div>
    </div>

    <h6 class="fw-bold mb-3">Payment Method</h6>
    <button class="method-btn" onclick="checkout('Card')">
        <i class="ri-bank-card-line"></i>
        <div class="text-start">
            <div class="fw-bold">Credit or Debit Card</div>
            <div class="small text-muted">Visa, Mastercard, Verve</div>
        </div>
        <i class="ri-arrow-right-s-line ms-auto text-muted"></i>
    </button>

    <button class="method-btn" onclick="checkout('Transfer')">
        <i class="ri-bank-line"></i>
        <div class="text-start">
            <div class="fw-bold">Bank Transfer</div>
            <div class="small text-muted">Instant confirmation</div>
        </div>
        <i class="ri-arrow-right-s-line ms-auto text-muted"></i>
    </button>
</div>



<script>
    const RATE = 50; // 1 MC = 50 Naira

    function selectPkg(coins, price) {
        alert(`Redirecting to payment for ${coins} monieCoins (₦${price.toLocaleString()})`);
    }

    function calcPrice(val) {
        const total = val * RATE;
        document.getElementById('customNaira').innerText = "₦" + total.toLocaleString();
    }

    function checkout(method) {
        alert(`Opening Paystack/Flutterwave for ${method} payment...`);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>