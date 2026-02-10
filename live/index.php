<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__."/../main-function.php"; ?>
    <?php 
        $main = new main($conn);
        if($main->isLoggedIn() === false) header('location:/');
        $userData = $main->getUserData()['data'];
        $profile = json_decode($userData['profile'], true);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Stream | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --live-red: #ff3b30;
            --fb-blue: #1877f2;
            --dark-surface: #18191a;
        }

        body { background-color: #000; color: #fff; font-family: 'Segoe UI', sans-serif; height: 100vh; overflow: hidden; }

        /* --- 3 COLUMN GRID --- */
        .live-wrapper {
            display: grid;
            grid-template-columns: 280px 1fr 360px;
            height: calc(100vh - 56px);
        }

        /* Sidebar Nav */
        .live-sidebar { border-right: 1px solid #222; padding: 20px; background: #000; }
        .nav-link-live {
            display: flex; align-items: center; padding: 12px;
            color: #eee; text-decoration: none; border-radius: 8px;
            margin-bottom: 5px; font-weight: 500;
        }
        .nav-link-live:hover { background: #242526; }
        .nav-link-live i { font-size: 22px; margin-right: 12px; }

        /* Main Player Area */
        .video-stage {
            background: #000;
            display: flex; flex-direction: column;
            position: relative;
        }
        .main-stream-container {
            flex: 1; position: relative;
            display: flex; align-items: center; justify-content: center;
        }
        .live-indicator {
            position: absolute; top: 20px; left: 20px;
            background: var(--live-red); color: white;
            padding: 4px 12px; border-radius: 4px; font-weight: bold;
            font-size: 14px; z-index: 10;
        }
        .viewer-count {
            position: absolute; top: 20px; left: 90px;
            background: rgba(0,0,0,0.6); color: white;
            padding: 4px 12px; border-radius: 4px; font-size: 14px;
        }

        /* Live Chat Sidebar */
        .chat-sidebar {
            background: var(--dark-surface);
            display: flex; flex-direction: column;
            border-left: 1px solid #333;
        }
        .chat-messages {
            flex: 1; overflow-y: auto; padding: 15px;
            display: flex; flex-direction: column; gap: 12px;
        }
        .chat-bubble { font-size: 14px; line-height: 1.4; }
        .chat-user { font-weight: bold; color: #45bd62; margin-right: 5px; }

        /* Input Area */
        .chat-input-area {
            padding: 15px; border-top: 1px solid #333;
            background: var(--dark-surface);
        }
        .chat-input-wrapper {
            background: #3a3b3c; border-radius: 20px;
            padding: 5px 15px; display: flex; align-items: center;
        }
        .chat-input-wrapper input {
            background: transparent; border: none; color: white;
            flex: 1; padding: 8px; outline: none;
        }

        /* Mobile Breakpoints */
        @media (max-width: 1100px) {
            .live-wrapper { grid-template-columns: 80px 1fr 300px; }
            .live-sidebar span { display: none; }
        }
        @media (max-width: 768px) {
            .live-wrapper { display: flex; flex-direction: column; }
            .live-sidebar { display: none; }
            .main-stream-container { height: 40vh; flex: none; }
            .chat-sidebar { flex: 1; }
        }
    </style>
</head>
<body>

<header class="d-flex align-items-center justify-content-between px-3 border-bottom border-dark" style="height: 56px; background: #000;">
    <div class="d-flex align-items-center">
        <a href="/home" class="text-white me-3"><i class="ri-arrow-left-line fs-4"></i></a>
        <div class="fw-bold fs-5 text-primary">monieFlow <span class="text-danger">LIVE</span></div>
    </div>
    <div class="d-flex gap-3">
        <button class="btn btn-danger btn-sm fw-bold rounded-pill px-3">Go Live</button>
        <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle" width="35">
    </div>
</header>

<div class="live-wrapper container px-0">
    <aside class="live-sidebar">
        <h6 class="text-secondary small fw-bold mb-3">MENU</h6>
        <nav>
            <a href="#" class="nav-link-live active"><i class="ri-broadcast-line"></i> <span>For You</span></a>
            <a href="#" class="nav-link-live"><i class="ri-gamepad-line"></i> <span>Gaming</span></a>
            <a href="#" class="nav-link-live"><i class="ri-music-2-line"></i> <span>Music</span></a>
            <a href="#" class="nav-link-live"><i class="ri-shopping-bag-line"></i> <span>Live Shopping</span></a>
        </nav>
    </aside>

    <main class="video-stage">
        <div class="main-stream-container">
            <div class="live-indicator">LIVE</div>
            <div class="viewer-count"><i class="ri-eye-line"></i> 4.2k</div>
            
            <video id="live-player" style="width: 100%; height: 100%; object-fit: cover;" poster="https://picsum.photos/1200/800">
                <source src="" type="video/mp4">
            </video>

            <div id="reaction-container" class="position-absolute bottom-0 end-0 p-5 overflow-hidden" style="pointer-events: none; height: 300px; width: 100px;"></div>
        </div>

        <div class="p-3 bg-dark">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="https://i.pravatar.cc/150?u=streamer" class="rounded-circle me-3" width="50">
                    <div>
                        <h5 class="mb-0 fw-bold">Tech Talk Weekly: monieFlow Updates</h5>
                        <small class="text-success fw-bold">Tunde Ade is Live</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary"><i class="ri-share-forward-line"></i> Share</button>
                    <button class="btn btn-primary"><i class="ri-heart-fill"></i> Support</button>
                </div>
            </div>
        </div>
    </main>

    <aside class="chat-sidebar">
        <div class="p-3 border-bottom border-dark d-flex justify-content-between">
            <h6 class="mb-0 fw-bold">Live Chat</h6>
            <i class="ri-more-2-fill"></i>
        </div>
        
        <div class="chat-messages" id="chat-box">
            <div class="chat-bubble"><span class="chat-user">Alex:</span> This new layout is insane! 🚀</div>
            <div class="chat-bubble"><span class="chat-user">Sarah:</span> Can you show the marketplace again?</div>
            <div class="chat-bubble"><span class="chat-user text-warning">Moderator:</span> Please follow the community rules.</div>
            <div class="chat-bubble"><span class="chat-user">John:</span> checking in from Lagos!</div>
        </div>

        <div class="chat-input-area">
            <div class="d-flex gap-2 mb-2">
                <span class="badge bg-secondary cursor-pointer" onclick="sendEmoji('❤️')">❤️</span>
                <span class="badge bg-secondary cursor-pointer" onclick="sendEmoji('🔥')">🔥</span>
                <span class="badge bg-secondary cursor-pointer" onclick="sendEmoji('👏')">👏</span>
            </div>
            <div class="chat-input-wrapper">
                <input type="text" id="chat-msg" placeholder="Say something...">
                <i class="ri-send-plane-2-fill text-primary ms-2" style="cursor: pointer;" onclick="sendMessage()"></i>
            </div>
        </div>
    </aside>
</div>

<script>
    function sendMessage() {
        const input = document.getElementById('chat-msg');
        if(!input.value) return;
        
        const chatBox = document.getElementById('chat-box');
        const newMsg = document.createElement('div');
        newMsg.className = 'chat-bubble';
        newMsg.innerHTML = `<span class="chat-user" style="color: #1877f2">You:</span> ${input.value}`;
        chatBox.appendChild(newMsg);
        
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function sendEmoji(emoji) {
        // Floating animation logic
        const container = document.getElementById('reaction-container');
        const e = document.createElement('div');
        e.innerHTML = emoji;
        e.style.position = 'absolute';
        e.style.bottom = '0';
        e.style.right = Math.random() * 50 + 'px';
        e.style.fontSize = '24px';
        e.style.transition = 'all 2s ease-out';
        e.style.opacity = '1';
        
        container.appendChild(e);
        
        setTimeout(() => {
            e.style.bottom = '250px';
            e.style.opacity = '0';
            e.style.transform = `translateX(${(Math.random() - 0.5) * 100}px)`;
        }, 50);

        setTimeout(() => e.remove(), 2000);
    }
</script>

</body>
</html>