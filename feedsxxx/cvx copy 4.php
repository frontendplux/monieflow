<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Win or Lose | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --pink: #ff4d6d;
            --gold: #ffcc00;
            --grey: #6c757d;
            --bg: #050505;
        }

        body {
            background-color: var(--bg);
            height: 100vh;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            padding-bottom: 12vh;
        }

        /* The Box shivering with energy */
        .gift-wrapper { position: relative; z-index: 10; }
        .gift-box { position: relative; cursor: pointer; transition: 0.3s; }
        .gift-box:active { transform: scale(0.9); }
        
        .box-body {
            width: 140px; height: 110px;
            background: var(--pink); border-radius: 12px;
            position: relative;
            box-shadow: 0 0 40px rgba(255, 77, 109, 0.3);
        }

        .box-body::before {
            content: ''; position: absolute; left: 50%; width: 25px; height: 100%; 
            background: var(--gold); transform: translateX(-50%);
        }

        .box-lid {
            width: 156px; height: 35px;
            background: var(--pink); border-radius: 8px;
            position: absolute; top: -30px; left: -8px;
            z-index: 5; transition: 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .opened .box-lid { transform: translateY(-300px) rotate(45deg); opacity: 0; }

        /* UI Overlays */
        .status-overlay {
            position: absolute; bottom: 40px; left: 50%;
            transform: translateX(-50%) scale(0.5);
            text-align: center; width: 350px; opacity: 0;
            transition: 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: none;
        }

        .show-status { transform: translateX(-50%) translateY(-220px) scale(1); opacity: 1; pointer-events: auto; }

        .win-amount {
            font-size: 4.5rem; font-weight: 900;
            background: linear-gradient(to bottom, #fff, var(--gold));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .lose-icon { font-size: 5rem; color: var(--grey); }

        .glow-ring {
            position: absolute; width: 250px; height: 250px;
            background: radial-gradient(circle, var(--pink) 0%, transparent 70%);
            filter: blur(50px); opacity: 0.4;
            top: 50%; left: 50%; transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>

    <div class="text-center">
        
        <div class="status-overlay" id="winView">
            <span class="badge bg-warning text-dark mb-2">JACKPOT!</span>
            <h2 class="fw-bold">BIG WINNER</h2>
            <div class="win-amount">5,000</div>
            <p class="fw-bold text-warning">monieCoins</p>
            <button class="btn btn-warning w-100 py-3 rounded-pill fw-bold shadow mt-3" onclick="location.reload()">Claim & Share</button>
        </div>

        <div class="status-overlay" id="loseView">
            <div class="lose-icon"><i class="ri-emotion-sad-line"></i></div>
            <h2 class="fw-bold text-secondary">Empty Box!</h2>
            <p class="text-muted">No luck this time. But there's always a next drop!</p>
            <button class="btn btn-outline-secondary w-100 py-3 rounded-pill fw-bold mt-3" onclick="location.reload()">Try Next Time</button>
        </div>

        <div class="gift-wrapper" id="giftWrapper" onclick="playGame()">
            <div class="glow-ring" id="glow"></div>
            <div class="gift-box">
                <div class="box-lid"></div>
                <div class="box-body"></div>
            </div>
            <p class="mt-4 text-secondary small tracking-widest" id="hint">TAP TO OPEN</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <script>
        function playGame() {
            const wrapper = document.getElementById('giftWrapper');
            const hint = document.getElementById('hint');
            const glow = document.getElementById('glow');
            
            if(wrapper.classList.contains('opened')) return;
            wrapper.classList.add('opened');
            hint.style.opacity = '0';

            // --- THE LOGIC ---
            // Generates a number between 0 and 1. 
            // 0.4 means a 40% chance of winning.
            const result = Math.random(); 
            const winProbability = 0.5; // 50/50 Chance

            setTimeout(() => {
                if (result < winProbability) {
                    // WIN
                    document.getElementById('winView').classList.add('show-status');
                    glow.style.background = "radial-gradient(circle, var(--gold) 0%, transparent 70%)";
                    triggerConfetti();
                } else {
                    // LOSE
                    document.getElementById('loseView').classList.add('show-status');
                    glow.style.background = "radial-gradient(circle, #333 0%, transparent 70%)";
                }
            }, 400);
        }

        function triggerConfetti() {
            var end = Date.now() + (2 * 1000);
            (function frame() {
              confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#ffcc00', '#fff'] });
              confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#ffcc00', '#fff'] });
              if (Date.now() < end) requestAnimationFrame(frame);
            }());
        }
    </script>
</body>
</html>