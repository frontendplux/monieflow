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
    <title>Gift monieCoins | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-pink: #ff4d6d;
            --mflow-gold: #ffcc00;
            --mflow-blue: #1877f2;
            --fb-bg: #f0f2f5;
        }

        body { background: #fff; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }

        /* Gift Theme Selector */
        .theme-card {
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            border: 2px solid transparent;
            background: #f8f9fa;
        }
        .theme-card.active {
            border-color: var(--mflow-pink);
            background: #fff0f3;
            transform: scale(1.05);
        }
        .theme-icon { font-size: 32px; margin-bottom: 5px; }

        /* The Gift Preview Box */
        .gift-preview {
            background: linear-gradient(135deg, #ff4d6d 0%, #ff8fa3 100%);
            border-radius: 25px;
            padding: 40px;
            color: #fff;
            text-align: center;
            box-shadow: 0 15px 35px rgba(255, 77, 109, 0.3);
            margin: 30px 0;
            position: relative;
            overflow: hidden;
        }
        .gift-preview::after {
            content: '🎁'; position: absolute; bottom: -10px; right: -10px;
            font-size: 100px; opacity: 0.1; transform: rotate(-15deg);
        }

        .input-transparent {
            background: transparent; border: none; border-bottom: 2px solid rgba(255,255,255,0.3);
            color: #fff; text-align: center; font-size: 1.5rem; font-weight: 700; width: 80%;
            margin-top: 15px;
        }
        .input-transparent::placeholder { color: rgba(255,255,255,0.6); }
        .input-transparent:focus { outline: none; border-color: #fff; }

        .btn-gift {
            background: var(--mflow-pink); color: #fff; border: none;
            width: 100%; padding: 18px; border-radius: 15px; font-weight: 800;
            font-size: 1.1rem; box-shadow: 0 10px 20px rgba(255, 77, 109, 0.2);
        }

        /* Recipient Scroller */
        .recipient-img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid #fff; }
    </style>
</head>
<body>

<header class="p-3 d-flex align-items-center justify-content-between">
    <a href="/wallet" class="text-dark"><i class="ri-close-line fs-3"></i></a>
    <h5 class="mb-0 fw-bold">Send a Gift</h5>
    <div style="width: 30px;"></div>
</header>

<div class="container pb-5">
    <h6 class="fw-bold mb-3 text-secondary small">CHOOSE A VIBE</h6>
    <div class="row g-3">
        <div class="col-4">
            <div class="theme-card active" onclick="selectTheme('Birthday', 'linear-gradient(135deg, #ff4d6d 0%, #ff8fa3 100%)', '🎂')">
                <div class="theme-icon">🎂</div>
                <div class="small fw-bold">Birthday</div>
            </div>
        </div>
        <div class="col-4">
            <div class="theme-card" onclick="selectTheme('Thanks', 'linear-gradient(135deg, #1877f2 0%, #63a4ff 100%)', '🙏')">
                <div class="theme-icon">🙏</div>
                <div class="small fw-bold">Thanks</div>
            </div>
        </div>
        <div class="col-4">
            <div class="theme-card" onclick="selectTheme('Love', 'linear-gradient(135deg, #7b2ff7 0%, #b185ff 100%)', '❤️')">
                <div class="theme-icon">❤️</div>
                <div class="small fw-bold">Love</div>
            </div>
        </div>
    </div>

    <div class="gift-preview" id="previewBox">
        <div id="themeEmoji" style="font-size: 50px;">🎂</div>
        <h4 class="fw-bold mt-2" id="themeTitle">Happy Birthday!</h4>
        
        <div class="mt-4">
            <span class="fs-4 fw-bold">MC</span>
            <input type="number" class="input-transparent" placeholder="0.00" id="giftAmount">
        </div>
        
        <textarea class="input-transparent fs-6 mt-3" style="border: none; font-weight: 400;" rows="1" placeholder="Write a sweet note..."></textarea>
    </div>

    <div class="mb-4">
        <h6 class="fw-bold mb-3 text-secondary small">SEND TO</h6>
        <div class="bg-light p-3 rounded-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="https://i.pravatar.cc/150?u=jack" class="recipient-img me-3 shadow-sm">
                <div>
                    <div class="fw-bold small">Jack Dorsey</div>
                    <div class="text-muted" style="font-size: 11px;">@jack</div>
                </div>
            </div>
            <button class="btn btn-sm btn-white bg-white text-primary fw-bold shadow-sm rounded-pill">Change</button>
        </div>
    </div>

    <button class="btn-gift" onclick="sendGift()">
        Send Gift Now 🎁
    </button>

    <div class="text-center mt-3">
        <small class="text-muted">Balance: 12,450.80 MC</small>
    </div>
</div>

<div class="modal fade" id="giftSuccess" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 text-center p-4">
            <div class="fs-1">🎉</div>
            <h4 class="fw-bold">Gift Sent!</h4>
            <p class="text-secondary">Jack will see your surprise in their messages.</p>
            <button class="btn btn-primary w-100 rounded-pill py-3 fw-bold" onclick="location.href='/chat'">Back to Chat</button>
        </div>
    </div>
</div>

<script>
    function selectTheme(title, gradient, emoji) {
        // Toggle Active Class
        document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('active'));
        event.currentTarget.classList.add('active');

        // Update Preview
        const preview = document.getElementById('previewBox');
        preview.style.background = gradient;
        document.getElementById('themeTitle').innerText = title + '!';
        document.getElementById('themeEmoji').innerText = emoji;
    }

    function sendGift() {
        const amount = document.getElementById('giftAmount').value;
        if(!amount || amount <= 0) {
            alert("Please enter a gift amount");
            return;
        }
        
        // Trigger Success (In a real app, this would be an AJAX call)
        const myModal = new bootstrap.Modal(document.getElementById('giftSuccess'));
        myModal.show();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>