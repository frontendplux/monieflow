<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lucky Win! | monieFlow</title>
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
            background-image: 
                radial-gradient(circle at 50% 80%, rgba(255, 204, 0, 0.15) 0%, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            padding-bottom: 12vh;
        }

        /* Screen Shake on Win */
        .shake-screen { animation: screen-shake 0.4s ease-in-out; }
        @keyframes screen-shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(5px); }
            50% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
            100% { transform: translateX(0); }
        }

        /* The Box shivering with energy */
        .gift-wrapper { position: relative; z-index: 10; }
        .gift-box { position: relative; cursor: pointer; animation: pulse 1.5s infinite; }
        
        @keyframes pulse {
            0% { transform: scale(1); filter: brightness(1); }
            50% { transform: scale(1.05); filter: brightness(1.2); }
            100% { transform: scale(1); filter: brightness(1); }
        }

        .box-body {
            width: 140px; height: 110px;
            background: var(--pink); border-radius: 12px;
            position: relative;
            box-shadow: 0 0 40px rgba(255, 77, 109, 0.3);
        }

        .box-body::before { /* Ribbon */
            content: ''; position: absolute; left: 50%; top: 0;
            width: 25px; height: 100%; background: var(--gold);
            transform: translateX(-50%);
        }

        .box-lid {
            width: 156px; height: 35px;
            background: var(--pink); border-radius: 8px;
            position: absolute; top: -30px; left: -8px;
            z-index: 5; transition: 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        /* Opened State */
        .opened .box-lid { transform: translateY(-300px) rotate(45deg); opacity: 0; }

        /* Reward Reveal Text */
        .reward-reveal {
            position: absolute; bottom: 40px; left: 50%;
            transform: translateX(-50%) scale(0.5);
            text-align: center; width: 350px; opacity: 0;
            transition: 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .opened .reward-reveal { transform: translateX(-50%) translateY(-220px) scale(1); opacity: 1; }

        .win-badge {
            background: var(--gold); color: #000;
            padding: 4px 15px; border-radius: 50px;
            font-size: 12px; font-weight: 900;
            display: inline-block; margin-bottom: 10px;
            animation: bounce 1s infinite;
        }

        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }

        .amount {
            font-size: 4.5rem; font-weight: 900;
            background: linear-gradient(to bottom, #fff, var(--gold), #ffa500);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1;
        }

        .glow-ring {
            position: absolute; width: 300px; height: 300px;
            background: radial-gradient(circle, var(--gold) 0%, transparent 70%);
            filter: blur(50px); opacity: 0;
            top: 50%; left: 50%; transform: translate(-50%, -50%);
            transition: 1s;
        }
        .opened .glow-ring { opacity: 0.4; transform: translate(-50%, -150%) scale(2); }
    </style>
</head>
<body>

    <div class="text-center">
        <div class="reward-reveal" id="reward">
            <div class="win-badge">LUCKY DROP!</div>
            <h5 class="text-white fw-bold mb-0">Congratulations!</h5>
            <p class="text-white-50 small">You just won a random reward</p>
            
            <div class="amount">5,000</div>
            <p class="fw-bold text-warning fs-5">monieCoins</p>
            
            <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-4 p-3 mb-4">
                <p class="small mb-0">"Holy moly! You're one of today's lucky winners. This has been added to your vault."</p>
            </div>

            <button class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow-lg" onclick="location.href='/wallet'">
                Claim My 5,000 MC
            </button>
        </div>

        <div class="gift-wrapper" id="giftWrapper" onclick="openBox()">
            <div class="glow-ring"></div>
            <div class="gift-box">
                <div class="box-lid"></div>
                <div class="box-body"></div>
            </div>
            <p class="mt-4 text-secondary small tracking-widest" id="hint">TAP TO REVEAL SURPRISE</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <script>
        function openBox() {
            const wrapper = document.getElementById('giftWrapper');
            const hint = document.getElementById('hint');
            
            if(wrapper.classList.contains('opened')) return;

            // Trigger animations
            wrapper.classList.add('opened');
            document.body.classList.add('shake-screen');
            hint.style.opacity = '0';

            // Massive Confetti Explosion
            var end = Date.now() + (2 * 1000);
            var colors = ['#ff4d6d', '#ffcc00', '#ffffff'];

            (function frame() {
              confetti({
                particleCount: 3,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: colors
              });
              confetti({
                particleCount: 3,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: colors
              });

              if (Date.now() < end) {
                requestAnimationFrame(frame);
              }
            }());
        }
    </script>
</body>
</html>