<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Passcode | SocialFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(45deg, #6366f1, #a855f7);
        }

        body {
            background: #f3f4f7;
            background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.1) 0, transparent 50%), 
                              radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.1) 0, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .passcode-container {
            text-align: center;
            max-width: 350px;
            width: 100%;
            padding: 2rem;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 1.5rem;
            border: 3px solid white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        /* Passcode Dot Indicators */
        .dots-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 2.5rem 0;
        }

        .dot {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dot.active {
            background: var(--primary-gradient);
            border-color: transparent;
            transform: scale(1.2);
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.4);
        }

        /* Numpad Styling */
        .numpad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .num-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: none;
            background: white;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: all 0.1s;
            cursor: pointer;
        }

        .num-btn:active {
            transform: scale(0.9);
            background: #f1f5f9;
        }

        .num-btn.special {
            background: transparent;
            box-shadow: none;
            font-size: 1.2rem;
            color: #64748b;
        }

        .error-shake {
            animation: shake 0.4s focus;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
    </style>
</head>
<body>

<div class="passcode-container">
    <img src="https://i.pravatar.cc/150?u=me" class="user-avatar" alt="User">
    <h5 class="fw-bold mb-1">Enter Passcode</h5>
    <p class="text-muted small">Confirm it's you to continue</p>

    <div class="dots-container" id="dotsContainer">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>

    <input type="password" id="passcodeHidden" maxlength="5" pattern="\d*" inputmode="numeric" style="opacity: 0; position: absolute; pointer-events: none;">

    <div class="numpad">
        <button class="num-btn" onclick="press(1)">1</button>
        <button class="num-btn" onclick="press(2)">2</button>
        <button class="num-btn" onclick="press(3)">3</button>
        <button class="num-btn" onclick="press(4)">4</button>
        <button class="num-btn" onclick="press(5)">5</button>
        <button class="num-btn" onclick="press(6)">6</button>
        <button class="num-btn" onclick="press(7)">7</button>
        <button class="num-btn" onclick="press(8)">8</button>
        <button class="num-btn" onclick="press(9)">9</button>
        <button class="num-btn special"><i class="ri-fingerprint-line"></i></button>
        <button class="num-btn" onclick="press(0)">0</button>
        <button class="num-btn special" onclick="del()"><i class="ri-backspace-line"></i></button>
    </div>

    <div class="mt-4">
        <a href="#" class="text-decoration-none small fw-bold text-primary">Forgot Passcode?</a>
    </div>
</div>

<script>
    let code = "";
    const maxLen = 5;
    const dots = document.querySelectorAll('.dot');
    const container = document.querySelector('.passcode-container');

    function press(num) {
        if (code.length < maxLen) {
            code += num;
            updateDots();
            
            if (code.length === maxLen) {
                verify();
            }
        }
    }

    function del() {
        code = code.slice(0, -1);
        updateDots();
    }

    function updateDots() {
        dots.forEach((dot, index) => {
            if (index < code.length) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function verify() {
        // Example verification logic
        if (code === "12345") {
            alert("Success! Redirecting...");
        } else {
            // Shake effect on error
            container.classList.add('error-shake');
            setTimeout(() => {
                container.classList.remove('error-shake');
                code = "";
                updateDots();
            }, 500);
        }
    }
</script>

</body>
</html>