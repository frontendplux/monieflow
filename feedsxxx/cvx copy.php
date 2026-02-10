<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Your Gift | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --pink: #ff4d6d;
            --gold: #ffcc00;
            --dark: #0f0f1a;
        }

        body {
            background: radial-gradient(circle at center, #1a1a2e 0%, var(--dark) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: white;
            font-family: 'Segoe UI', sans-serif;
        }

        /* The Gift Container */
        .gift-container {
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .gift-container:hover { transform: scale(1.05); }

        /* The Box Body */
        .box-body {
            width: 150px;
            height: 120px;
            background: var(--pink);
            border-radius: 10px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            z-index: 2;
        }

        /* Ribbon */
        .box-body::after {
            content: '';
            position: absolute;
            left: 55%; top: 0;
            width: 30px; height: 100%;
            background: var(--gold);
            transform: translateX(-50%);
        }

        /* The Lid */
        .box-lid {
            width: 170px;
            height: 40px;
            background: var(--pink);
            border-radius: 5px;
            position: absolute;
            top: -35px; left: -10px;
            z-index: 3;
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-bottom: 3px solid rgba(0,0,0,0.1);
        }

        /* Lid Ribbon */
        .box-lid::after {
            content: '';
            position: absolute;
            left: 55%; width: 30px; height: 100%;
            background: var(--gold);
            transform: translateX(-50%);
        }

        /* State: Open Animation */
        .opened .box-lid {
            transform: translateY(-150px) rotate(-20deg) scale(0.8);
            opacity: 0;
        }

        /* The Reward (Hidden inside) */
        .reward {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0);
            text-align: center;
            transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1;
            width: 300px;
        }

        .opened .reward {
            transform: translate(-50%, -180%) scale(1);
        }

        /* Coin Burst Animation */
        .coin {
            position: absolute;
            color: var(--gold);
            font-size: 24px;
            opacity: 0;
            z-index: 0;
        }

        .opened .coin { animation: burst 1s forwards ease-out; }

        @keyframes burst {
            0% { transform: translate(0, 0); opacity: 1; }
            100% { transform: translate(var(--x), var(--y)); opacity: 0; }
        }

        .btn-wallet {
            background: linear-gradient(45deg, var(--pink), #ff8fa3);
            border: none; border-radius: 50px;
            padding: 12px 30px; font-weight: 700;
            color: white; opacity: 0; transition: 0.5s;
        }

        .opened .btn-wallet { opacity: 1; margin-top: 20px; }
    </style>
</head>
<body>

<div class="text-center">
    <div id="instruction" class="mb-5">
        <h4 class="fw-bold">A gift from Jack Dorsey</h4>
        <p class="text-secondary">Tap the box to open your surprise!</p>
    </div>

    <div class="gift-container" id="giftBox" onclick="openGift()">
        <div id="coinContainer"></div>
        
        <div class="box-lid"></div>
        <div class="box-body"></div>

        <div class="reward">
            <h1 class="display-3 fw-bold text-warning mb-0">500.00</h1>
            <p class="fs-4 fw-bold">monieCoins</p>
            <p class="text-light italic small">"Happy Birthday! Enjoy the vibes! 🎂"</p>
            <button class="btn-wallet shadow" onclick="location.href='/wallet'">Add to Wallet</button>
        </div>
    </div>
</div>

<script>
    function openGift(){
        const box = document.getElementById('giftBox');
        const instruction = document.getElementById('instruction');
        
        if(box.classList.contains('opened')) return;

        box.classList.add('opened');
        instruction.style.opacity = '0';
        createCoins();
    }

    function createCoins() {
        const container = document.getElementById('coinContainer');
        for (let i = 0; i < 15; i++) {
            const coin = document.createElement('i');
            coin.className = 'ri-copper-coin-fill coin';
            
            // Random burst directions
            const x = (Math.random() - 0.5) * 400 + 'px';
            const y = (Math.random() - 0.5) * 400 + 'px';
            
            coin.style.setProperty('--x', x);
            coin.style.setProperty('--y', y);
            coin.style.left = '60px';
            coin.style.top = '40px';
            
            container.appendChild(coin);
        }
    }
</script>

</body>
</html> 