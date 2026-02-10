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
    <title>Security | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-dark: #000000;
        }

        body { 
            background: radial-gradient(circle at top right, #1e1e1e, #000000);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .passkey-card {
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
        }

        .auth-icon-wrapper {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, var(--mflow-blue), #00d2ff);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 25px;
            font-size: 40px;
            box-shadow: 0 10px 20px rgba(24, 119, 242, 0.3);
        }

        .fingerprint-btn {
            background: none; border: 2px solid var(--mflow-blue);
            color: #fff; width: 70px; height: 70px; border-radius: 50%;
            font-size: 30px; margin-top: 30px; transition: 0.3s;
        }
        .fingerprint-btn:hover { background: var(--mflow-blue); transform: scale(1.1); }

        .pin-dot-container {
            display: flex; justify-content: center; gap: 15px; margin-top: 30px;
        }
        .pin-dot {
            width: 15px; height: 15px; border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: 0.2s;
        }
        .pin-dot.active { background: var(--mflow-blue); box-shadow: 0 0 10px var(--mflow-blue); }

        .keyboard-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 15px; margin-top: 40px;
        }
        .num-key {
            padding: 15px; font-size: 20px; font-weight: 600; cursor: pointer;
            border-radius: 15px; transition: 0.1s;
        }
        .num-key:active { background: rgba(255,255,255,0.1); }
    </style>
</head>
<body>

<div class="passkey-card">
    <div class="auth-icon-wrapper">
        <i class="ri-shield-user-fill"></i>
    </div>
    
    <h4 class="fw-bold">Security Required</h4>
    <p class="text-secondary small">Authenticate to complete your monieCoin transfer to <b>Jack</b></p>

    <div class="pin-dot-container">
        <div class="pin-dot" id="dot-1"></div>
        <div class="pin-dot" id="dot-2"></div>
        <div class="pin-dot" id="dot-3"></div>
        <div class="pin-dot" id="dot-4"></div>
    </div>

    <div class="keyboard-grid">
        <div class="num-key" onclick="pressKey(1)">1</div>
        <div class="num-key" onclick="pressKey(2)">2</div>
        <div class="num-key" onclick="pressKey(3)">3</div>
        <div class="num-key" onclick="pressKey(4)">4</div>
        <div class="num-key" onclick="pressKey(5)">5</div>
        <div class="num-key" onclick="pressKey(6)">6</div>
        <div class="num-key" onclick="pressKey(7)">7</div>
        <div class="num-key" onclick="pressKey(8)">8</div>
        <div class="num-key" onclick="pressKey(9)">9</div>
        <div class="num-key"></div>
        <div class="num-key" onclick="pressKey(0)">0</div>
        <div class="num-key" onclick="backspace()"><i class="ri-backspace-line"></i></div>
    </div>

    <button class="fingerprint-btn" onclick="startBiometric()">
        <i class="ri-fingerprint-fill"></i>
    </button>
    <div class="mt-3 small text-secondary">Use Biometrics</div>
</div>



<script>
    let pin = "";
    
    function pressKey(num) {
        if(pin.length < 4) {
            pin += num;
            updateDots();
        }
        
        if(pin.length === 4) {
            // Validate PIN logic
            setTimeout(() => {
                alert("Identity Verified!");
                window.location.href = "/wallet";
            }, 300);
        }
    }

    function backspace() {
        pin = pin.slice(0, -1);
        updateDots();
    }

    function updateDots() {
        for(let i = 1; i <= 4; i++) {
            const dot = document.getElementById(`dot-${i}`);
            if(i <= pin.length) dot.classList.add('active');
            else dot.classList.remove('active');
        }
    }

    function startBiometric() {
        // Trigger native device biometric prompt
        alert("Scanning Fingerprint/Face...");
    }
</script>

</body>
</html>