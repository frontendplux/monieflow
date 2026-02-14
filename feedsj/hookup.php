<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuddyFlow | Platonic Connections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --accent: #6366f1;
            --bg-light: #fdfdff;
            --text-main: #1e293b;
        }

        body { background: var(--bg-light); color: var(--text-main); font-family: 'Outfit', sans-serif; }

        /* Interest Badges */
        .interest-tag {
            font-size: 0.75rem;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(99, 102, 241, 0.1);
            color: var(--accent);
            border: 1px solid rgba(99, 102, 241, 0.2);
            display: inline-block;
            margin-right: 5px;
        }

        /* Profile Cards */
        .friend-card {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .friend-card:hover { transform: scale(1.02); box-shadow: 0 20px 40px rgba(99, 102, 241, 0.1); }

        .profile-img { height: 280px; object-fit: cover; width: 100%; }

        /* Vibe Check UI */
        .vibe-match {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-connect {
            background: var(--accent);
            color: white;
            border-radius: 14px;
            font-weight: 700;
            padding: 12px;
            border: none;
        }

        .btn-connect:hover { filter: brightness(1.1); color: white; }

        /* Activity Filter Bar */
        .filter-bar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 15px 0; position: sticky; top: 0; z-index: 100; }
    </style>
</head>
<body>

<div class="filter-bar shadow-sm">
    <div class="container d-flex align-items-center justify-content-between">
        <h4 class="fw-bold mb-0 text-primary">BuddyFlow</h4>
        <div class="d-none d-md-flex gap-2">
            <button class="btn btn-light rounded-pill px-4 active">All</button>
            <button class="btn btn-light rounded-pill px-4">Fitness</button>
            <button class="btn btn-light rounded-pill px-4">Coding</button>
            <button class="btn btn-light rounded-pill px-4">Gaming</button>
        </div>
        <button class="btn btn-outline-secondary rounded-pill"><i class="ri-equalizer-line"></i> Filters</button>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card friend-card">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=400" class="profile-img" alt="Profile">
                    <div class="position-absolute bottom-0 start-0 p-3 w-100" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                        <h4 class="text-white mb-0 fw-bold">Alex, 26</h4>
                        <p class="text-white-50 small mb-0"><i class="ri-map-pin-2-line"></i> 1.5 miles away</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="interest-tag">UI Design</span>
                        <span class="interest-tag">Tennis</span>
                        <span class="interest-tag">Sushi</span>
                    </div>
                    <p class="text-muted small mb-0">"Looking for a tennis partner or someone to geek out over Figma with. Always down for weekend hikes."</p>
                    
                    <div class="vibe-match">
                        <i class="ri-sparkling-fill text-warning fs-4"></i>
                        <div class="small">
                            <strong>High Vibe Match!</strong><br>
                            Both of you love <span class="text-primary">Design</span>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button class="btn-connect">Say Hi! <i class="ri-send-plane-fill ms-1"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card friend-card">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=400" class="profile-img" alt="Profile">
                    <div class="position-absolute bottom-0 start-0 p-3 w-100" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                        <h4 class="text-white mb-0 fw-bold">Leo, 29</h4>
                        <p class="text-white-50 small mb-0"><i class="ri-map-pin-2-line"></i> 3.0 miles away</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="interest-tag">Gym</span>
                        <span class="interest-tag">Crypto</span>
                        <span class="interest-tag">Travel</span>
                    </div>
                    <p class="text-muted small mb-0">"Passionate about fitness and web3. Let's hit the gym or talk about the next big project."</p>
                    
                    <div class="vibe-match">
                        <i class="ri-user-search-line text-info fs-4"></i>
                        <div class="small">
                            <strong>Shared Interest</strong><br>
                            Both of you follow <span class="text-primary">Crypto News</span>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button class="btn-connect">Connect <i class="ri-add-line ms-1"></i></button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>