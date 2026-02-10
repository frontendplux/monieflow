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
    <title>Home | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --fb-bg: #f0f2f5;
            --fb-white: #ffffff;
            --fb-blue: #1877f2;
            --fb-gray-text: #65676b;
            --fb-hover: #e4e6eb;
        }

        body {
            background-color: var(--fb-bg);
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
            color: #050505;
        }

        header {
            background: var(--fb-white);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 56px;
            z-index: 1050;
        }

        .brand-logo {
            font-weight: 800; font-size: 1.6rem;
            letter-spacing: -1px;
            background: linear-gradient(45deg, #1877f2, #6366f1);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .fb-nav-icon {
            width: 40px; height: 40px; border-radius: 50%;
            background: #e4e6eb; display: flex; align-items: center;
            justify-content: center; font-size: 20px; cursor: pointer;
            color: #050505; text-decoration: none;
        }

        .sidebar-sticky {
            position: sticky; 
            top: 56px; 
            height: calc(100vh - 56px);
            overflow-y: auto; 
            padding-top: 15px;
        }

        .sidebar-sticky::-webkit-scrollbar { width: 0px; }

        .sidebar-link {
            display: flex; align-items: center; padding: 10px 12px;
            border-radius: 8px; color: #050505; text-decoration: none;
            font-weight: 500; transition: background 0.2s;
        }

        .sidebar-link:hover { background: var(--fb-hover); }
        .sidebar-link i { font-size: 24px; margin-right: 15px; }

        .composer-card {
            background: var(--fb-white); border-radius: 8px;
            padding: 12px 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .composer-input {
            background: #f0f2f5; border-radius: 20px; padding: 8px 15px;
            cursor: pointer; color: var(--fb-gray-text); flex-grow: 1;
            transition: 0.2s;
        }
        .composer-input:hover { background: #e4e6eb; }

        .story-card {
            min-width: 110px; height: 190px; border-radius: 10px;
            overflow: hidden; position: relative; cursor: pointer;
            background: #ddd; box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .story-card img { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .story-card:hover img { transform: scale(1.05); }

        .main-wrapper {
            max-width: 1920px;
            margin: 0 auto;
        }

        /* === RESPONSIVE & FRIEND/COMMENT IMPROVEMENTS === */
        .friend-btn {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }

        .post-header {
            flex-wrap: wrap;
            gap: 12px;
        }

        .comment-box {
            background: #f0f2f5;
            border-radius: 18px;
            padding: 8px 14px;
            font-size: 0.95rem;
        }

        .comment-form .form-control {
            background: #f0f2f5;
            border: none;
            border-radius: 20px !important;
            padding: 8px 16px;
        }

        .comment-form .btn {
            border-radius: 50% !important;
            width: 34px;
            height: 34px;
            padding: 0;
        }

        @media (max-width: 576px) {
            .friend-btn { font-size: 0.75rem; padding: 0.25rem 0.6rem; }
            .sidebar-link { padding: 8px 10px; font-size: 0.92rem; }
            .composer-input { font-size: 0.92rem; padding: 7px 12px; }
            .story-card { min-width: 90px; height: 160px; }
            .post-header .btn { font-size: 0.8rem; }
        }

        @media (max-width: 400px) {
            .main-wrapper .container { padding-left: 8px; padding-right: 8px; }
        }

        @media (max-width: 992px) {
            .sidebar-left, .sidebar-right { display: none; }
        }
    </style>
</head>
<body>

<header class="sticky-top d-flex align-items-center justify-content-between px-3">
    <div class="d-flex align-items-center col-3">
        <div class="brand-logo me-2">monieFlow</div>
        <div class="fb-nav-icon"><i class="ri-search-line"></i></div>
    </div>
    
    <div class="col-6 d-none d-md-flex justify-content-center h-100 align-items-center">
        <a href="#" class="text-primary fs-3 border-bottom border-primary border-3 px-5 py-2"><i class="ri-home-fill"></i></a>
        <a href="#" class="text-secondary fs-3 px-5 py-2"><i class="ri-group-line"></i></a>
        <a href="#" class="text-secondary fs-3 px-5 py-2"><i class="ri-tv-2-line"></i></a>
        <a href="#" class="text-secondary fs-3 px-5 py-2"><i class="ri-store-2-line"></i></a>
    </div>

    <div class="col-3 d-flex justify-content-end gap-2">
        <div class="fb-nav-icon"><i class="ri-menu-line"></i></div>
        <div class="fb-nav-icon"><i class="ri-messenger-fill"></i></div>
        <div class="fb-nav-icon"><i class="ri-notification-3-fill"></i></div>
        <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle border" width="40" height="40">
    </div>
</header>

<div class="main-wrapper">
    <div class="container">
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-lg-3 sidebar-left sidebar-sticky">
                <a href="/profile" class="sidebar-link">
                    <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle me-3" width="36" height="36" style="object-fit: cover;">
                    <span><?= $profile['username'] ?></span>
                </a>
                <a href="#" class="sidebar-link"><i class="ri-group-fill text-primary"></i> Friends</a>
                <a href="#" class="sidebar-link"><i class="ri-history-line text-info"></i> Memories</a>
                <a href="#" class="sidebar-link"><i class="ri-bookmark-fill" style="color:#a855f7"></i> Saved</a>
                <hr>
                <h6 class="px-3 text-secondary">Shortcuts</h6>
                <a href="#" class="sidebar-link"><i class="ri-gamepad-line text-success"></i> Gaming Hub</a>
            </div>

            <!-- Main Feed -->
            <div class="col-12 col-md-10 col-lg-6 mx-auto pt-4">
                <div style="max-width: 590px; margin: 0 auto;">

                    <!-- Stories -->
                    <div class="d-flex gap-2 mb-4 overflow-auto pb-2" style="scrollbar-width: none;">
                        <div class="story-card"><img src="https://picsum.photos/200/300?random=1"></div>
                        <div class="story-card"><img src="https://picsum.photos/200/300?random=2"></div>
                        <div class="story-card"><img src="https://picsum.photos/200/300?random=3"></div>
                        <div class="story-card"><img src="https://picsum.photos/200/300?random=4"></div>
                        <div class="story-card"><img src="https://picsum.photos/200/300?random=5"></div>
                    </div>

                    <!-- Post Composer -->
                    <div class="composer-card mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle me-2" width="40" height="40">
                            <div class="composer-input" data-bs-toggle="modal" data-bs-target="#createPostModal">
                                What's on your mind, <?= $profile['username'] ?>?
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-around">
                            <button class="btn btn-link text-secondary fw-bold flex-grow-1"><i class="ri-video-add-fill text-danger me-2"></i> Live</button>
                            <button class="btn btn-link text-secondary fw-bold flex-grow-1"><i class="ri-image-2-fill text-success me-2"></i> Photo</button>
                            <button class="btn btn-link text-secondary fw-bold flex-grow-1"><i class="ri-emotion-happy-fill text-warning me-2"></i> Feeling</button>
                        </div>
                    </div>

                    <!-- Sample Posts with Friend & Comment Features -->
                    <div id="roots">
                        <!-- Post 1 -->
                        <div class="card p-3 mb-3 border-0 shadow-sm" style="border-radius:12px">
                            <div class="d-flex justify-content-between align-items-start post-header mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://i.pravatar.cc/150?u=7" class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0">Sarah Johnson</h6>
                                        <small class="text-muted">Lagos · 45m · <i class="ri-earth-line"></i></small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary rounded-pill friend-btn">Add Friend</button>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill d-none d-sm-inline">Message</button>
                                    <button class="btn btn-sm"><i class="ri-more-line"></i></button>
                                </div>
                            </div>

                            <p>Just finished a great project today! Feeling accomplished ✨</p>

                            <div class="d-flex justify-content-between border-top pt-3 mt-2 text-muted">
                                <button class="btn btn-link text-muted"><i class="ri-thumb-up-line me-1"></i> Like</button>
                                <button class="btn btn-link text-muted"><i class="ri-chat-3-line me-1"></i> Comment</button>
                                <button class="btn btn-link text-muted"><i class="ri-share-line me-1"></i> Share</button>
                            </div>

                            <!-- Comments -->
                            <div class="mt-3">
                                <div class="d-flex mb-3">
                                    <img src="https://i.pravatar.cc/150?u=8" class="rounded-circle me-2" width="32" height="32">
                                    <div class="comment-box flex-grow-1">
                                        <strong>Mike Ade</strong> 
                                        <span class="small text-muted">Wow, congrats! What's next?</span>
                                    </div>
                                </div>

                                <form class="comment-form d-flex align-items-center mt-2">
                                    <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle me-2" width="32" height="32">
                                    <input type="text" class="form-control me-2" placeholder="Write a comment..." aria-label="Comment">
                                    <button class="btn btn-light" type="submit"><i class="ri-send-plane-2-line"></i></button>
                                </form>
                            </div>
                        </div>

                        <!-- Post 2 -->
                        <div class="card p-3 mb-3 border-0 shadow-sm" style="border-radius:12px">
                            <div class="d-flex justify-content-between align-items-start post-header mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://i.pravatar.cc/150?u=12" class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0">Alex Carter</h6>
                                        <small class="text-muted">2h · Friends</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-success rounded-pill friend-btn">Friends</button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="ri-user-minus-line"></i></button>
                                    <button class="btn btn-sm"><i class="ri-more-line"></i></button>
                                </div>
                            </div>

                            <p>Weekend vibes in Lagos 🌴☀️</p>

                            <div class="d-flex justify-content-between border-top pt-3 mt-2 text-muted">
                                <button class="btn btn-link text-muted"><i class="ri-thumb-up-line me-1"></i> Like</button>
                                <button class="btn btn-link text-muted"><i class="ri-chat-3-line me-1"></i> Comment</button>
                                <button class="btn btn-link text-muted"><i class="ri-share-line me-1"></i> Share</button>
                            </div>

                            <div class="mt-3">
                                <form class="comment-form d-flex align-items-center mt-2">
                                    <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle me-2" width="32" height="32">
                                    <input type="text" class="form-control me-2" placeholder="Write a comment..." aria-label="Comment">
                                    <button class="btn btn-light" type="submit"><i class="ri-send-plane-2-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Contacts with Friend Controls -->
            <div class="col-lg-3 sidebar-right sidebar-sticky">
                <h6 class="text-secondary px-3 mb-3 d-flex justify-content-between">
                    <span>Contacts</span>
                    <div class="d-flex gap-3">
                        <i class="ri-video-add-fill"></i>
                        <i class="ri-search-line"></i>
                        <i class="ri-more-fill"></i>
                    </div>
                </h6>

                <div class="sidebar-link d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <img src="https://i.pravatar.cc/150?u=9" class="rounded-circle me-2" width="32" height="32">
                        <span class="text-truncate">Sarah Stone</span>
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                        <button class="btn btn-sm btn-outline-success rounded-pill friend-btn">Friends</button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="ri-user-minus-line"></i></button>
                    </div>
                </div>

                <div class="sidebar-link d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <img src="https://i.pravatar.cc/150?u=15" class="rounded-circle me-2" width="32" height="32">
                        <span class="text-truncate">Tunde Bello</span>
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                        <button class="btn btn-sm btn-primary rounded-pill friend-btn">Add Friend</button>
                    </div>
                </div>

                <div class="sidebar-link d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <img src="https://i.pravatar.cc/150?u=22" class="rounded-circle me-2" width="32" height="32">
                        <span class="text-truncate">Grace Okoro</span>
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                        <button class="btn btn-sm btn-primary rounded-pill friend-btn">Add Friend</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/api.js"></script>
<script src="/feeds/script.js"></script>

</body>
</html>