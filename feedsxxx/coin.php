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
    <title>Coin Wallet | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-gold: #ffcc00;
            --mflow-dark: #1a1a1a;
            --mflow-blue: #1877f2;
            --fb-bg: #f0f2f5;
        }

        body { background-color: var(--fb-bg); font-family: 'Segoe UI', sans-serif; color: #333; }

        /* Wallet Card */
        .wallet-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
            border-radius: 20px;
            padding: 30px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .wallet-card::after {
            content: ''; position: absolute; top: -50px; right: -50px;
            width: 150px; height: 150px; background: rgba(255, 204, 0, 0.1);
            border-radius: 50%;
        }

        .coin-balance { font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; }
        .coin-symbol { color: var(--mflow-gold); margin-right: 10px; }

        /* Action Buttons */
        .action-circle {
            width: 55px; height: 55px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: #fff; color: var(--mflow-dark);
            font-size: 24px; margin: 0 auto 10px;
            transition: 0.3s; cursor: pointer; border: 1px solid #eee;
        }
        .action-circle:hover { background: var(--mflow-blue); color: #fff; transform: translateY(-3px); }

        /* Transaction List */
        .trx-item {
            background: #fff; border-radius: 12px; padding: 15px;
            margin-bottom: 10px; border: 1px solid transparent;
            display: flex; align-items: center; justify-content: space-between;
        }
        .trx-icon {
            width: 45px; height: 45px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-right: 15px;
        }

        /* Sidebar Nav */
        .wallet-nav-item {
            display: flex; align-items: center; padding: 12px 15px;
            border-radius: 10px; color: #555; text-decoration: none;
            font-weight: 600; margin-bottom: 5px;
        }
        .wallet-nav-item.active { background: var(--mflow-blue); color: #fff; }
        .wallet-nav-item i { font-size: 20px; margin-right: 15px; }

        @media (max-width: 992px) {
            .side-panel { display: none; }
        }
    </style>
</head>
<body>

<header class="sticky-top bg-white border-bottom d-flex align-items-center justify-content-between px-4" style="height: 65px; z-index: 1000;">
    <div class="d-flex align-items-center">
        <a href="/home" class="text-dark me-3"><i class="ri-arrow-left-s-line fs-3"></i></a>
        <h5 class="mb-0 fw-bold">Coin Wallet</h5>
    </div>
    <div class="d-flex align-items-center gap-3">
        <i class="ri-qr-code-line fs-4 cursor-pointer"></i>
        <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle" width="35">
    </div>
</header>

<div class="container py-4">
    <div class="row">
        
        <div class="col-lg-3 side-panel">
            <div class="bg-white rounded-4 p-3 shadow-sm">
                <nav>
                    <a href="#" class="wallet-nav-item active"><i class="ri-wallet-3-line"></i> Assets</a>
                    <a href="#" class="wallet-nav-item"><i class="ri-arrow-left-right-line"></i> Swap</a>
                    <a href="#" class="wallet-nav-item"><i class="ri-bank-card-line"></i> Card</a>
                    <a href="#" class="wallet-nav-item"><i class="ri-safe-2-line"></i> Staking</a>
                    <hr>
                    <a href="#" class="wallet-nav-item"><i class="ri-shield-check-line"></i> Security</a>
                </nav>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            
            <div class="wallet-card mb-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <small class="text-uppercase opacity-75 fw-bold">Total monieCoin</small>
                        <div class="coin-balance mt-1">
                            <span class="coin-symbol">MC</span>12,450.80
                        </div>
                        <small class="text-success fw-bold">≈ ₦622,540.00</small>
                    </div>
                    <img src="https://cdn-icons-png.flaticon.com/512/825/825540.png" width="50" style="filter: brightness(0) invert(1);">
                </div>
                
                <div class="row text-center mt-4 g-0">
                    <div class="col-4">
                        <div class="action-circle"><i class="ri-arrow-up-line"></i></div>
                        <small class="fw-bold">Send</small>
                    </div>
                    <div class="col-4">
                        <div class="action-circle"><i class="ri-arrow-down-line"></i></div>
                        <small class="fw-bold">Receive</small>
                    </div>
                    <div class="col-4">
                        <div class="action-circle"><i class="ri-add-line"></i></div>
                        <small class="fw-bold">Buy</small>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Recent Activity</h6>
                <a href="#" class="text-decoration-none small fw-bold">View All</a>
            </div>

            <div class="trx-list">
                <div class="trx-item shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="trx-icon bg-light text-primary"><i class="ri-shopping-bag-3-line"></i></div>
                        <div>
                            <div class="fw-bold small">Marketplace Purchase</div>
                            <div class="text-muted" style="font-size: 11px;">Feb 9, 2026 • 10:45 PM</div>
                        </div>
                    </div>
                    <div class="text-danger fw-bold">-250 MC</div>
                </div>

                <div class="trx-item shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="trx-icon bg-light text-success"><i class="ri-gift-line"></i></div>
                        <div>
                            <div class="fw-bold small">Task Reward: Swiper Fix</div>
                            <div class="text-muted" style="font-size: 11px;">Feb 8, 2026 • 02:20 PM</div>
                        </div>
                    </div>
                    <div class="text-success fw-bold">+1,500 MC</div>
                </div>

                <div class="trx-item shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="trx-icon bg-light text-warning"><i class="ri-user-received-line"></i></div>
                        <div>
                            <div class="fw-bold small">Received from @tunde_ade</div>
                            <div class="text-muted" style="font-size: 11px;">Feb 7, 2026 • 09:12 AM</div>
                        </div>
                    </div>
                    <div class="text-success fw-bold">+50 MC</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 side-panel">
            <div class="bg-white rounded-4 p-3 shadow-sm mb-4">
                <h6 class="fw-bold mb-3">Coin Value</h6>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted">1 MC = ₦50.00</span>
                    <span class="badge bg-success">+2.4%</span>
                </div>
                <div class="bg-light rounded-3 d-flex align-items-end justify-content-around p-2" style="height: 100px;">
                    <div class="bg-primary opacity-25" style="width: 15%; height: 40%; border-radius: 4px;"></div>
                    <div class="bg-primary opacity-50" style="width: 15%; height: 60%; border-radius: 4px;"></div>
                    <div class="bg-primary opacity-75" style="width: 15%; height: 50%; border-radius: 4px;"></div>
                    <div class="bg-primary" style="width: 15%; height: 85%; border-radius: 4px;"></div>
                </div>
            </div>

            <div class="bg-info bg-opacity-10 border border-info border-opacity-25 rounded-4 p-3 shadow-sm">
                <h6 class="fw-bold text-info"><i class="ri-lightbulb-line"></i> Earn more!</h6>
                <p class="small text-dark opacity-75">Invite 5 friends to monieFlow and get 500 MC instantly.</p>
                <button class="btn btn-info btn-sm w-100 fw-bold text-white">Invite Friends</button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>