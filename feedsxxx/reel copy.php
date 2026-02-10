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
    <title>Reels | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        :root {
            --reel-bg: #000000;
            --reel-overlay: rgba(0, 0, 0, 0.4);
        }

        body, html {
            margin: 0; padding: 0;
            height: 100%; background: var(--reel-bg);
            overflow: hidden; /* Prevent body scroll */
        }

        /* Swiper Container */
        .swiper { width: 100%; height: 100vh; }

        .swiper-slide {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #000;
        }

        /* Video Background */
        .reel-video {
            height: 100%;
            width: 100%;
            object-fit: cover; /* Vertical fill */
        }

        /* Overlay Styling */
        .reel-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 100px 20px 40px; /* Top padding for gradient */
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: #fff;
            z-index: 10;
        }

        /* Interaction Buttons */
        .reel-actions {
            position: absolute;
            right: 15px;
            bottom: 120px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
            z-index: 20;
        }

        .action-item { text-align: center; cursor: pointer; }
        .action-item i {
            font-size: 32px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            transition: transform 0.2s;
        }
        .action-item:hover i { transform: scale(1.1); }
        .action-item span { font-size: 12px; font-weight: 600; display: block; }

        .user-avatar-reel {
            width: 45px; height: 45px;
            border: 2px solid #fff;
            border-radius: 50%;
        }

        /* Header Navigation */
        .reels-header {
            position: absolute;
            top: 20px; left: 20px;
            z-index: 100;
            color: #fff;
            display: flex; align-items: center;
            gap: 15px;
        }

        .brand-text { font-weight: 800; font-size: 1.5rem; letter-spacing: -1px; }

        /* Desktop Mode Constraint */
        @media (min-width: 992px) {
            .swiper-slide {
                max-width: 420px; /* Phone-shaped container on desktop */
                margin: 0 auto;
                border-left: 1px solid #333;
                border-right: 1px solid #333;
            }
        }
    </style>
</head>
<body>

<div class="reels-header">
    <a href="/home" class="text-white"><i class="ri-arrow-left-line fs-3"></i></a>
    <span class="brand-text">Reels</span>
</div>

<div class="swiper reelSwiper">
    <div class="swiper-wrapper">
        
        <div class="swiper-slide">
            <video class="reel-video" loop muted autoplay playsinline>
                <source src="https://assets.mixkit.co/videos/preview/mixkit-girl-in-neon-light-dancing-29-large.mp4" type="video/mp4">
            </video>

            <div class="reel-actions">
                <div class="action-item" onclick="toggleLike(this)">
                    <i class="ri-heart-fill"></i><span>1.2k</span>
                </div>
                <div class="action-item" onclick="openComments()">
                    <i class="ri-chat-3-fill"></i><span>458</span>
                </div>
                <div class="action-item">
                    <i class="ri-share-forward-fill"></i><span>Share</span>
                </div>
                <div class="action-item">
                    <i class="ri-more-2-fill"></i>
                </div>
            </div>

            <div class="reel-overlay">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://i.pravatar.cc/150?u=1" class="user-avatar-reel me-2">
                    <h6 class="mb-0 fw-bold">sarah_styles · <span class="small fw-normal">Follow</span></h6>
                </div>
                <p class="small mb-2">Check out this new monieFlow UI transition! #coding #uidesign #reels</p>
                <div class="small"><i class="ri-music-2-fill me-2"></i> Original Audio - monieFlow Beats</div>
            </div>
        </div>

        <div class="swiper-slide">
            <video class="reel-video" loop muted playsinline>
                <source src="https://assets.mixkit.co/videos/preview/mixkit-tree-with-yellow-leaves-low-angle-shot-1571-large.mp4" type="video/mp4">
            </video>

            <div class="reel-actions">
                <div class="action-item"><i class="ri-heart-fill"></i><span>890</span></div>
                <div class="action-item"><i class="ri-chat-3-fill"></i><span>120</span></div>
                <div class="action-item"><i class="ri-share-forward-fill"></i><span>Share</span></div>
            </div>

            <div class="reel-overlay">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://i.pravatar.cc/150?u=2" class="user-avatar-reel me-2">
                    <h6 class="mb-0 fw-bold">nature_vibes · <span class="small fw-normal">Follow</span></h6>
                </div>
                <p class="small mb-2">Nature is healing. 🌿 #serenity #peace</p>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    // Initialize Vertical Swiper
    const swiper = new Swiper('.reelSwiper', {
        direction: 'vertical',
        mousewheel: true,
        on: {
            slideChange: function () {
                // Pause all videos and play only the active one
                const videos = document.querySelectorAll('.reel-video');
                videos.forEach(v => v.pause());
                const activeVideo = this.slides[this.activeIndex].querySelector('video');
                if (activeVideo) activeVideo.play();
            },
        },
    });

    function toggleLike(el) {
        const icon = el.querySelector('i');
        icon.classList.toggle('text-danger');
        // Add animation class or hit API here
    }

    function openComments() {
        alert("Comments feature coming soon to monieFlow!");
    }
</script>

</body>
</html>