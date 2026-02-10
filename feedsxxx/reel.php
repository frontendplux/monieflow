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
            --glass: rgba(255, 255, 255, 0.1);
            --fb-blue: #1877f2;
        }

        body { 
            background-color: var(--reel-bg); 
            color: #fff; 
            margin: 0; 
            height: 100vh; 
            overflow: hidden; 
            font-family: 'Segoe UI', sans-serif;
        }

        /* --- LAYOUT --- */
        .reels-container {
            display: grid;
            grid-template-columns: 360px 1fr 400px;
            height: 100vh;
        }

        /* Left Column: Navigation */
        .left-panel {
            padding: 24px;
            border-right: 1px solid #222;
        }

        .nav-link-reel {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            margin-bottom: 8px;
            transition: 0.3s;
        }

        .nav-link-reel:hover { background: var(--glass); }
        .nav-link-reel i { font-size: 24px; margin-right: 15px; }
        .nav-link-reel.active { color: var(--fb-blue); }

        /* Center Column: The Stage */
        .center-stage {
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .swiper { width: 100%; height: 90vh; }

        .reel-frame {
            width: 100%;
            max-width: 420px;
            height: 100%;
            margin: 0 auto;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            background: #111;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        video { width: 100%; height: 100%; object-fit: cover; }

        /* Right Column: Engagement */
        .right-panel {
            background: #000;
            border-left: 1px solid #222;
            display: flex;
            flex-direction: column;
        }

        .panel-header { padding: 20px; border-bottom: 1px solid #222; }
        .comment-box { flex: 1; overflow-y: auto; padding: 20px; }
        .interaction-footer { padding: 20px; border-top: 1px solid #222; }

        /* Mobile Adjustments */
        @media (max-width: 1200px) {
            .reels-container { grid-template-columns: 80px 1fr 0px; }
            .left-panel span, .right-panel { display: none; }
        }

        @media (max-width: 768px) {
            .reels-container { display: block; }
            .left-panel { display: none; }
            .swiper { height: 100vh; }
            .reel-frame { max-width: 100%; border-radius: 0; }
        }

        /* Floating Actions for Mobile */
        .mobile-actions {
            position: absolute;
            right: 15px;
            bottom: 100px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            z-index: 100;
        }
        
        .action-btn {
            background: none; border: none; color: #fff; text-align: center;
        }
        .action-btn i { font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.8)); }
        .action-btn span { display: block; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>

<div class="reels-container container px-0">   
    <aside class="left-panel">
        <h2 class="fw-bold mb-5" style="letter-spacing: -1px;">monieFlow</h2>
        <nav>
            <a href="/home" class="nav-link-reel"><i class="ri-home-5-line"></i> <span>Home</span></a>
            <a href="#" class="nav-link-reel active"><i class="ri-movie-fill"></i> <span>Reels</span></a>
            <a href="#" class="nav-link-reel"><i class="ri-compass-3-line"></i> <span>Explore</span></a>
            <a href="#" class="nav-link-reel"><i class="ri-messenger-line"></i> <span>Messages</span></a>
            <a href="/profile" class="nav-link-reel">
                <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle me-3" width="24">
                <span>Profile</span>
            </a>
        </nav>
    </aside>

    <main class="center-stage">
        <div class="swiper reelSwiper">
            <div class="swiper-wrapper">
                
                <div class="swiper-slide">
                    <div class="reel-frame">
                        <video loop playsinline class="reel-video">
                            <source src="https://v1.pexels.com/video-files/3209828/3209828-uhd_1440_2560_25fps.mp4" type="video/mp4">
                        </video>
                        <div class="d-md-none position-absolute bottom-0 start-0 p-3 w-100 bg-gradient-dark">
                            <h6 class="mb-1">@<?= $profile['username'] ?></h6>
                            <p class="small mb-0">Building the new monieFlow experience! 🚀</p>
                        </div>
                        <div class="mobile-actions d-md-none">
                            <button class="action-btn"><i class="ri-heart-fill"></i><span>12k</span></button>
                            <button class="action-btn"><i class="ri-chat-3-fill"></i><span>450</span></button>
                            <button class="action-btn"><i class="ri-share-forward-fill"></i><span>Share</span></button>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="reel-frame">
                        <video loop playsinline class="reel-video">
                            <source src="https://v1.pexels.com/video-files/5834615/5834615-uhd_1440_2560_24fps.mp4" type="video/mp4">
                        </video>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <aside class="right-panel">
        <div class="panel-header">
            <div class="d-flex align-items-center mb-3">
                <img src="https://i.pravatar.cc/150?u=1" class="rounded-circle me-3" width="45">
                <div>
                    <h6 class="mb-0 fw-bold">sarah_codes</h6>
                    <small class="text-muted">Original Audio</small>
                </div>
                <button class="btn btn-outline-light btn-sm ms-auto fw-bold">Follow</button>
            </div>
            <p class="small">This is how we handle Swiper JS in a 3-column layout. Clean, fast, and responsive. #webdev #monieflow</p>
        </div>

        <div class="comment-box">
            <div class="d-flex gap-3 mb-4">
                <img src="https://i.pravatar.cc/150?u=9" class="rounded-circle" width="35" height="35">
                <div>
                    <div class="small"><span class="fw-bold">mike_ross</span> This is smooth! Love the snapping effect.</div>
                    <div class="small text-muted mt-1">3h · 12 likes · Reply</div>
                </div>
            </div>
            </div>

        <div class="interaction-footer">
            <div class="d-flex justify-content-between mb-3 fs-4">
                <div class="d-flex gap-4">
                    <i class="ri-heart-line cursor-pointer" onclick="like(this)"></i>
                    <i class="ri-chat-3-line"></i>
                    <i class="ri-share-forward-line"></i>
                </div>
                <i class="ri-bookmark-line"></i>
            </div>
            <div class="fw-bold small mb-3">12,405 Likes</div>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm bg-dark border-0 text-white" placeholder="Add comment...">
                <button class="btn btn-sm text-primary fw-bold">Post</button>
            </div>
        </div>
    </aside>

</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper('.reelSwiper', {
        direction: 'vertical',
        mousewheel: true,
        keyboard: true,
        on: {
            init: function() {
                // Play first video on load
                setTimeout(() => {
                    const firstVideo = this.slides[0].querySelector('video');
                    if(firstVideo) firstVideo.play();
                }, 500);
            },
            slideChange: function () {
                const videos = document.querySelectorAll('.reel-video');
                videos.forEach(v => v.pause());
                
                const activeVideo = this.slides[this.activeIndex].querySelector('video');
                if (activeVideo) {
                    activeVideo.currentTime = 0;
                    activeVideo.play();
                }
            }
        }
    });

    function like(el) {
        el.classList.toggle('ri-heart-fill');
        el.classList.toggle('ri-heart-line');
        el.classList.toggle('text-danger');
    }
</script>

</body>
</html>