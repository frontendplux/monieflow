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
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
            font-weight: 800; 
            font-size: 1.6rem;
            letter-spacing: -1px;
            background: linear-gradient(45deg, #1877f2, #6366f1);
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent;
        }

        .fb-nav-icon {
            width: 40px; 
            height: 40px; 
            border-radius: 50%;
            background: #e4e6eb; 
            display: flex; 
            align-items: center;
            justify-content: center; 
            font-size: 20px; 
            cursor: pointer;
            color: #050505; 
            text-decoration: none;
        }

        .sidebar-sticky {
            position: sticky; 
            top: 56px; 
            height: calc(100vh - 56px);
            overflow-y: auto; 
            padding-top: 15px;
        }

        .sidebar-sticky::-webkit-scrollbar { 
            width: 0px; 
        }

        .sidebar-link {
            display: flex; 
            align-items: center; 
            padding: 10px 12px;
            border-radius: 8px; 
            color: #050505; 
            text-decoration: none;
            font-weight: 500; 
            transition: background 0.2s;
        }

        .sidebar-link:hover { 
            background: var(--fb-hover); 
        }

        .sidebar-link i { 
            font-size: 24px; 
            margin-right: 15px; 
        }

        .composer-card {
            background: var(--fb-white); 
            border-radius: 8px;
            padding: 12px 16px; 
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .composer-input {
            background: #f0f2f5; 
            border-radius: 20px; 
            padding: 8px 15px;
            cursor: pointer; 
            color: var(--fb-gray-text); 
            flex-grow: 1;
            transition: 0.2s;
        }

        .composer-input:hover { 
            background: #e4e6eb; 
        }

        .story-card {
            min-width: 110px; 
            height: 190px; 
            border-radius: 10px;
            overflow: hidden; 
            position: relative; 
            cursor: pointer;
            background: #ddd; 
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .story-card img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: 0.3s; 
        }

        .story-card:hover img { 
            transform: scale(1.05); 
        }

        .main-wrapper {
            max-width: 1920px;
            margin: 0 auto;
        }

        /* Friend / Comment styles */
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

        /* MARKETPLACE SECTION STYLES */
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
            .friend-btn { 
                font-size: 0.75rem; 
                padding: 0.25rem 0.6rem; 
            }

            .sidebar-link { 
                padding: 8px 10px; 
                font-size: 0.92rem; 
            }

            .composer-input { 
                font-size: 0.92rem; 
                padding: 7px 12px; 
            }

            .story-card { 
                min-width: 90px; 
                height: 160px; 
            }

            .product-card { 
                height: auto; 
            }

            .product-img { 
                height: 150px; 
            }

            .product-title { 
                font-size: 0.95rem; 
            }

            .product-price { 
                font-size: 1rem; 
            }
        }

        @media (max-width: 400px) {
            .main-wrapper .container { 
                padding-left: 8px; 
                padding-right: 8px; 
            }
        }

        @media (max-width: 992px) {
            .sidebar-left, .sidebar-right { 
                display: none; 
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__."/../header.php"; ?>
    <div class="main-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 sidebar-left sidebar-sticky">
                    <a href="/profile/" class="sidebar-link">
                        <img src="/uploads/<?= $profile['profile_pic'] ?>" class="rounded-circle me-3" width="36" height="36" style="object-fit: cover;">
                        <span><?= $profile['username'] ?></span>
                    </a>
                    <a href="/friends" class="sidebar-link"><i class="ri-group-fill text-primary"></i> Friends</a>
                    <a href="/memories" class="sidebar-link"><i class="ri-history-line text-info"></i> Memories</a>
                    <a href="/saved" class="sidebar-link"><i class="ri-bookmark-fill" style="color:#a855f7"></i> Saved</a>
                    <a href="/pages" class="sidebar-link"><i class="ri-flag-fill" style="color:#f97316"></i> Pages</a>
                    <a href="/events" class="sidebar-link"><i class="ri-calendar-event-fill text-danger"></i> Events</a>
                    <hr>
                    <h6 class="px-3 text-secondary">Shortcuts</h6>
                    <a href="/gaming" class="sidebar-link"><i class="ri-gamepad-line text-success"></i> Gaming Hub</a>
                </div>

                <div class="col-12 col-md-10 col-lg-6 mx-auto pt-4 d-flex justify-content-center">
                    <div style="width: 100%; max-width: 590px;">
                        <!-- Stories/Reels Section (Horizontal Scroll) -->
                       <div class="container px-0">
    <div class="d-flex gap-3 pb-3 overflow-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
        
        <a href="/feeds/reel/create.php" class="card text-decoration-none border-0 shadow-sm flex-shrink-0" style="width: 130px; border-radius: 15px; overflow: hidden;">
            <div style="height: 150px; background: url('https://picsum.photos/200/300?random=1') center/cover;"></div>
            <div class="card-body p-0 position-relative text-center" style="height: 60px; background: #fff;">
                <div class="position-absolute translate-middle-x start-50" style="top: -18px;">
                    <div class="bg-primary border border-3 border-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="ri-add-line text-white fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 small fw-bold text-dark">Create Reel</div>
            </div>
        </a>

        <a href="/feeds/reel/" class="position-relative flex-shrink-0 shadow-sm user-reel" style="width: 130px; height: 210px; border-radius: 15px; overflow: hidden;">
            <div class="h-100 w-100" style="background: url('https://picsum.photos/200/300?random=12') center/cover;"></div>
            
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-2" 
                 style="background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.8) 100%);">
                
                <div class="avatar-ring">
                    <img src="https://i.pravatar.cc/100?u=jack" class="rounded-circle border border-2 border-primary" width="35" height="35">
                </div>

                <div class="blur-footer rounded-2 py-1 px-2">
                    <div class="text-white small fw-semibold text-truncate" style="font-size: 0.75rem;">Jack_Dorsey</div>
                </div>
            </div>
        </a>

        <div class="position-relative flex-shrink-0 shadow-sm user-reel" style="width: 130px; height: 210px; border-radius: 15px; overflow: hidden;">
            <div class="h-100 w-100" style="background: url('https://picsum.photos/200/300?random=45') center/cover;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-2" 
                 style="background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.9) 100%);">
                <img src="https://i.pravatar.cc/100?u=sarah" class="rounded-circle border border-2 border-primary" width="35" height="35">
                <div class="blur-footer rounded-2 py-1 px-2">
                    <div class="text-white small fw-semibold text-truncate" style="font-size: 0.75rem;">Sarah.Flow</div>
                </div>
            </div>
        </div>

    </div>
</div>

                        <div class="composer-card mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <a href="/profile/"><img src="/uploads/<?= $profile['profile_pic'] ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;" /></a>
                                <a href="/feeds/create.php" class="composer-input text-decoration-none">
                                    What's on your mind, <?= $profile['username'] ?>?
                                </a>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-around">
                                <a href="/live/"  class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center"><i class="ri-video-add-fill text-danger me-2"></i> Live</a>
                                <a href="/feeds/create.php" class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center" onclick="document.getElementById('image-open').click()"><i class="ri-image-2-fill text-success me-2"></i> Photo</a>
                                <a href="/shop/create.php" class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center"><i class="ri-shopping-bag-fill text-warning me-2"></i> Shopping</a>
                            </div>
                        </div>

                        <!-- MARKETPLACE SECTION (Swiper Carousel) -->
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
                                    <!-- Add more slides dynamically if needed -->
                                </div>

                                <!-- Navigation & Pagination -->
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-pagination mt-3"></div>
                            </div>
                        </div>

                        <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">People You May Know</h6>
        <a href="#" class="text-primary text-decoration-none small fw-bold">See All</a>
    </div>

    <div class="d-flex gap-3 pb-3 overflow-auto custom-scrollbar" style="scrollbar-width: none;">
        
        <div class="card border-0 shadow-sm flex-shrink-0 friend-suggestion-card" style="width: 150px; border-radius: 18px;">
            <div class="position-relative">
                <div class="suggestion-cover" style="height: 60px; background: url('https://picsum.photos/200/100?random=1') center/cover; border-radius: 18px 18px 0 0;"></div>
                <div class="position-absolute start-50 translate-middle" style="top: 60px;">
                    <img src="https://i.pravatar.cc/150?u=alex" class="rounded-circle border border-3 border-white shadow-sm" width="55" height="55">
                </div>
            </div>
            
            <div class="card-body text-center pt-5 pb-3">
                <div class="fw-bold text-dark text-truncate mb-0" style="font-size: 0.9rem;">Alex Rivers</div>
                <div class="text-muted mb-3" style="font-size: 0.75rem;">12 Mutual Friends</div>
                
                <div class="d-grid gap-2 px-2">
                    <button class="btn btn-primary btn-sm rounded-pill fw-bold btn-add-friend">
                        <i class="ri-user-add-line me-1"></i> Add
                    </button>
                    <button class="btn btn-light btn-sm rounded-pill text-muted fw-bold">Remove</button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm flex-shrink-0 friend-suggestion-card" style="width: 150px; border-radius: 18px;">
            <div class="position-relative">
                <div class="suggestion-cover" style="height: 60px; background: url('https://picsum.photos/200/100?random=2') center/cover; border-radius: 18px 18px 0 0;"></div>
                <div class="position-absolute start-50 translate-middle" style="top: 60px;">
                    <img src="https://i.pravatar.cc/150?u=sophia" class="rounded-circle border border-3 border-white shadow-sm" width="55" height="55">
                </div>
            </div>
            <div class="card-body text-center pt-5 pb-3">
                <div class="fw-bold text-dark text-truncate mb-0" style="font-size: 0.9rem;">Sophia Chen</div>
                <div class="text-muted mb-3" style="font-size: 0.75rem;">8 Mutual Friends</div>
                <div class="d-grid gap-2 px-2">
                    <button class="btn btn-primary btn-sm rounded-pill fw-bold btn-add-friend">
                        <i class="ri-user-add-line me-1"></i> Add
                    </button>
                    <button class="btn btn-light btn-sm rounded-pill text-muted fw-bold">Remove</button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm flex-shrink-0 friend-suggestion-card" style="width: 150px; border-radius: 18px;">
            <div class="position-relative">
                <div class="suggestion-cover" style="height: 60px; background: url('https://picsum.photos/200/100?random=3') center/cover; border-radius: 18px 18px 0 0;"></div>
                <div class="position-absolute start-50 translate-middle" style="top: 60px;">
                    <img src="https://i.pravatar.cc/150?u=marcus" class="rounded-circle border border-3 border-white shadow-sm" width="55" height="55">
                </div>
            </div>
            <div class="card-body text-center pt-5 pb-3">
                <div class="fw-bold text-dark text-truncate mb-0" style="font-size: 0.9rem;">Marcus Thorne</div>
                <div class="text-muted mb-3" style="font-size: 0.75rem;">24 Mutual Friends</div>
                <div class="d-grid gap-2 px-2">
                    <button class="btn btn-primary btn-sm rounded-pill fw-bold btn-add-friend">
                        <i class="ri-user-add-line me-1"></i> Add
                    </button>
                    <button class="btn btn-light btn-sm rounded-pill text-muted fw-bold">Remove</button>
                </div>
            </div>
        </div>

    </div>
</div>







<div class="container px-0 mt-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-primary fw-bold text-uppercase small">Marketplace</span>
            <h3 class="fw-bold mb-0 fs-5">Trending Products</h3>
        </div>
        <a href="#" class="btn btn-outline-dark btn-sm rounded-pill px-3">View Store</a>
    </div>

    <div class="d-flex gap-2 overflow-auto py-3 g-3 g-md-4 custom-scrollbar" style="scrollbar-width: none;">
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm product-card">
                <div class="position-relative overflow-hidden rounded-top">
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2 z-index-2">-20%</span>
                    <button class="btn btn-white btn-wishlist position-absolute top-0 end-0 m-2 shadow-sm rounded-circle">
                        <i class="ri-heart-line"></i>
                    </button>
                    <div class="ratio ratio-1x1 bg-light">
                        <img src="https://picsum.photos/400/400?random=10" class="object-fit-cover" alt="Product">
                    </div>
                    <div class="product-actions w-100 position-absolute bottom-0 p-2">
                        <button class="btn btn-dark w-100 btn-sm rounded-pill py-2 shadow">
                            <i class="ri-shopping-cart-2-line me-1"></i> Quick Add
                        </button>
                    </div>
                </div>
                <div class="card-body px-2 pt-3">
                    <div class="text-muted small mb-1">Electronics</div>
                    <h6 class="fw-bold text-dark text-truncate mb-2">Wireless Noise-Canceling Headphones</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-primary">1,200 MC</span>
                        <span class="text-muted text-decoration-line-through small">1,500 MC</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm product-card">
                <div class="position-relative overflow-hidden rounded-top">
                    <button class="btn btn-white btn-wishlist position-absolute top-0 end-0 m-2 shadow-sm rounded-circle">
                        <i class="ri-heart-line"></i>
                    </button>
                    <div class="ratio ratio-1x1 bg-light">
                        <img src="https://picsum.photos/400/400?random=20" class="object-fit-cover" alt="Product">
                    </div>
                    <div class="product-actions w-100 position-absolute bottom-0 p-2">
                        <button class="btn btn-dark w-100 btn-sm rounded-pill py-2 shadow">
                            <i class="ri-shopping-cart-2-line me-1"></i> Quick Add
                        </button>
                    </div>
                </div>
                <div class="card-body px-2 pt-3">
                    <div class="text-muted small mb-1">Lifestyle</div>
                    <h6 class="fw-bold text-dark text-truncate mb-2">Smart Sport Watch v2</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-primary">3,500 MC</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm product-card">
                <div class="position-relative overflow-hidden rounded-top">
                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2 z-index-2">Limited</span>
                    <button class="btn btn-white btn-wishlist position-absolute top-0 end-0 m-2 shadow-sm rounded-circle">
                        <i class="ri-heart-line"></i>
                    </button>
                    <div class="ratio ratio-1x1 bg-light">
                        <img src="https://picsum.photos/400/400?random=30" class="object-fit-cover" alt="Product">
                    </div>
                    <div class="product-actions w-100 position-absolute bottom-0 p-2">
                        <button class="btn btn-dark w-100 btn-sm rounded-pill py-2 shadow">
                            <i class="ri-shopping-cart-2-line me-1"></i> Quick Add
                        </button>
                    </div>
                </div>
                <div class="card-body px-2 pt-3">
                    <div class="text-muted small mb-1">Accessories</div>
                    <h6 class="fw-bold text-dark text-truncate mb-2">Minimalist Leather Wallet</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-primary">450 MC</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm product-card">
                <div class="position-relative overflow-hidden rounded-top">
                    <div class="ratio ratio-1x1 bg-light">
                        <img src="https://picsum.photos/400/400?random=40" class="object-fit-cover" alt="Product">
                    </div>
                    <div class="product-actions w-100 position-absolute bottom-0 p-2">
                        <button class="btn btn-dark w-100 btn-sm rounded-pill py-2 shadow">
                            <i class="ri-shopping-cart-2-line me-1"></i> Quick Add
                        </button>
                    </div>
                </div>
                <div class="card-body px-2 pt-3">
                    <div class="text-muted small mb-1">Home</div>
                    <h6 class="fw-bold text-dark text-truncate mb-2">Aromatic Essential Oil Diffuser</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-primary">800 MC</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<div id="sammyHeroSlider" class="carousel slide carousel-fade" data-bs-ride="carousel">
    
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#sammyHeroSlider" data-bs-slide-to="0" class="active rounded-circle" style="width:12px; height:12px;"></button>
        <button type="button" data-bs-target="#sammyHeroSlider" data-bs-slide-to="1" class="rounded-circle" style="width:12px; height:12px;"></button>
    </div>

    <div class="carousel-inner">
        
        <div class="carousel-item active" style="height: 85vh; background: #fdfcf0;">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-6 text-center text-lg-start">
                        <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2 rounded-pill fw-bold">v3.0 IS LIVE</span>
                        <h1 class="display-3 fw-black text-dark mb-4">Master Your <span class="text-primary">Flow</span></h1>
                        <p class="lead text-muted mb-5">Connect with friends, trade digital assets, and grow your monieCoins in the most advanced social ecosystem.</p>
                        <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                            <button class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg">Join Now</button>
                            <button class="btn btn-outline-dark btn-lg rounded-pill px-4"><i class="ri-play-fill"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="position-relative">
                            <img src="https://picsum.photos/600/600?random=1" class="img-fluid rounded-5 shadow-lg" alt="Slide 1">
                            <div class="position-absolute bottom-0 start-0 m-4 p-3 border border-white border-opacity-50 shadow-lg" 
                                 style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border-radius: 20px; width: 220px;">
                                <div class="small fw-bold text-dark">Recent Harvest</div>
                                <div class="h4 text-success mb-0">+1,240 MC</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="carousel-item" style="height: 85vh; background: #1d1d1d;">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-6 text-center text-lg-start text-white">
                        <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold">EARN PASSIVE INCOME</span>
                        <h1 class="display-3 fw-black mb-4 text-white">The <span class="text-warning">Barn</span> is Open</h1>
                        <p class="lead opacity-75 mb-5">Stake your coins in high-yield silos. High risk, high reward. Start growing your digital portfolio today.</p>
                        <button class="btn btn-warning btn-lg rounded-pill px-5 fw-bold text-dark">Start Staking</button>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="position-relative text-center">
                            <div class="bg-warning opacity-10 position-absolute start-50 top-50 translate-middle rounded-circle" style="width: 400px; height: 400px; filter: blur(80px);"></div>
                            <img src="https://picsum.photos/600/600?random=2" class="img-fluid rounded-5 border border-warning border-opacity-25" style="z-index: 2; position: relative;" alt="Slide 2">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#sammyHeroSlider" data-bs-slide="prev">
        <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#sammyHeroSlider" data-bs-slide="next">
        <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </span>
    </button>
</div>




<div class="container px-0 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Explore Groups</h3>
            <p class="text-muted small">Find communities that match your interests.</p>
        </div>
        <button class="btn btn-outline-primary btn-sm rounded-pill px-3">View All Groups</button>
    </div>

    <div class="d-flex gap-2 overflow-auto py-3 g-4  custom-scrollbar" style="scrollbar-width: none;">
        <div class="col-md-6 col-9 col-lg-6">
            <div class="card h-100 border-0 shadow-sm hover-lift" style="border-radius: 20px;">
                <div class="position-relative">
                    <div style="height: 100px; background: url('https://picsum.photos/400/200?random=50') center/cover; border-radius: 20px 20px 0 0;"></div>
                    <span class="badge bg-white text-primary position-absolute top-0 end-0 m-3 shadow-sm rounded-pill">Trending</span>
                </div>
                
                <div class="card-body pt-0">
                    <div class="bg-white p-1 rounded-3 shadow-sm d-inline-block position-relative" style="margin-top: -30px; margin-left: 15px;">
                        <img src="https://picsum.photos/60/60?random=5" class="rounded-3" width="60" height="60">
                    </div>
                    
                    <div class="mt-3">
                        <h5 class="fw-bold mb-1">Crypto Whales 🐳</h5>
                        <p class="text-muted small mb-3">Daily market analysis and monieCoin trading signals for the elite.</p>
                        
                        <div class="d-flex align-items-center justify-content-between border-top pt-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-group d-flex me-2">
                                    <img src="https://i.pravatar.cc/100?u=1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                    <img src="https://i.pravatar.cc/100?u=2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                    <img src="https://i.pravatar.cc/100?u=3" class="rounded-circle border border-2 border-white" width="28">
                                </div>
                                <small class="text-muted fw-bold">4.2k members</small>
                            </div>
                            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">Join</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6 ">
            <div class="card h-100 border-0 shadow-sm hover-lift" style="border-radius: 20px;">
                <div class="position-relative">
                    <div style="height: 100px; background: url('https://picsum.photos/400/200?random=51') center/cover; border-radius: 20px 20px 0 0;"></div>
                    <span class="badge bg-dark text-white position-absolute top-0 end-0 m-3 shadow-sm rounded-pill">Private</span>
                </div>
                <div class="card-body pt-0">
                    <div class="bg-white p-1 rounded-3 shadow-sm d-inline-block position-relative" style="margin-top: -30px; margin-left: 15px;">
                        <img src="https://picsum.photos/60/60?random=6" class="rounded-3" width="60" height="60">
                    </div>
                    <div class="mt-3">
                        <h5 class="fw-bold mb-1">Design Squad 🎨</h5>
                        <p class="text-muted small mb-3">Share your UI/UX designs and get feedback from pro monieFlow creators.</p>
                        <div class="d-flex align-items-center justify-content-between border-top pt-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-group d-flex me-2">
                                    <img src="https://i.pravatar.cc/100?u=4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                    <img src="https://i.pravatar.cc/100?u=5" class="rounded-circle border border-2 border-white" width="28">
                                </div>
                                <small class="text-muted fw-bold">1.8k members</small>
                            </div>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">Request</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-lift" style="border-radius: 20px;">
                <div class="position-relative">
                    <div style="height: 100px; background: url('https://picsum.photos/400/200?random=52') center/cover; border-radius: 20px 20px 0 0;"></div>
                </div>
                <div class="card-body pt-0">
                    <div class="bg-white p-1 rounded-3 shadow-sm d-inline-block position-relative" style="margin-top: -30px; margin-left: 15px;">
                        <img src="https://picsum.photos/60/60?random=7" class="rounded-3" width="60" height="60">
                    </div>
                    <div class="mt-3">
                        <h5 class="fw-bold mb-1">Gamers Hub 🎮</h5>
                        <p class="text-muted small mb-3">Find teammates for the monieFlow Arena and climb the leaderboards together.</p>
                        <div class="d-flex align-items-center justify-content-between border-top pt-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-group d-flex me-2">
                                    <img src="https://i.pravatar.cc/100?u=6" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                    <img src="https://i.pravatar.cc/100?u=7" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                    <img src="https://i.pravatar.cc/100?u=8" class="rounded-circle border border-2 border-white" width="28">
                                </div>
                                <small class="text-muted fw-bold">12k members</small>
                            </div>
                            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">Join</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



                        <div id="roots">
                            <div class="card p-3 mb-3 border-0 shadow-sm" id="preloader-loding" style="border-radius:8px">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="placeholder-wave"><span class="placeholder rounded-circle me-3" style="width:40px; height:40px;"></span></span>
                                    <div class="flex-grow-1"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></div>
                                </div>
                                <span class="placeholder-glow"><span class="placeholder col-12 mb-2" style="height:150px; border-radius:8px"></span></span>
                            </div>

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

                <div class="col-lg-3 sidebar-right sidebar-sticky">
                    <h6 class="text-secondary px-3 mb-3 d-flex justify-content-between">
                        <span>Contacts</span>
                        <div class="d-flex gap-3">
                            <i class="ri-video-add-fill"></i>
                            <i class="ri-search-line"></i>
                            <i class="ri-more-fill"></i>
                        </div>
                    </h6>
                    <a href="#" class="sidebar-link"><img src="https://i.pravatar.cc/150?u=9" class="rounded-circle me-3" width="32" height="32" style="object-fit: cover;"> Sarah Stone</a>
                    <a href="#" class="sidebar-link"><img src="https://i.pravatar.cc/150?u=12" class="rounded-circle me-3" width="32" height="32" style="object-fit: cover;"> Mike Miller</a>
                    <a href="#" class="sidebar-link"><img src="https://i.pravatar.cc/150?u=5" class="rounded-circle me-3" width="32" height="32" style="object-fit: cover;"> Alex Rivera</a>
                </div>
            </div>
        </div>
    </div>

    <script src="/api.js"></script>
    <script src="/feeds/script.js"></script>
    <script>
        initSammyPicker('emojiBtn', 'postContent');
    </script>
    <script>
        // Initialize Marketplace Swiper
        const marketplaceSwiper = new Swiper('.marketplace-swiper', {
            slidesPerView: 2.0,
            spaceBetween: 5,
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
                480:  { slidesPerView: 2.5, spaceBetween: 16 },
                768:  { slidesPerView: 2.5, spaceBetween: 20 },
                992:  { slidesPerView: 3.2, spaceBetween: 20 },
                1200: { slidesPerView: 3, spaceBetween: 24 }
            }
        });
    </script>
</body>
</html>