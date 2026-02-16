<!DOCTYPE html>
<html lang="en">
<head>
    <?php // include __DIR__."/../main-function.php"; ?>
    <?php include __DIR__."/req.php"; ?>
    <?php 
        $main = new feeds($conn);
        if($main->isLoggedIn() === false) header('location:/');
        $userData = $main->getUserData()['data'];
        $profile = json_decode($userData['profile'], true);
        $reels=$main->feeds(0,'reel');
        // print_r($main->feeds(0,'reel'))
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=0.9, user-scalable=1">
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

        .masonry {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  grid-auto-flow: dense;
  gap: 10px;
}

.masonry-item {
  width: 100%;
  max-height: 200px;
  object-fit: cover;   /* ensures preview style */
}

/* Special case: only one image */
.masonry-item.single {
  grid-column: 1 / -1; /* span full width */
  max-height: none;    /* remove height restriction */
}


/* Remove default Swiper arrow icons */
.swiper-button-next::after,
.swiper-button-prev::after {
  content: none !important; /* clears the built-in arrow */
}

/* Keep your custom button styles */
.swiper-button-prev,
.swiper-button-next {
  width: 30px;
  height: 30px;
  top: 50%;
  transform: translateY(-50%);
  background: #fbf9f97a;   /* optional background */
  border-radius: 50%;      /* optional rounded look */
  color: #212020;
  z-index: 10;
}

/* Positioning */
.swiper-button-prev {
  left: 10px;
}

.swiper-button-next {
  right: 10px;
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
                            <div id="reelifield" class="d-flex gap-3 pb-3 overflow-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
                                <a href="/feeds/reel/create.php"  class="card text-decoration-none border-0 shadow-sm flex-shrink-0" style="width: 130px; border-radius: 15px; overflow: hidden;">
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
                                <?php foreach ($reels as $key => $reel):
                                    $profile=json_decode($reel['profile'],true);
                                    $data=json_decode($reel['data'],true);
                                 ?>
                                    <a href="/feeds/reel/?r=<?= $reel['feed_id'] ?>" class="position-relative flex-shrink-0 shadow-sm user-reel" style="width: 130px; height: 210px; border-radius: 15px; overflow: hidden;">
                                        <div class="h-100 w-100" style="background: url('/uploads/reel_covers/<?= $data['cover'] ?>') center/cover;"></div>
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-2" 
                                            style="background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.8) 100%);">
                                            <div class="avatar-ring">
                                                <img src="/uploads/<?= $profile['profile_pic'] ?>" class="rounded-circle border border-2 border-primary" width="35" height="35">
                                            </div>
                                            <div class="blur-footer rounded-2 py-1 px-2">
                                                <div class="text-white small fw-semibold text-truncate" style="font-size: 0.75rem;"><?= $profile['username'] ?></div>
                                            </div>
                                         </div>
                                    </a>
                                <?php endforeach; ?>
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
                                <a href="/live/"  class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center"><i class="ri-video-add-fill text-danger me-2"></i> Reels</a>
                                <a href="/feeds/create.php" class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center" onclick="document.getElementById('image-open').click()"><i class="ri-image-2-fill text-success me-2"></i> Photo</a>
                                <a href="/feeds/music/create.php" class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center"><i class="ri-music-fill text-warning me-2"></i> Music</a>
                            </div>
                        </div>
                        <div id="rooterfild"></div>
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