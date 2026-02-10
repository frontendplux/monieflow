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
    <title>Tasks | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --fb-bg: #f0f2f5;
            --fb-blue: #1877f2;
            --fb-hover: #e4e6eb;
            --success: #42b72a;
        }

        body { background-color: var(--fb-bg); font-family: 'Segoe UI', sans-serif; }

        /* Left Nav */
        .task-sidebar {
            background: #fff;
            height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
            padding: 20px;
            border-right: 1px solid var(--fb-hover);
        }

        .filter-item {
            display: flex; align-items: center; padding: 12px;
            border-radius: 8px; color: #050505; text-decoration: none;
            font-weight: 600; margin-bottom: 5px;
        }
        .filter-item:hover, .filter-item.active { background: var(--fb-hover); color: var(--fb-blue); }
        .filter-item i { font-size: 20px; margin-right: 12px; }

        /* Task Cards */
        .task-card {
            background: #fff; border-radius: 12px; padding: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1); margin-bottom: 16px;
            border: 1px solid transparent; transition: 0.2s;
        }
        .task-card:hover { border-color: var(--fb-blue); }

        .progress { height: 8px; border-radius: 10px; }
        
        .reward-tag {
            background: rgba(66, 183, 42, 0.1); color: var(--success);
            padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 14px;
        }

        /* Right Panel */
        .earnings-card {
            background: linear-gradient(135deg, #1877f2, #0056b3);
            color: #fff; border-radius: 12px; padding: 20px; margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .task-sidebar, .right-panel { display: none; }
        }
    </style>
</head>
<body>

<header class="sticky-top bg-white border-bottom d-flex align-items-center justify-content-between px-3" style="height: 56px;">
    <div class="d-flex align-items-center">
        <div class="brand-logo fw-bold text-primary fs-4">monieFlow</div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="fw-bold text-success"><i class="ri-wallet-3-line"></i> ₦45,200</div>
        <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle" width="35">
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="col-lg-3 task-sidebar">
            <h5 class="fw-bold mb-4">Task Center</h5>
            <nav>
                <a href="#" class="filter-item active"><i class="ri-layout-grid-line"></i> All Tasks</a>
                <a href="#" class="filter-item"><i class="ri-time-line"></i> In Progress</a>
                <a href="#" class="filter-item"><i class="ri-checkbox-circle-line"></i> Completed</a>
                <a href="#" class="filter-item"><i class="ri-star-line"></i> High Priority</a>
                <hr>
                <a href="#" class="filter-item text-secondary"><i class="ri-settings-3-line"></i> Settings</a>
            </nav>
        </div>

        <div class="col-12 col-lg-6 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Active Missions</h4>
                <button class="btn btn-primary rounded-pill btn-sm px-3">+ New Task</button>
            </div>

            <div class="task-card">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge bg-light text-primary border">Development</span>
                    <span class="reward-tag">₦15,000</span>
                </div>
                <h5 class="fw-bold">Fix monieFlow Swiper Responsiveness</h5>
                <p class="text-secondary small">Ensure the marketplace carousel works perfectly on iPhone 15 Pro Max screens.</p>
                
                <div class="d-flex justify-content-between mb-2 mt-4">
                    <span class="small fw-bold">Progress</span>
                    <span class="small text-muted">75%</span>
                </div>
                <div class="progress mb-4">
                    <div class="progress-bar bg-primary" style="width: 75%"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex -space-x-2">
                        <img src="https://i.pravatar.cc/150?u=a1" class="rounded-circle border border-white" width="30" style="margin-right: -10px;">
                        <img src="https://i.pravatar.cc/150?u=a2" class="rounded-circle border border-white" width="30">
                    </div>
                    <button class="btn btn-outline-primary btn-sm fw-bold px-4">Update</button>
                </div>
            </div>

            <div class="task-card">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge bg-light text-info border">UI Design</span>
                    <span class="reward-tag">₦5,000</span>
                </div>
                <h5 class="fw-bold">Design App Icon for "Reels"</h5>
                <p class="text-secondary small">Create a minimalist icon for the new video feature using the monieFlow brand colors.</p>
                
                <div class="d-flex justify-content-between mb-2 mt-4">
                    <span class="small fw-bold">Progress</span>
                    <span class="small text-muted">0%</span>
                </div>
                <div class="progress mb-4">
                    <div class="progress-bar bg-secondary" style="width: 5%"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-danger small fw-bold"><i class="ri-time-line"></i> Due Tomorrow</span>
                    <button class="btn btn-primary btn-sm fw-bold px-4">Start Task</button>
                </div>
            </div>
        </div>

        <div class="col-lg-3 p-4 right-panel">
            <div class="earnings-card">
                <small class="opacity-75">Available Balance</small>
                <h2 class="fw-bold">₦45,200.00</h2>
                <hr class="opacity-25">
                <div class="d-flex justify-content-between">
                    <span>This Week</span>
                    <span class="fw-bold">+₦12,000</span>
                </div>
            </div>

            <div class="bg-white rounded-3 p-3 shadow-sm">
                <h6 class="fw-bold mb-3">Top Earners</h6>
                <div class="d-flex align-items-center mb-3">
                    <span class="fw-bold me-3">1</span>
                    <img src="https://i.pravatar.cc/150?u=user1" class="rounded-circle me-2" width="30">
                    <div class="flex-grow-1 small fw-bold">Tunde Ade</div>
                    <div class="text-success small fw-bold">₦250k</div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="fw-bold me-3">2</span>
                    <img src="https://i.pravatar.cc/150?u=user2" class="rounded-circle me-2" width="30">
                    <div class="flex-grow-1 small fw-bold">Sarah S.</div>
                    <div class="text-success small fw-bold">₦190k</div>
                </div>
                <button class="btn btn-light w-100 btn-sm fw-bold">View Leaderboard</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>