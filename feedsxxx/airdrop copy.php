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
    <title>AirDrop Pro | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-purple: #8e2de2;
            --mflow-blue: #4a00e0;
            --mflow-gold: #ffcc00;
            --glass: rgba(255, 255, 255, 0.05);
        }

        body { 
            background: radial-gradient(circle at top left, #1a1a2e, #050505);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        /* Promotion Header Banner */
        .promo-banner {
            background: linear-gradient(90deg, var(--mflow-purple), var(--mflow-blue));
            padding: 10px; text-align: center; font-size: 13px; font-weight: 700;
        }

        .airdrop-main {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            padding: 40px;
            position: relative;
        }

        /* Task Cards */
        .task-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 12px;
            transition: 0.3s;
        }
        .task-item:hover { background: rgba(255,255,255,0.07); border-color: var(--mflow-gold); }
        .task-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }

        .multiplier-badge {
            background: var(--mflow-gold); color: #000;
            padding: 2px 10px; border-radius: 5px; font-weight: 800; font-size: 12px;
        }

        .btn-claim {
            background: linear-gradient(45deg, var(--mflow-purple), var(--mflow-blue));
            border: none; color: #fff; padding: 18px;
            border-radius: 15px; font-weight: 800; width: 100%;
            box-shadow: 0 10px 20px rgba(74, 0, 224, 0.3);
        }

        .btn-claim:disabled { background: #333; opacity: 0.6; }

        @media (max-width: 992px) { .side-col { display: none; } }
    </style>
</head>
<body>

<div class="promo-banner">
    🚀 PROMO: First 1,000 users to complete all tasks get a 2x Multiplier! <span class="ms-2">412/1000 left</span>
</div>

<header class="p-3 d-flex align-items-center justify-content-between">
    <a href="/wallet" class="text-white"><i class="ri-arrow-left-s-line fs-3"></i></a>
    <div class="d-flex align-items-center gap-2">
        <i class="ri-flashlight-fill text-warning"></i>
        <h5 class="mb-0 fw-bold">Daily Mission</h5>
    </div>
    <div class="text-warning fw-bold">Season 1</div>
</header>

<div class="container py-4">
    <div class="row g-4">
        
        <div class="col-lg-4 side-col">
            <div class="airdrop-main p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="ri-medal-line me-2"></i>My Standing</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary small">Current Points</span>
                    <span class="fw-bold text-warning">1,250 XP</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-secondary small">Global Rank</span>
                    <span class="fw-bold">#4,102</span>
                </div>
                <hr class="opacity-10">
                <p class="small text-muted">Complete more tasks to climb the leaderboard and unlock exclusive NFT badges.</p>
            </div>

            <div class="airdrop-main p-4">
                <h6 class="fw-bold mb-3"><i class="ri-group-line me-2"></i>Referral Promo</h6>
                <div class="text-center">
                    <small class="text-secondary">Earn 50 MC for every friend</small>
                    <div class="bg-dark p-2 rounded mt-2 mb-3 border border-secondary small">
                        mflow.io/ref/<?= $userData['id'] ?>
                    </div>
                    <button class="btn btn-sm btn-outline-light w-100">Copy Link</button>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="airdrop-main">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Unlock AirDrop</h2>
                    <p class="text-secondary">Complete at least 3 tasks to claim your 500 MC</p>
                </div>

                <div class="task-list">
                    <div class="task-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="task-icon bg-primary bg-opacity-10 text-primary"><i class="ri-twitter-x-fill"></i></div>
                            <div>
                                <div class="fw-bold small">Follow monieFlow on X</div>
                                <div class="multiplier-badge">+1.2x Multiplier</div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary rounded-pill px-3" onclick="completeTask(this)">Follow</button>
                    </div>

                    <div class="task-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="task-icon bg-danger bg-opacity-10 text-danger"><i class="ri-video-line"></i></div>
                            <div>
                                <div class="fw-bold small">Watch Promo Video</div>
                                <div class="multiplier-badge">+1.1x Multiplier</div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary rounded-pill px-3" onclick="completeTask(this)">Watch</button>
                    </div>

                    <div class="task-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="task-icon bg-success bg-opacity-10 text-success"><i class="ri-chat-smile-2-line"></i></div>
                            <div>
                                <div class="fw-bold small">Invite 2 Friends</div>
                                <div class="multiplier-badge">+1.5x Multiplier</div>
                            </div>
                        </div>
                        <span class="text-muted small">0/2 Done</span>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-top border-secondary border-opacity-25">
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <div>
                            <small class="text-muted d-block">Current Reward</small>
                            <h3 class="fw-bold mb-0 text-warning" id="rewardValue">500.00 MC</h3>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Required Tasks</small>
                            <span class="fw-bold" id="taskCount">0/3 Completed</span>
                        </div>
                    </div>
                    <button class="btn-claim" id="claimBtn" disabled onclick="startClaim()">
                        Claim AirDrop
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>



<script>
    let completed = 0;
    let baseReward = 500;

    function completeTask(btn) {
        if(btn.classList.contains('btn-success')) return;
        
        btn.innerHTML = '<i class="ri-check-line"></i> Done';
        btn.classList.replace('btn-primary', 'btn-success');
        
        completed++;
        document.getElementById('taskCount').innerText = `${completed}/3 Completed`;
        
        // Increase reward visual
        baseReward += 50;
        document.getElementById('rewardValue').innerText = baseReward.toFixed(2) + " MC";

        if(completed >= 3) {
            document.getElementById('claimBtn').disabled = false;
        }
    }

    function startClaim() {
        const btn = document.getElementById('claimBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying Tasks...';
        setTimeout(() => {
            window.location.href = "/success?type=airdrop";
        }, 3000);
    }
</script>

</body>
</html>