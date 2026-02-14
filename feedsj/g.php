<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Zone | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --mflow-neon: #00f2ff;
            --mflow-purple: #7b2ff7;
            --mflow-gold: #ffcc00;
            --game-bg: #05050a;
        }

        body {
            background-color: var(--game-bg);
            color: #fff;
            font-family: 'Orbitron', sans-serif; /* Optional: Use a gaming font */
        }

        /* --- HERO TOURNAMENT CARD --- */
        .game-hero {
            height: 300px;
            background: linear-gradient(to right, rgba(0,0,0,0.9), transparent), 
                        url('https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            border-radius: 24px;
            display: flex;
            align-items: center;
            padding: 40px;
            margin-bottom: 30px;
            border: 1px solid rgba(0, 242, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .prize-pool {
            background: rgba(0, 242, 255, 0.1);
            border: 1px solid var(--mflow-neon);
            color: var(--mflow-neon);
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }

        /* --- GAME GRID --- */
        .game-card {
            background: #12121f;
            border-radius: 18px;
            overflow: hidden;
            transition: 0.3s;
            border: 1px solid transparent;
            height: 100%;
        }
        .game-card:hover {
            transform: translateY(-10px);
            border-color: var(--mflow-purple);
            box-shadow: 0 10px 30px rgba(123, 47, 247, 0.3);
        }

        .game-img {
            height: 160px;
            width: 100%;
            object-fit: cover;
        }

        .play-btn {
            background: var(--mflow-purple);
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: bold;
            color: #fff;
            width: 100%;
            margin-top: 15px;
        }

        /* --- LIVE STREAM SECTION --- */
        .live-tag {
            background: #ff4d6d;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        /* --- HORIZONTAL CATEGORIES --- */
        .cat-scroll {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 15px;
            scrollbar-width: none;
        }
        .cat-item {
            background: #1a1a2e;
            padding: 10px 25px;
            border-radius: 15px;
            white-space: nowrap;
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
        }
        .cat-item.active { background: var(--mflow-neon); color: #000; font-weight: bold; }

    </style>
</head>
<body>

<div class="container py-4">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Game Zone</h2>
            <p class="text-secondary small">Play, Win, and Cash out monieCoins</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block">
                <small class="text-secondary">Balance</small>
                <div class="fw-bold text-warning">12,450 MC</div>
            </div>
            <img src="https://i.pravatar.cc/150?u=jack" class="rounded-circle border border-2 border-primary" width="45">
        </div>
    </header>

    <div class="game-hero">
        <div class="hero-content">
            <div class="prize-pool">🏆 50,000 MC PRIZE POOL</div>
            <h1 class="display-5 fw-bold">Cyber Strike 2026</h1>
            <p class="text-white-50">Join the weekly arena and climb the leaderboard.</p>
            <button class="btn btn-primary btn-lg rounded-pill px-5">Join Tournament</button>
        </div>
    </div>

    <div class="cat-scroll mb-4">
        <div class="cat-item active">All Games</div>
        <div class="cat-item">Action</div>
        <div class="cat-item">Strategy</div>
        <div class="cat-item">Puzzle</div>
        <div class="cat-item">Arcade</div>
        <div class="cat-item">Earn MC</div>
    </div>

    <h5 class="fw-bold mb-3"><i class="ri-fire-line text-danger"></i> Trending Now</h5>
    <div class="row g-4">
        <div class="col-6 col-md-3">
            <div class="game-card">
                <img src="https://images.unsplash.com/photo-1614027126733-75778009bc3b?auto=format&fit=crop&w=400&q=80" class="game-img">
                <div class="p-3">
                    <h6 class="mb-1 text-truncate">Space Runner</h6>
                    <small class="text-warning"><i class="ri-copper-coin-line"></i> Win up to 500 MC</small>
                    <button class="play-btn">PLAY</button>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="game-card">
                <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=400&q=80" class="game-img">
                <div class="p-3">
                    <h6 class="mb-1 text-truncate">Neon Drift</h6>
                    <small class="text-warning"><i class="ri-copper-coin-line"></i> Daily Jackpot</small>
                    <button class="play-btn">PLAY</button>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="game-card position-relative">
                <span class="live-tag">LIVE</span>
                <img src="https://images.unsplash.com/photo-1560253023-3ee71f219fa0?auto=format&fit=crop&w=400&q=80" class="game-img">
                <div class="p-3">
                    <h6 class="mb-1 text-truncate">Pro Gamer Stream</h6>
                    <small class="text-secondary">4.2K Watching</small>
                    <button class="play-btn" style="background: transparent; border: 1px solid var(--mflow-purple);">WATCH</button>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="game-card">
                <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=400&q=80" class="game-img">
                <div class="p-3">
                    <h6 class="mb-1 text-truncate">Block Master</h6>
                    <small class="text-warning"><i class="ri-copper-coin-line"></i> +10 MC / Level</small>
                    <button class="play-btn">PLAY</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 p-4 rounded-4" style="background: #12121f;">
        <h5 class="fw-bold mb-4">Top Earners Today</h5>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary fw-bold">1</span>
                <img src="https://i.pravatar.cc/150?u=user1" class="rounded-circle" width="35">
                <span>GamerKing_99</span>
            </div>
            <div class="text-warning fw-bold">+15,200 MC</div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary fw-bold">2</span>
                <img src="https://i.pravatar.cc/150?u=user2" class="rounded-circle" width="35">
                <span>ShadowSlayer</span>
            </div>
            <div class="text-warning fw-bold">+12,000 MC</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>