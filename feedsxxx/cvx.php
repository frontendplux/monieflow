<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Transformation | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --pink: #ff4d6d;
            --gold: #ffcc00;
            --bg: #050505;
        }

        body {
            background-color: var(--bg);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: white;
            font-family: 'Segoe UI', sans-serif;
        }

        .stage {
            position: relative;
            width: 350px;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- BOX STYLE --- */
        .gift-box {
            position: absolute;
            cursor: pointer;
            transition: all 0.6s cubic-bezier(0.6, -0.28, 0.735, 0.045);
            z-index: 10;
        }

        .box-body {
            width: 130px;
            height: 110px;
            background: var(--pink);
            border-radius: 12px;
            position: relative;
            box-shadow: 0 15px 40px rgba(255, 77, 109, 0.4);
        }

        .box-body::after {
            content: '';
            position: absolute;
            left: 50%;
            width: 25px;
            height: 100%;
            background: var(--gold);
            transform: translateX(-50%);
        }

        /* WIN: Box shrinking and spinning away */
        .box-disappear {
            transform: scale(0) rotate(360deg);
            opacity: 0;
            pointer-events: none;
        }

        /* --- TROPHY STYLE --- */
        .trophy-view {
            position: absolute;
            transform: scale(0);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
            z-index: 5;
        }

        .trophy-view.active {
            transform: scale(1);
            opacity: 1;
        }

        .trophy-icon {
            font-size: 110px;
            color: var(--gold);
            filter: drop-shadow(0 0 30px rgba(255, 204, 0, 0.6));
            animation: float 2s infinite ease-in-out;
            display: block;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .halo {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, var(--gold) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.3;
            z-index: -1;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.2; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.4; }
            100% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.2; }
        }

        /* --- LOSE STYLE --- */
        .lose-view {
            position: absolute;
            transform: scale(0.5);
            opacity: 0;
            transition: 0.5s ease;
            text-align: center;
        }

        .lose-view.active {
            transform: scale(1);
            opacity: 1;
        }

        .btn-claim {
            background: var(--gold);
            color: #000;
            border: none;
            padding: 12px 35px;
            border-radius: 50px;
            font-weight: 800;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn-claim:hover { transform: scale(1.1); }
    </style>
</head>
<body>

    <div class="stage">
        <div class="gift-box" id="mainBox" onclick="startSequence()">
            <div class="box-body"></div>
            <p class="mt-4 text-secondary small tracking-widest text-center">TAP TO REVEAL</p>
        </div>

        <div class="trophy-view" id="winContent">
            <div class="halo"></div>
            <i class="ri-trophy-fill trophy-icon"></i>
            <h2 class="fw-bold mt-2">YOU WON!</h2>
            <h1 class="display-4 fw-bold text-warning">5,000</h1>
            <p class="text-white-50">monieCoins added to vault</p>
            <button class="btn-claim" onclick="location.reload()">COLLECT TROPHY</button>
        </div>

        <div class="lose-view" id="loseContent">
            <i class="ri-ghost-smile-line text-secondary" style="font-size: 80px;"></i>
            <h2 class="fw-bold text-secondary mt-3">Empty!</h2>
            <p class="text-muted small">The box was a decoy.<br>Try your luck again tomorrow.</p>
            <button class="btn btn-outline-secondary rounded-pill mt-3" onclick="location.reload()">TRY AGAIN</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <script>
        function startSequence() {
            const box = document.getElementById('mainBox');
            const winView = document.getElementById('winContent');
            const loseView = document.getElementById('loseContent');

            // 1. Roll the dice (Change 0.5 to 0.9 for 90% win rate while testing)
            const isWinner = Math.random() < 0.5;

            // 2. Make box disappear
            box.classList.add('box-disappear');

            // 3. Show the result after the box shrinks
            setTimeout(() => {
                if(isWinner) {
                    winView.classList.add('active');
                    runConfetti();
                } else {
                    loseView.classList.add('active');
                }
            }, 600);
        }

        function runConfetti() {
            var duration = 3 * 1000;
            var end = Date.now() + duration;

            (function frame() {
              confetti({
                particleCount: 5,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: ['#ffcc00', '#ffffff', '#ff4d6d']
              });
              confetti({
                particleCount: 5,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: ['#ffcc00', '#ffffff', '#ff4d6d']
              });

              if (Date.now() < end) {
                requestAnimationFrame(frame);
              }
            }());
        }
    </script>

</body>
</html>