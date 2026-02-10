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
    <title>AirDrop | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-purple: #8e2de2;
            --mflow-blue: #4a00e0;
            --mflow-gold: #ffcc00;
        }

        body { 
            background: radial-gradient(circle at top, #1a1a2e, #0f0f1a);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        /* AirDrop Card */
        .airdrop-main {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Glowing Effect */
        .airdrop-main::before {
            content: ''; position: absolute; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: conic-gradient(transparent, var(--mflow-purple), transparent);
            animation: rotate 6s linear infinite;
            z-index: -1; opacity: 0.3;
        }

        @keyframes rotate { 100% { transform: rotate(360deg); } }

        .airdrop-icon {
            font-size: 80px;
            color: var(--mflow-gold);
            filter: drop-shadow(0 0 20px rgba(255, 204, 0, 0.5));
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .claim-btn {
            background: linear-gradient(45deg, var(--mflow-purple), var(--mflow-blue));
            border: none; color: #fff; padding: 15px 40px;
            border-radius: 50px; font-weight: 800; font-size: 1.2rem;
            box-shadow: 0 10px 20px rgba(142, 45, 226, 0.4);
            transition: 0.3s;
        }
        .claim-btn:hover { transform: scale(1.05); box-shadow: 0 15px 30px rgba(142, 45, 226, 0.6); }

        .progress-custom {
            height: 10px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden;
        }

        /* Sidebar Glass Panels */
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20px; padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 992px) { .side-col { display: none; } }
    </style>
</head>
<body>

<header class="p-3 d-flex align-items-center justify-content-between">
    <a href="/wallet" class="text-white"><i class="ri-arrow-left-s-line fs-3"></i></a>
    <h5 class="mb-0 fw-bold">Live AirDrop</h5>
    <div class="badge bg-danger">LIVE</div>
</header>

<div class="container-fluid px-lg-5 py-4">
    <div class="row g-4">
        
        <div class="col-lg-3 side-col">
            <h6 class="fw-bold mb-3 text-secondary">MORE OPPORTUNITIES</h6>
            <div class="glass-panel mb-3">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <i class="ri-twitter-x-fill fs-4"></i>
                    <div class="small fw-bold">Twitter Raid</div>
                </div>
                <div class="small text-muted mb-2">Retweet and earn 100 MC</div>
                <button class="btn btn-sm btn-outline-light w-100">Join Task</button>
            </div>
            <div class="glass-panel">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <i class="ri-user-follow-fill fs-4 text-info"></i>
                    <div class="small fw-bold">Referral Bonus</div>
                </div>
                <div class="small text-muted">Claim 500 MC per invite</div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="airdrop-main">
                <div class="airdrop-icon mb-4">
                    <i class="ri-parachute-line"></i>
                </div>
                <h2 class="fw-bold mb-2">Community AirDrop</h2>
                <p class="text-secondary mb-4">You are eligible to claim your weekly monieFlow rewards.</p>
                
                <div class="mb-5">
                    <h1 class="fw-bold display-4 mb-0" style="color: var(--mflow-gold);">500.00 MC</h1>
                    <small class="text-muted text-uppercase">Estimated Value: ₦25,000</small>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Global Claim Progress</span>
                        <span class="text-warning fw-bold">85% Filled</span>
                    </div>
                    <div class="progress-custom">
                        <div class="progress-bar bg-warning" style="width: 85%"></div>
                    </div>
                </div>

                <button class="claim-btn w-100" id="claimBtn" onclick="startClaim()">
                    Claim AirDrop
                </button>
            </div>
        </div>

        <div class="col-lg-3 side-col">
            <h6 class="fw-bold mb-3 text-secondary">YOUR ELIGIBILITY</h6>
            <div class="glass-panel">
                <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                    <span class="small">Account Age</span>
                    <i class="ri-checkbox-circle-fill text-success"></i>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                    <span class="small">5+ Posts</span>
                    <i class="ri-checkbox-circle-fill text-success"></i>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                    <span class="small">Wallet Verified</span>
                    <i class="ri-checkbox-circle-fill text-success"></i>
                </div>
                <div class="mt-4 text-center">
                    <small class="text-muted">Need help?</small><br>
                    <a href="#" class="text-info small text-decoration-none">Read Whitepaper</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function startClaim() {
        const btn = document.getElementById('claimBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        btn.disabled = true;

        // Simulate Blockchain/Server Delay
        setTimeout(() => {
            window.location.href = "/success?type=airdrop";
        }, 3000);
    }
</script>

</body>
</html>