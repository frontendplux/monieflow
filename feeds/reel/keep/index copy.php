<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flow Reels | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-gold: #ffcc00;
            --mflow-pink: #ff4d6d;
        }

        body, html {
            margin: 0; padding: 0;
            height: 100%; width: 100%;
            background: #000;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Fullscreen Video Container */
        .reel-container {
            height: 100%;
            width: 100%;
            position: relative;
            scroll-snap-type: y mandatory;
            overflow-y: scroll;
        }

        .reel-video-wrapper {
            height: 100%;
            width: 100%;
            scroll-snap-align: start;
            position: relative;
            background: #111;
        }

        video {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        /* UI Overlays */
        .reel-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 80px 20px 30px;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            pointer-events: none;
        }

        .reel-content { pointer-events: auto; }

        /* Right Side Actions */
        .side-actions {
            position: absolute;
            right: 15px;
            bottom: 120px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
            z-index: 10;
        }

        .action-item {
            text-align: center;
            color: #fff;
            cursor: pointer;
        }

        .action-item i {
            font-size: 28px;
            display: block;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .action-item span { font-size: 12px; font-weight: 600; }

        /* Tipping Button */
        .tip-btn {
            background: var(--mflow-gold);
            color: #000;
            width: 45px; height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 0 20px rgba(255, 204, 0, 0.5);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 204, 0, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(255, 204, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 204, 0, 0); }
        }

        /* Creator Profile Area */
        .creator-info { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .creator-avatar { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #fff; }

        .reel-title { font-size: 14px; margin-bottom: 5px; color: #eee; }
        .music-tag { font-size: 13px; color: #bbb; display: flex; align-items: center; gap: 5px; }

        /* Top Bar */
        .reel-top-bar {
            position: absolute;
            top: 20px; left: 0; right: 0;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 20;
        }
    </style>
</head>
<body>

<div class="reel-top-bar">
    <i class="ri-arrow-s-left-s-line fs-3 text-white" onclick="history.back()"></i>
    <div class="d-flex gap-4 fw-bold">
        <span class="text-white-50">Following</span>
        <span class="text-white border-bottom border-3 pb-1">For You</span>
    </div>
    <i class="ri-camera-line fs-3 text-white"></i>
</div>

<div class="reel-container">
    
    <div class="reel-video-wrapper">
        <video loop muted autoplay playsinline src="https://assets.mixkit.co/videos/preview/mixkit-girl-in-neon-light-dancing-alone-34087-large.mp4"></video>
        
        <div class="side-actions">
            <div class="action-item">
                <i class="ri-heart-fill text-danger"></i>
                <span>45.2K</span>
            </div>
            <div class="action-item">
                <i class="ri-chat-3-fill"></i>
                <span>820</span>
            </div>
            <div class="action-item" onclick="openTipModal()">
                <div class="tip-btn">MC</div>
                <span class="text-warning">Tip</span>
            </div>
            <div class="action-item">
                <i class="ri-share-forward-fill"></i>
                <span>Share</span>
            </div>
        </div>

        <div class="reel-overlay">
            <div class="reel-content">
                <div class="creator-info">
                    <img src="https://i.pravatar.cc/150?u=techgirl" class="creator-avatar">
                    <span class="fw-bold">@alex_vibe</span>
                    <button class="btn btn-sm btn-outline-light rounded-pill px-3 py-0 ms-2" style="font-size: 11px;">Follow</button>
                </div>
                <p class="reel-title">Building the future of monieFlow! 🚀 #Web3 #Fintech</p>
                <div class="music-tag">
                    <i class="ri-music-2-line"></i> Original Sound - alex_vibe
                </div>
            </div>
        </div>
    </div>
    
    <div class="reel-video-wrapper">
        <video loop muted autoplay playsinline src="https://assets.mixkit.co/videos/preview/mixkit-girl-in-neon-light-dancing-alone-34087-large.mp4"></video>
        
        <div class="side-actions">
            <div class="action-item">
                <i class="ri-heart-fill text-danger"></i>
                <span>45.2K</span>
            </div>
            <div class="action-item">
                <i class="ri-chat-3-fill"></i>
                <span>820</span>
            </div>
            <div class="action-item" onclick="openTipModal()">
                <div class="tip-btn">MC</div>
                <span class="text-warning">Tip</span>
            </div>
            <div class="action-item">
                <i class="ri-share-forward-fill"></i>
                <span>Share</span>
            </div>
        </div>

        <div class="reel-overlay">
            <div class="reel-content">
                <div class="creator-info">
                    <img src="https://i.pravatar.cc/150?u=techgirl" class="creator-avatar">
                    <span class="fw-bold">@alex_vibe</span>
                    <button class="btn btn-sm btn-outline-light rounded-pill px-3 py-0 ms-2" style="font-size: 11px;">Follow</button>
                </div>
                <p class="reel-title">Building the future of monieFlow! 🚀 #Web3 #Fintech</p>
                <div class="music-tag">
                    <i class="ri-music-2-line"></i> Original Sound - alex_vibe
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="tipModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary rounded-4 p-4 text-center">
            <h5 class="fw-bold">Support Creator</h5>
            <p class="text-muted small">Send monieCoins to @alex_vibe</p>
            
            <div class="d-flex justify-content-center gap-2 my-4">
                <button class="btn btn-outline-warning rounded-pill px-3">10 MC</button>
                <button class="btn btn-outline-warning rounded-pill px-3">50 MC</button>
                <button class="btn btn-outline-warning rounded-pill px-3">100 MC</button>
            </div>
            
            <input type="number" class="form-control bg-transparent text-white text-center mb-3" placeholder="Custom Amount">
            
            <button class="btn btn-warning w-100 py-3 rounded-pill fw-bold" onclick="sendTip()">Send Tip 🚀</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openTipModal() {
        const myModal = new bootstrap.Modal(document.getElementById('tipModal'));
        myModal.show();
    }

    function sendTip() {
        alert("Success! Your tip has been sent.");
        location.reload();
    }

    // Auto-play videos as they enter view (Simulated)
    const container = document.querySelector('.reel-container');
    container.addEventListener('scroll', () => {
        // Logic to pause/play based on scroll position could go here
    });
</script>

</body>
</html>