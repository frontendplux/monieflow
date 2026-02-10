<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surprise | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --pink: #ff4d6d;
            --gold: #ffcc00;
            --neon-blue: #00d2ff;
            --bg: #050505;
        }

        body {
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 50% 70%, rgba(255, 77, 109, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 20% 20%, rgba(0, 210, 255, 0.05) 0%, transparent 30%);
            height: 100vh;
            display: flex;
            align-items: flex-end; /* Moves content lower */
            justify-content: center;
            overflow: hidden;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            padding-bottom: 15vh; /* Precise positioning */
        }

        /* Ambient Floating Particles */
        .particle {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0.3;
            animation: float-up var(--d) linear infinite;
        }

        @keyframes float-up {
            0% { transform: translateY(0) scale(1); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: translateY(-100vh) scale(0); opacity: 0; }
        }

        /* The Box shivering with energy */
        .gift-wrapper {
            position: relative;
            z-index: 10;
        }

        .gift-box {
            position: relative;
            cursor: pointer;
            animation: shiver 2s infinite ease-in-out;
        }

        @keyframes shiver {
            0%, 100% { transform: rotate(-1deg) scale(1); }
            50% { transform: rotate(1deg) scale(1.02); }
        }

        .box-body {
            width: 140px; height: 110px;
            background: var(--pink);
            border-radius: 12px;
            position: relative;
            box-shadow: 0 20px 50px rgba(255, 77, 109, 0.4), inset 0 -10px 20px rgba(0,0,0,0.2);
        }

        .box-body::before { /* Ribbon Vertical */
            content: ''; position: absolute; left: 50%; top: 0;
            width: 25px; height: 100%; background: var(--gold);
            transform: translateX(-50%); box-shadow: 0 0 15px rgba(255, 204, 0, 0.3);
        }

        .box-lid {
            width: 156px; height: 35px;
            background: var(--pink);
            border-radius: 8px;
            position: absolute; top: -30px; left: -8px;
            z-index: 5;
            transition: all 0.7s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        /* Opened State */
        .opened .box-lid {
            transform: translateY(-250px) rotate(45deg) scale(0.5);
            opacity: 0;
        }

        /* Reward Appearance */
        .reward-reveal {
            position: absolute;
            bottom: 50px; left: 50%;
            transform: translateX(-50%) scale(0);
            text-align: center;
            transition: 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            width: 320px;
            opacity: 0;
        }

        .opened .reward-reveal {
            transform: translateX(-50%) translateY(-180px) scale(1);
            opacity: 1;
        }

        .amount {
            font-size: 4rem; font-weight: 900;
            background: linear-gradient(to bottom, #fff, var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 0 10px rgba(255, 204, 0, 0.5));
        }

        /* Glow Ring behind box */
        .glow-ring {
            position: absolute;
            width: 200px; height: 200px;
            background: var(--pink);
            filter: blur(80px);
            opacity: 0.4;
            border-radius: 50%;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            transition: 0.5s;
        }
        .opened .glow-ring { background: var(--gold); opacity: 0.6; transform: translate(-50%, -150%) scale(1.5); }

    </style>
</head>
<body>

    <div id="particles"></div>

    <div class="text-center">
        <div class="reward-reveal" id="reward">
            <h6 class="text-uppercase tracking-widest text-secondary mb-0">Gift Received</h6>
            <div class="amount">500.00</div>
            <p class="fw-bold text-white-50 mt-n2">monieCoins</p>
            
            <div class="card bg-white bg-opacity-10 border-0 rounded-4 p-3 mb-4">
                <small class="italic">"A little something for your great work this week. Keep it up! 🚀"</small>
            </div>

            <button class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow-lg" onclick="location.href='/wallet'">
                Collect & Continue
            </button>
        </div>

        <div class="gift-wrapper" id="giftWrapper" onclick="openBox()">
            <div class="glow-ring"></div>
            <div class="gift-box">
                <div class="box-lid"></div>
                <div class="box-body"></div>
            </div>
            <p class="mt-4 text-secondary small text-uppercase tracking-widest" id="hint">Tap to Unbox</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        // Create ambient particles
        const pContainer = document.getElementById('particles');
        for(let i=0; i<30; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.width = p.style.height = Math.random() * 5 + 'px';
            p.style.top = '100vh';
            p.style.setProperty('--d', (Math.random() * 5 + 5) + 's');
            pContainer.appendChild(p);
        }

        function openBox() {
            const wrapper = document.getElementById('giftWrapper');
            const hint = document.getElementById('hint');
            
            if(wrapper.classList.contains('opened')) return;

            wrapper.classList.add('opened');
            hint.style.display = 'none';

            // Premium Confetti Burst
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

            function randomInRange(min, max) {
              return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
              var timeLeft = animationEnd - Date.now();
              if (timeLeft <= 0) return clearInterval(interval);
              var particleCount = 50 * (timeLeft / duration);
              confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
              confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }
    </script>
</body>
</html>