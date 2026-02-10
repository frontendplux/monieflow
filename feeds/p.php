<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jack Dorsey (@jack) | monieFlow</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-gold: #ffcc00;
            --mflow-pink: #ff4d6d;
            --glass: rgba(255, 255, 255, 0.8);
        }

        body { background-color: #f0f2f5; font-family: 'Segoe UI', system-ui, sans-serif; }

        /* --- COVER & AVATAR SECTION --- */
        .profile-header { background: #fff; position: relative; }
        
        .cover-photo {
            height: 250px;
            background: linear-gradient(45deg, #1a1a2e, #1877f2);
            background-image: url('https://images.unsplash.com/photo-1614850523296-e8c0d9f8d42f?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
        }

        .profile-meta-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            margin-top: -60px;
        }

        .profile-avatar-wrapper {
            position: relative;
            display: inline-block;
        }

        .profile-avatar {
            width: 150px; height: 150px;
            border-radius: 50%;
            border: 5px solid #fff;
            background: #eee;
            object-fit: cover;
        }

        .verified-badge {
            position: absolute; bottom: 10px; right: 10px;
            background: var(--mflow-blue); color: #fff;
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 3px solid #fff;
        }

        /* --- STATS BAR --- */
        .stats-row { display: flex; gap: 30px; margin-top: 15px; }
        .stat-item { text-align: center; }
        .stat-value { font-weight: 800; font-size: 1.1rem; display: block; }
        .stat-label { font-size: 0.85rem; color: #65676b; text-transform: uppercase; }

        /* --- TABS --- */
        .profile-tabs {
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: center;
            gap: 50px;
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .tab-link {
            padding: 15px 5px;
            text-decoration: none;
            color: #65676b;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-link.active { color: var(--mflow-blue); border-color: var(--mflow-blue); }

        /* --- CONTENT GRID --- */
        .content-section { max-width: 1100px; margin: 20px auto; padding: 0 15px; }
        
        .sticky-sidebar { position: sticky; top: 70px; }

        .info-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        /* Reels Grid */
        .reels-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .reel-thumb {
            aspect-ratio: 9/16;
            background: #ddd;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
        }
        .reel-thumb img { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .reel-thumb:hover img { transform: scale(1.05); }
        .reel-views {
            position: absolute; bottom: 8px; left: 8px;
            color: #fff; font-size: 12px; font-weight: 600;
            text-shadow: 0 1px 4px rgba(0,0,0,0.8);
        }

        /* Locked Content Styling */
        .locked-post {
            background: #fff; border-radius: 12px; padding: 20px;
            border: 2px dashed var(--mflow-gold);
            text-align: center;
        }

        /* Mobile Action Bar */
        .mobile-actions {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #fff; padding: 12px 20px;
            display: flex; gap: 10px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.05);
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .stats-row { justify-content: space-around; width: 100%; }
            .reels-grid { grid-template-columns: repeat(2, 1fr); }
            .desktop-actions { display: none; }
            .profile-avatar { width: 100px; height: 100px; }
            .profile-meta-container { margin-top: -40px; text-align: center; }
            .stats-row { margin-bottom: 20px; }
        }

        @media (min-width: 769px) {
            .mobile-actions { display: none; }
        }
    </style>
</head>
<body>

<div class="profile-header">
    <div class="cover-photo"></div>
    <div class="profile-meta-container">
        <div class="d-md-flex align-items-end justify-content-between">
            <div class="d-md-flex align-items-end">
                <div class="profile-avatar-wrapper">
                    <img src="https://i.pravatar.cc/150?u=jack" class="profile-avatar">
                    <div class="verified-badge"><i class="ri-checkbox-circle-fill"></i></div>
                </div>
                <div class="ms-md-4 mb-2">
                    <h2 class="fw-bold mb-0">Jack Dorsey</h2>
                    <span class="text-secondary">@jack • Digital Architect</span>
                    <div class="stats-row">
                        <div class="stat-item"><span class="stat-value">1.2M</span><span class="stat-label">Followers</span></div>
                        <div class="stat-item"><span class="stat-value">450</span><span class="stat-label">Following</span></div>
                        <div class="stat-item"><span class="stat-value">85K</span><span class="stat-label">Tips</span></div>
                    </div>
                </div>
            </div>
            <div class="desktop-actions mb-3">
                <button class="btn btn-primary rounded-pill px-4 fw-bold me-2"><i class="ri-user-add-line"></i> Follow</button>
                <button class="btn btn-warning rounded-pill px-4 fw-bold"><i class="ri-money-dollar-circle-line"></i> Subscribe</button>
                <button class="btn btn-light rounded-circle ms-2"><i class="ri-more-2-fill"></i></button>
            </div>
        </div>
        <p class="mt-4 text-dark" style="max-width: 600px;">
            Building decentralized protocols and exploring the future of social money. Stay focused. 🕊️ 
            <br><a href="#" class="text-primary text-decoration-none small fw-bold">linktr.ee/jack_flow</a>
        </p>
    </div>

    <div class="profile-tabs">
        <a href="#posts" class="tab-link active"><i class="ri-grid-line"></i> Posts</a>
        <a href="#reels" class="tab-link"><i class="ri-video-line"></i> Reels</a>
        <a href="#locked" class="tab-link"><i class="ri-lock-line"></i> Exclusive</a>
    </div>
</div>

<div class="content-section">
    <div class="row">
        <div class="col-lg-4 d-none d-lg-block">
            <div class="sticky-sidebar">
                <div class="info-card">
                    <h6 class="fw-bold mb-3">About</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="ri-map-pin-line me-2 text-muted"></i> Lagos, Nigeria</li>
                        <li class="mb-2"><i class="ri-calendar-line me-2 text-muted"></i> Joined February 2024</li>
                        <li><i class="ri-link me-2 text-muted"></i> <span class="text-primary">monieflow.com/jack</span></li>
                    </ul>
                </div>
                <div class="info-card">
                    <h6 class="fw-bold mb-3">Top Supporters 💎</h6>
                    <div class="d-flex gap-2">
                        <img src="https://i.pravatar.cc/150?u=1" class="rounded-circle" width="35">
                        <img src="https://i.pravatar.cc/150?u=2" class="rounded-circle" width="35">
                        <img src="https://i.pravatar.cc/150?u=3" class="rounded-circle" width="35">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold small" style="width: 35px; height: 35px;">+12</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Recent Reels</h5>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </div>
            
            <div class="reels-grid mb-5">
                <div class="reel-thumb">
                    <img src="https://images.unsplash.com/photo-1516245834210-c4c142787335?auto=format&fit=crop&w=400&q=80">
                    <span class="reel-views"><i class="ri-play-fill"></i> 12.5K</span>
                </div>
                <div class="reel-thumb">
                    <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=400&q=80">
                    <span class="reel-views"><i class="ri-play-fill"></i> 8.2K</span>
                </div>
                <div class="reel-thumb">
                    <div class="locked-content-overlay d-flex flex-column align-items-center justify-content-center h-100 bg-dark bg-opacity-50 text-white">
                        <i class="ri-lock-fill fs-2 text-warning"></i>
                        <small>Premium</small>
                    </div>
                    <img src="https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&w=400&q=80">
                </div>
            </div>

            <div class="info-card">
                <div class="d-flex mb-3">
                    <img src="https://i.pravatar.cc/150?u=jack" class="rounded-circle me-3" width="45" height="45">
                    <div>
                        <div class="fw-bold">Jack Dorsey <i class="ri-checkbox-circle-fill text-primary"></i></div>
                        <small class="text-muted">2 hours ago</small>
                    </div>
                </div>
                <p>The transition from traditional social to social-money protocols is happening faster than we thought. monieFlow is leading the charge. ⚡️</p>
                <img src="https://images.unsplash.com/photo-1639762681485-074b7f938ba0?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-3 mb-3">
                <div class="d-flex gap-4 text-muted">
                    <span><i class="ri-heart-line"></i> 1.2K</span>
                    <span><i class="ri-chat-3-line"></i> 45</span>
                    <span><i class="ri-copper-coin-line"></i> 128 Tips</span>
                </div>
            </div>
            
            <div style="height: 500px;" class="d-flex align-items-center justify-content-center text-muted italic">
                Scroll for more content...
            </div>
        </div>
    </div>
</div>

<div class="mobile-actions">
    <button class="btn btn-primary flex-grow-1 rounded-pill fw-bold">Follow</button>
    <button class="btn btn-warning flex-grow-1 rounded-pill fw-bold">Subscribe</button>
    <button class="btn btn-light rounded-circle"><i class="ri-send-plane-fill"></i></button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple tab switching logic
    document.querySelectorAll('.tab-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>

</body>
</html>