<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuddyFlow | Community Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; }

        /* 1. Story Bar */
        .story-container {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 20px 0;
            scrollbar-width: none;
        }
        .story-item {
            flex: 0 0 auto;
            text-align: center;
            width: 80px;
        }
        .story-ring {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            margin-bottom: 5px;
        }
        .story-ring img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
        }

        /* 2. Bento Gallery Grid */
        .buddy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            grid-auto-rows: 300px;
            grid-gap: 20px;
            grid-auto-flow: dense;
        }

        .buddy-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            cursor: pointer;
        }

        /* Make some items larger for visual interest */
        .buddy-card.wide { grid-column: span 2; }
        .buddy-card.tall { grid-row: span 2; }

        .buddy-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .buddy-card:hover img {
            transform: scale(1.1);
        }

        /* Profile Overlay */
        .buddy-info {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mini-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .btn-like {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            transition: 0.3s;
        }

        .btn-like:hover { background: #ff4757; color: white; }

        @media (max-width: 768px) {
            .buddy-card.wide { grid-column: span 1; }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="story-container">
        <div class="story-item">
            <div class="story-ring"><img src="https://i.pravatar.cc/150?u=1"></div>
            <span class="small fw-bold">Your Story</span>
        </div>
        <div class="story-item">
            <div class="story-ring"><img src="https://i.pravatar.cc/150?u=2"></div>
            <span class="small">Lina</span>
        </div>
        <div class="story-item">
            <div class="story-ring"><img src="https://i.pravatar.cc/150?u=3"></div>
            <span class="small">Kufre</span>
        </div>
        <div class="story-item">
            <div class="story-ring"><img src="https://i.pravatar.cc/150?u=4"></div>
            <span class="small">Sarah</span>
        </div>
        <div class="story-item">
            <div class="story-ring"><img src="https://i.pravatar.cc/150?u=5"></div>
            <span class="small">David</span>
        </div>
    </div>

    <hr class="my-4 opacity-10">

    <div class="buddy-grid">
        
        <div class="buddy-card wide tall">
            <button class="btn-like"><i class="ri-heart-fill"></i></button>
            <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800" alt="Group">
            <div class="buddy-info">
                <img src="https://i.pravatar.cc/150?u=10" class="mini-avatar">
                <div>
                    <h6 class="mb-0 fw-bold">Weekend Beach Party</h6>
                    <small class="opacity-75">12 Buddies joined</small>
                </div>
            </div>
        </div>

        <div class="buddy-card">
            <button class="btn-like"><i class="ri-heart-line"></i></button>
            <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=500" alt="Friends">
            <div class="buddy-info">
                <img src="https://i.pravatar.cc/150?u=12" class="mini-avatar">
                <div>
                    <h6 class="mb-0 fw-bold">Coffee & Code</h6>
                    <small class="opacity-75">Tobi & 2 others</small>
                </div>
            </div>
        </div>

        <div class="buddy-card tall">
            <button class="btn-like"><i class="ri-heart-line"></i></button>
            <img src="https://images.unsplash.com/photo-1475483768296-6163e08872a1?auto=format&fit=crop&w=500" alt="Travel">
            <div class="buddy-info">
                <img src="https://i.pravatar.cc/150?u=15" class="mini-avatar">
                <div>
                    <h6 class="mb-0 fw-bold">Lekki Hike</h6>
                    <small class="opacity-75">Outdoor Adventure</small>
                </div>
            </div>
        </div>

        <div class="buddy-card">
            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=500" alt="Games">
            <div class="buddy-info">
                <img src="https://i.pravatar.cc/150?u=20" class="mini-avatar">
                <div>
                    <h6 class="mb-0 fw-bold">Game Night</h6>
                    <small class="opacity-75">PS5 Tournament</small>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>