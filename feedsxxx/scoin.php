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
    <title>Send Coins | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-dark: #1a1a1a;
            --fb-bg: #f0f2f5;
        }

        body { background-color: #fff; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }

        /* Search & User Selection */
        .search-box {
            background: #f0f2f5; border: none; border-radius: 12px; padding: 12px 20px; width: 100%;
        }

        .contact-circle {
            width: 65px; text-align: center; cursor: pointer; transition: 0.2s;
        }
        .contact-circle:hover { transform: scale(1.1); }
        .contact-img {
            width: 55px; height: 55px; border-radius: 50%; border: 2px solid transparent; padding: 2px;
        }
        .contact-circle.selected .contact-img { border-color: var(--mflow-blue); background: #fff; }

        /* Amount Input */
        .amount-display {
            font-size: 4rem; font-weight: 800; color: var(--mflow-dark); text-align: center;
            margin: 40px 0; display: flex; align-items: center; justify-content: center;
        }
        .currency-label { font-size: 1.5rem; color: #65676b; margin-right: 10px; }

        /* Keypad */
        .keypad-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
            max-width: 320px; margin: 0 auto;
        }
        .key-btn {
            height: 60px; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 600; border-radius: 50%;
            cursor: pointer; transition: 0.1s;
        }
        .key-btn:active { background: #f0f2f5; }

        .send-btn {
            background: var(--mflow-blue); color: #fff; border: none;
            width: 100%; padding: 18px; border-radius: 15px; font-weight: 800;
            font-size: 1.1rem; margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="/wallet" class="text-dark"><i class="ri-close-line fs-3"></i></a>
        <h5 class="mb-0 fw-bold">Send monieCoin</h5>
        <div style="width: 30px;"></div>
    </div>

    <div class="mb-4">
        <input type="text" class="search-box mb-3" placeholder="Search name or @username">
        
        <label class="small fw-bold text-muted mb-2 d-block">RECENT CONTACTS</label>
        <div class="d-flex gap-3 overflow-auto pb-2 no-scrollbar">
            <div class="contact-circle selected">
                <img src="https://i.pravatar.cc/150?u=jack" class="contact-img">
                <div class="small fw-bold mt-1">Jack</div>
            </div>
            <?php for($i=1; $i<6; $i++): ?>
            <div class="contact-circle">
                <img src="https://i.pravatar.cc/150?random=<?= $i ?>" class="contact-img">
                <div class="small text-muted mt-1">User <?= $i ?></div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="amount-display">
        <span class="currency-label">MC</span>
        <span id="amount">0</span>
    </div>
    
    <div class="text-center text-muted small mb-4">
        Balance: 12,450.80 MC
    </div>

    <div class="keypad-grid">
        <div class="key-btn" onclick="addNum('1')">1</div>
        <div class="key-btn" onclick="addNum('2')">2</div>
        <div class="key-btn" onclick="addNum('3')">3</div>
        <div class="key-btn" onclick="addNum('4')">4</div>
        <div class="key-btn" onclick="addNum('5')">5</div>
        <div class="key-btn" onclick="addNum('6')">6</div>
        <div class="key-btn" onclick="addNum('7')">7</div>
        <div class="key-btn" onclick="addNum('8')">8</div>
        <div class="key-btn" onclick="addNum('9')">9</div>
        <div class="key-btn" onclick="addNum('.')">.</div>
        <div class="key-btn" onclick="addNum('0')">0</div>
        <div class="key-btn" onclick="delNum()"><i class="ri-backspace-line"></i></div>
    </div>

    <button class="send-btn" data-bs-toggle="offcanvas" data-bs-target="#confirmSheet">
        Continue
    </button>
</div>

<div class="offcanvas offcanvas-bottom" tabindex="-1" id="confirmSheet" style="height: auto; border-top-left-radius: 25px; border-top-right-radius: 25px;">
    <div class="offcanvas-body p-4">
        <div class="text-center mb-4">
            <div class="bg-light rounded-pill mx-auto mb-3" style="width: 50px; height: 5px;"></div>
            <h5 class="fw-bold">Confirm Transfer</h5>
        </div>
        
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Sending to</span>
            <span class="fw-bold">Jack Dorsey (@jack)</span>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <span class="text-muted">Amount</span>
            <span class="fw-bold text-primary" id="final-amount">0 MC</span>
        </div>

        <div class="bg-light p-3 rounded-3 mb-4 d-flex align-items-center">
            <i class="ri-information-line text-primary me-2 fs-5"></i>
            <small class="text-muted">Coins are transferred instantly. This action cannot be undone.</small>
        </div>

        <button class="btn btn-primary w-100 py-3 fw-bold rounded-3" onclick="processSend()">
            Confirm & Send
        </button>
    </div>
</div>

<script>
    let currentAmount = "0";

    function addNum(num) {
        if (currentAmount === "0") currentAmount = num;
        else currentAmount += num;
        updateDisplay();
    }

    function delNum() {
        currentAmount = currentAmount.slice(0, -1);
        if (currentAmount === "") currentAmount = "0";
        updateDisplay();
    }

    function updateDisplay() {
        document.getElementById('amount').innerText = currentAmount;
        document.getElementById('final-amount').innerText = currentAmount + " MC";
    }

    function processSend() {
        alert("Transaction of " + currentAmount + " MC sent successfully!");
        window.location.href = "/wallet";
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>