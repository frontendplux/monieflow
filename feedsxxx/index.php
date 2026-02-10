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

    <!-- Swiper.js CSS (latest stable bundle) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

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

        /* Friend / Comment styles (unchanged) */
        .friend-btn { font-size: 0.85rem; padding: 0.35rem 0.75rem; }
        .post-header { flex-wrap: wrap; gap: 12px; }
        .comment-box { background: #f0f2f5; border-radius: 18px; padding: 8px 14px; font-size: 0.95rem; }
        .comment-form .form-control { background: #f0f2f5; border: none; border-radius: 20px !important; padding: 8px 16px; }
        .comment-form .btn { border-radius: 50% !important; width: 34px; height: 34px; padding: 0; }

        /* === MARKETPLACE SECTION STYLES === */
        .marketplace-section {
            background: var(--fb-white);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .marketplace-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .marketplace-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .product-img {
            height: 180px;
            background: #f8f9fa;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 12px 14px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-weight: 600;
            font-size: 1rem;
            line-height: 1.3;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            color: var(--fb-blue);
            font-weight: 700;
            font-size: 1.1rem;
            margin: 4px 0;
        }

        .product-location {
            font-size: 0.875rem;
            color: var(--fb-gray-text);
            margin-top: auto;
        }

        /* Responsive tweaks */
        @media (max-width: 576px) {
            .friend-btn { font-size: 0.75rem; padding: 0.25rem 0.6rem; }
            .sidebar-link { padding: 8px 10px; font-size: 0.92rem; }
            .composer-input { font-size: 0.92rem; padding: 7px 12px; }
            .story-card { min-width: 90px; height: 160px; }
            .product-card { height: auto; }
            .product-img { height: 150px; }
            .product-title { font-size: 0.95rem; }
            .product-price { font-size: 1rem; }
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

                    <!-- === MARKETPLACE SECTION (Swiper Carousel) === -->
                    <div class="marketplace-section">
                        <div class="marketplace-header">
                            <h5>Marketplace Picks for You</h5>
                            <a href="#" class="text-primary fw-medium text-decoration-none">See all</a>
                        </div>

                        <div class="swiper marketplace-swiper">
                            <div class="swiper-wrapper">

                                <!-- Slide 1 -->
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="product-img">
                                            <img src="https://picsum.photos/300/300?random=21" alt="Smartphone">
                                        </div>
                                        <div class="product-info">
                                            <div class="product-title">iPhone 13 128GB - Very Clean</div>
                                            <div class="product-price">₦340,000</div>
                                            <div class="product-location">Ikeja, Lagos</div>
                                            <button class="btn btn-sm btn-outline-primary w-100 mt-3 rounded-pill">Message Seller</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Slide 2 -->
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="product-img">
                                            <img src="https://picsum.photos/300/300?random=22" alt="Laptop">
                                        </div>
                                        <div class="product-info">
                                            <div class="product-title">Dell Inspiron 15 - i7, 16GB RAM</div>
                                            <div class="product-price">₦420,000</div>
                                            <div class="product-location">Lekki Phase 1</div>
                                            <button class="btn btn-sm btn-outline-primary w-100 mt-3 rounded-pill">Message Seller</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Slide 3 -->
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="product-img">
                                            <img src="https://picsum.photos/300/300?random=23" alt="Shoes">
                                        </div>
                                        <div class="product-info">
                                            <div class="product-title">Nike Air Max 270 - Size 43</div>
                                            <div class="product-price">₦85,000</div>
                                            <div class="product-location">Abuja</div>
                                            <button class="btn btn-sm btn-outline-primary w-100 mt-3 rounded-pill">Message Seller</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Slide 4 -->
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="product-img">
                                            <img src="https://picsum.photos/300/300?random=24" alt="Headphones">
                                        </div>
                                        <div class="product-info">
                                            <div class="product-title">Sony WH-1000XM5 Noise Cancelling</div>
                                            <div class="product-price">₦195,000</div>
                                            <div class="product-location">Port Harcourt</div>
                                            <button class="btn btn-sm btn-outline-primary w-100 mt-3 rounded-pill">Message Seller</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- You can add 5–10 more slides dynamically later -->
                            </div>

                            <!-- Navigation & Pagination -->
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-pagination mt-3"></div>
                        </div>
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

                    <!-- Sample Posts -->
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

                        <!-- Post 2 (unchanged) -->
                        <div class="card p-3 mb-3 border-0 shadow-sm" style="border-radius:12px">
                            <!-- ... your second post content here ... -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-3 sidebar-right sidebar-sticky">
                <h6 class="text-secondary px-3 mb-3 d-flex justify-content-between">
                    <span>Contacts</span>
                    <div class="d-flex gap-3">
                        <i class="ri-video-add-fill"></i>
                        <i class="ri-search-line"></i>
                        <i class="ri-more-fill"></i>
                    </div>
                </h6>

                <!-- Your existing contacts with friend buttons -->
                <div class="sidebar-link d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <!-- ... existing contact items ... -->
                </div>
                <!-- ... more contacts ... -->
            </div>
        </div>
    </div>
</div>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
// Initialize Marketplace Swiper
const marketplaceSwiper = new Swiper('.marketplace-swiper', {
    slidesPerView: 1.2,
    spaceBetween: 12,
    loop: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        480:  { slidesPerView: 1.8, spaceBetween: 16 },
        768:  { slidesPerView: 2.5, spaceBetween: 20 },
        992:  { slidesPerView: 3.2, spaceBetween: 20 },
        1200: { slidesPerView: 3.8, spaceBetween: 24 }
    }
});
</script>
<style>
    
        /* Sidebar Logic */
        .sidebar-sticky {
            position: sticky; 
            top: 56px; 
            height: calc(100vh - 56px);
            overflow-y: auto; 
            padding-top: 15px;
        }

        /* Hide scrollbar for sidebars */
        .sidebar-sticky::-webkit-scrollbar { width: 0px; }

        .sidebar-link {
            display: flex; align-items: center; padding: 10px 12px;
            border-radius: 8px; color: #050505; text-decoration: none;
            font-weight: 500; transition: background 0.2s;
        }

        .sidebar-link:hover { background: var(--fb-hover); }
        .sidebar-link i { font-size: 24px; margin-right: 15px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/api.js"></script>
<script src="/feeds/script.js"></script>

</body>
</html>