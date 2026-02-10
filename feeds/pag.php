<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Page Layout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; }
        
        /* 1. Cover Photo & Profile Header */
        .page-cover {
            height: 350px;
            background: url('https://picsum.photos/1200/400?random=99') center/cover;
            border-radius: 0 0 20px 20px;
        }
        
        .profile-overlap {
            margin-top: -100px;
            padding: 0 30px;
        }

        .profile-pic-container {
            width: 170px;
            height: 170px;
            border: 5px solid white;
            border-radius: 50%;
            overflow: hidden;
            background: #fff;
        }

        /* 2. Navigation Tabs */
        .nav-page-tabs .nav-link {
            color: #65676b;
            font-weight: 600;
            border: none;
            padding: 15px 20px;
            border-bottom: 3px solid transparent;
        }

        .nav-page-tabs .nav-link.active {
            color: #1877f2;
            background: none;
            border-bottom: 3px solid #1877f2;
        }

        /* 3. Cards & Components */
        .glass-card {
            background: #ffffff;
            border-radius: 15px;
            border: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .btn-action {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 20px;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .page-cover { height: 200px; }
            .profile-overlap { margin-top: -60px; text-align: center; }
            .profile-pic-container { width: 120px; height: 120px; margin: 0 auto; }
            .action-buttons { margin-top: 15px; justify-content: center !important; }
        }
    </style>
</head>
<body>

<div class="bg-white shadow-sm mb-4">
    <div class="container p-0">
        <div class="page-cover position-relative">
            <button class="btn btn-light btn-sm position-absolute bottom-0 end-0 m-3 shadow-sm rounded-pill">
                <i class="ri-camera-line"></i> Edit Cover
            </button>
        </div>

        <div class="profile-overlap d-md-flex align-items-end justify-content-between">
            <div class="d-md-flex align-items-end gap-3">
                <div class="profile-pic-container shadow-sm">
                    <img src="https://picsum.photos/200/200?random=1" class="w-100 h-100 object-fit-cover">
                </div>
                <div class="mb-2">
                    <h1 class="fw-bold mb-0">Official monieFlow Page <i class="ri-checkbox-circle-fill text-primary fs-4"></i></h1>
                    <p class="text-muted fw-semibold mb-0">1.2M Followers • 45 Following</p>
                </div>
            </div>
            <div class="d-flex gap-2 mb-2 action-buttons">
                <button class="btn btn-primary btn-action"><i class="ri-add-line"></i> Follow</button>
                <button class="btn btn-light btn-action"><i class="ri-messenger-line"></i> Message</button>
                <button class="btn btn-light btn-action"><i class="ri-more-fill"></i></button>
            </div>
        </div>

        <hr class="mx-3 mt-4 mb-0 opacity-10">

        <div class="container px-3">
            <nav class="nav nav-page-tabs">
                <a class="nav-link active" href="#">Posts</a>
                <a class="nav-link" href="#">About</a>
                <a class="nav-link" href="#">Mentions</a>
                <a class="nav-link" href="#">Reviews</a>
                <a class="nav-link" href="#">Followers</a>
            </nav>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        
        <div class="col-lg-5">
            <div class="card glass-card p-3 mb-4">
                <h5 class="fw-bold mb-3">Intro</h5>
                <p class="text-center py-2">Welcome to the Official monieFlow Page. Connect, Trade, and Earn! 🚀</p>
                <button class="btn btn-light w-100 fw-bold mb-3">Edit Bio</button>
                
                <div class="list-unstyled mb-0">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="ri-global-line text-muted fs-4"></i>
                        <span>Web3 Financial Ecosystem</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="ri-map-pin-2-line text-muted fs-4"></i>
                        <span>Lagos, Nigeria</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="ri-link text-muted fs-4"></i>
                        <a href="#" class="text-decoration-none fw-bold">monieflow.com</a>
                    </div>
                </div>
            </div>

            <div class="card glass-card p-3">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">Photos</h5>
                    <a href="#" class="text-decoration-none small">See All</a>
                </div>
                <div class="row g-2">
                    <div class="col-4"><img src="https://picsum.photos/150/150?random=10" class="w-100 rounded-3"></div>
                    <div class="col-4"><img src="https://picsum.photos/150/150?random=11" class="w-100 rounded-3"></div>
                    <div class="col-4"><img src="https://picsum.photos/150/150?random=12" class="w-100 rounded-3"></div>
                    <div class="col-4"><img src="https://picsum.photos/150/150?random=13" class="w-100 rounded-3"></div>
                    <div class="col-4"><img src="https://picsum.photos/150/150?random=14" class="w-100 rounded-3"></div>
                    <div class="col-4"><img src="https://picsum.photos/150/150?random=15" class="w-100 rounded-3"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card glass-card p-3 mb-4">
                <div class="d-flex gap-2">
                    <img src="https://picsum.photos/200/200?random=1" class="rounded-circle" width="40" height="40">
                    <button class="btn btn-light rounded-pill w-100 text-start px-3 text-muted">What's on your mind?</button>
                </div>
                <hr class="my-3 opacity-10">
                <div class="d-flex justify-content-around">
                    <button class="btn btn-link text-decoration-none text-muted fw-bold small"><i class="ri-image-line text-success"></i> Photo</button>
                    <button class="btn btn-link text-decoration-none text-muted fw-bold small"><i class="ri-video-line text-danger"></i> Reel</button>
                    <button class="btn btn-link text-decoration-none text-muted fw-bold small"><i class="ri-flag-line text-warning"></i> Life Event</button>
                </div>
            </div>

            <div class="card glass-card mb-4">
                <div class="p-3">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex gap-2">
                            <img src="https://picsum.photos/200/200?random=1" class="rounded-circle" width="40" height="40">
                            <div>
                                <h6 class="fw-bold mb-0">monieFlow Page</h6>
                                <small class="text-muted">2 hours ago • <i class="ri-earth-fill"></i></small>
                            </div>
                        </div>
                        <i class="ri-more-fill fs-4 text-muted"></i>
                    </div>
                    <p class="mt-3">Big news coming to the monieFlow Barn tomorrow! Are you ready to harvest? 🌾💰</p>
                </div>
                <img src="https://picsum.photos/800/450?random=44" class="w-100">
                <div class="p-3">
                    <div class="d-flex justify-content-between text-muted small mb-3">
                        <span><i class="ri-heart-fill text-danger"></i> 1.5k</span>
                        <span>42 Comments • 12 Shares</span>
                    </div>
                    <hr class="opacity-10 my-2">
                    <div class="d-flex justify-content-around">
                        <button class="btn btn-link text-decoration-none text-muted fw-bold small"><i class="ri-thumb-up-line"></i> Like</button>
                        <button class="btn btn-link text-decoration-none text-muted fw-bold small"><i class="ri-chat-1-line"></i> Comment</button>
                        <button class="btn btn-link text-decoration-none text-muted fw-bold small"><i class="ri-share-forward-line"></i> Share</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>