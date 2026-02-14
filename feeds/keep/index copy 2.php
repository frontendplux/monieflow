<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=0.9">
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
    <div class="main-wrapper">
        
    

<section class="py-5 bg-white overflow-hidden">
    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-800 mb-1 text-dark tracking-tighter">Discover Pages</h3>
                <p class="text-muted small mb-0">Communities you might like</p>
            </div>
            <a href="#" class="btn btn-light rounded-pill px-4 fw-600 text-dark border-0">
                See All <i class="bi bi-chevron-right ms-1"></i>
            </a>
        </div>

        <div class="d-flex flex-nowrap overflow-auto pb-5 pt-2 custom-snap-slider" 
             style="scroll-snap-type: x mandatory; gap: 20px; -webkit-overflow-scrolling: touch;">
            
            <div class="col-9 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="social-card border-0 rounded-5 bg-white transition-all shadow-hover">
                    <div class="position-relative p-2">
                        <img src="https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=600" 
                             class="rounded-5 w-100 object-fit-cover" style="height: 320px;" alt="Coding">
                        <span class="position-absolute top-4 start-4 badge rounded-pill bg-white text-dark shadow-sm px-3 py-2 ms-3 mt-3">
                            #Technology
                        </span>
                    </div>
                    <div class="card-body px-3 py-3 text-center">
                        <h5 class="fw-700 mb-1">Code Masters</h5>
                        <p class="text-muted small mb-3">42k Members</p>
                        <button class="btn btn-dark w-100 rounded-pill py-2 fw-600">Explore Page</button>
                    </div>
                </div>
            </div>

            <div class="col-9 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="social-card border-0 rounded-5 bg-white transition-all shadow-hover">
                    <div class="position-relative p-2">
                        <img src="https://images.unsplash.com/photo-1502134249126-9f3755a50d78?auto=format&fit=crop&w=600" 
                             class="rounded-5 w-100 object-fit-cover" style="height: 320px;" alt="Space">
                        <span class="position-absolute top-4 start-4 badge rounded-pill bg-white text-dark shadow-sm px-3 py-2 ms-3 mt-3">
                            #Science
                        </span>
                    </div>
                    <div class="card-body px-3 py-3 text-center">
                        <h5 class="fw-700 mb-1">Cosmos Daily</h5>
                        <p class="text-muted small mb-3">108k Members</p>
                        <button class="btn btn-dark w-100 rounded-pill py-2 fw-600">Explore Page</button>
                    </div>
                </div>
            </div>

            <div class="col-9 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="social-card border-0 rounded-5 bg-white transition-all shadow-hover">
                    <div class="position-relative p-2">
                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=600" 
                             class="rounded-5 w-100 object-fit-cover" style="height: 320px;" alt="Design">
                        <span class="position-absolute top-4 start-4 badge rounded-pill bg-white text-dark shadow-sm px-3 py-2 ms-3 mt-3">
                            #Design
                        </span>
                    </div>
                    <div class="card-body px-3 py-3 text-center">
                        <h5 class="fw-700 mb-1">Minimalist Hub</h5>
                        <p class="text-muted small mb-3">15k Members</p>
                        <button class="btn btn-dark w-100 rounded-pill py-2 fw-600">Explore Page</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Typography & Clean UI */
.fw-700 { font-weight: 700; }
.fw-800 { font-weight: 800; }
.fw-600 { font-weight: 600; }
.tracking-tighter { letter-spacing: -1px; }

/* The Modern Social Card Look */
.social-card {
    border: 1px solid #f0f0f0 !important;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.shadow-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
    border-color: transparent !important;
}

/* Hide scrollbar logic */
.custom-snap-slider::-webkit-scrollbar { display: none; }
.custom-snap-slider { -ms-overflow-style: none; scrollbar-width: none; }

/* Button Hover State */
.btn-dark:hover {
    background-color: #333;
    transform: scale(0.98);
}
</style>

<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">Suggested for you</h4>
            <a href="#" class="btn btn-link text-decoration-none fw-bold p-0">See More</a>
        </div>

        <div class="d-flex flex-nowrap overflow-auto gap-3 pb-3 custom-scroll" style="scroll-snap-type: x mandatory;">
            
            <div class="col-5 col-md-3 col-lg-2 flex-shrink-0 text-center" style="scroll-snap-align: start;">
                <div class="p-3 border rounded-5 bg-light transition-hover shadow-sm">
                    <div class="position-relative mb-3">
                        <img src="https://i.pravatar.cc/150?u=1" class="rounded-circle img-fluid border border-3 border-white shadow-sm" alt="User">
                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-1" title="Online"></span>
                    </div>
                    <h6 class="fw-bold mb-0 text-truncate">Art_Studio</h6>
                    <p class="text-muted x-small mb-3">12k followers</p>
                    <button class="btn btn-primary btn-sm rounded-pill w-100 fw-bold">Follow</button>
                </div>
            </div>

            <div class="col-5 col-md-3 col-lg-2 flex-shrink-0 text-center" style="scroll-snap-align: start;">
                <div class="p-3 border rounded-5 bg-light transition-hover shadow-sm">
                    <div class="position-relative mb-3">
                        <img src="https://i.pravatar.cc/150?u=2" class="rounded-circle img-fluid border border-2 border-white" alt="User">
                    </div>
                    <h6 class="fw-bold mb-0 text-truncate">TechTalk</h6>
                    <p class="text-muted x-small mb-3">8k followers</p>
                    <button class="btn btn-outline-dark btn-sm rounded-pill w-100 fw-bold">Follow</button>
                </div>
            </div>

            </div>
    </div>
</section>


<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-800 text-uppercase small tracking-widest text-secondary">Trending Now</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-dark btn-sm rounded-circle shadow-sm" onclick="document.getElementById('socialSlider').scrollBy({left: -300, behavior: 'smooth'})"><i class="bi bi-chevron-left"></i></button>
                <button class="btn btn-outline-dark btn-sm rounded-circle shadow-sm" onclick="document.getElementById('socialSlider').scrollBy({left: 300, behavior: 'smooth'})"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>

        <div id="socialSlider" class="d-flex flex-nowrap overflow-auto pb-4 gap-4 custom-scroll" style="scroll-snap-type: x mandatory;">
            
            <div class="col-11 col-md-8 col-lg-5 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card border-0 rounded-5 overflow-hidden shadow-sm bg-light position-relative">
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800" class="card-img h-100" style="min-height: 350px; object-fit: cover;" alt="">
                    <div class="card-img-overlay d-flex flex-column justify-content-end p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://i.pravatar.cc/100?u=a1" class="rounded-circle border border-2 border-white" width="50" height="50">
                            <div>
                                <h4 class="text-white fw-bold mb-0">Abstract Collective</h4>
                                <p class="text-white-50 small mb-0">95k active members</p>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="stretched-link"></a>
                </div>
            </div>

            <div class="col-8 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card border-0 bg-white shadow-sm rounded-5 h-100 p-2">
                    <img src="https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&w=600" class="rounded-5 w-100 mb-3" style="height: 180px; object-fit: cover;">
                    <div class="px-2 pb-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill mb-2">Design</span>
                        <h6 class="fw-700">UI/UX Inspirations</h6>
                        <p class="text-muted small">Daily doses of digital art.</p>
                        <button class="btn btn-light w-100 rounded-pill fw-600 border">Join</button>
                    </div>
                </div>
            </div>

            <div class="col-8 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card border-0 bg-white shadow-sm rounded-5 h-100 p-2">
                    <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=600" class="rounded-5 w-100 mb-3" style="height: 180px; object-fit: cover;">
                    <div class="px-2 pb-2">
                        <span class="badge bg-success-subtle text-success rounded-pill mb-2">Dev</span>
                        <h6 class="fw-700">Fullstack Hub</h6>
                        <p class="text-muted small">Learn by building real apps.</p>
                        <button class="btn btn-dark w-100 rounded-pill fw-600">Join</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>



<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 px-2">
            <h2 class="fw-900 display-6 m-0 tracking-tighter">Recommended</h2>
            <button class="btn btn-dark rounded-circle p-2" style="width: 45px; height: 45px;">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>

        <div class="d-flex flex-nowrap overflow-auto pb-4 custom-scroll-modern" style="scroll-snap-type: x mandatory; gap: 15px;">
            
            <div class="col-10 col-md-6 col-lg-5 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card h-100 border-0 rounded-5 bg-light overflow-hidden shadow-sm position-relative">
                    <img src="https://images.unsplash.com/photo-1516251193007-45ef944ab0c6?auto=format&fit=crop&w=800" class="w-100 h-100 object-fit-cover" style="min-height: 380px;" alt="">
                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, rgba(255,255,255,0.9) 70%);">
                        <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill fw-bold">PRO PAGE</span>
                        <h3 class="fw-800 text-dark">Urban Photography</h3>
                        <p class="text-muted small mb-3">Learn to capture the city lights like a professional.</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex -space-x-2">
                                <img src="https://i.pravatar.cc/100?u=1" class="rounded-circle border border-2 border-white" width="35">
                                <img src="https://i.pravatar.cc/100?u=2" class="rounded-circle border border-2 border-white" width="35" style="margin-left: -12px;">
                                <img src="https://i.pravatar.cc/100?u=3" class="rounded-circle border border-2 border-white" width="35" style="margin-left: -12px;">
                            </div>
                            <button class="btn btn-white border-dark rounded-pill px-4 fw-bold">Visit</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-7 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card h-100 border-0 rounded-5 bg-white shadow-sm p-3">
                    <div class="ratio ratio-1x1 mb-3">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=500" class="rounded-circle object-fit-cover p-1 border border-2 border-primary" alt="">
                    </div>
                    <div class="text-center">
                        <h5 class="fw-800 mb-1">Sarah Chen</h5>
                        <p class="text-muted x-small mb-3">Product Designer</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary rounded-pill fw-bold py-2">Follow</button>
                            <button class="btn btn-light rounded-pill fw-bold py-2">Message</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-7 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card h-100 border-0 rounded-5 bg-dark text-white p-4 d-flex flex-column justify-content-between">
                    <div>
                        <i class="bi bi-lightning-charge-fill display-5 text-warning"></i>
                        <h4 class="fw-800 mt-3">Active Communities</h4>
                    </div>
                    <p class="small opacity-75">Join over 2,000 users discussing the latest in AI and Tech.</p>
                    <a href="#" class="text-white fw-bold text-decoration-none border-bottom border-2 pb-1 w-fit">Explore All →</a>
                </div>
            </div>

        </div>
    </div>
</section>



<section class="py-5 bg-white overflow-hidden">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-primary fw-bold text-uppercase small tracking-widest">Fresh Hits</h6>
                <h2 class="fw-900 display-5 m-0 tracking-tighter">New Releases</h2>
            </div>
            <a href="#" class="btn btn-outline-dark rounded-pill fw-bold px-4">See More</a>
        </div>

        <div class="d-flex flex-nowrap overflow-auto gap-4 pb-4 custom-music-scroll" style="scroll-snap-type: x mandatory;">
            
            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="music-card position-relative rounded-5 overflow-hidden shadow-hover bg-light">
                    <img src="https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?auto=format&fit=crop&w=600" 
                         class="w-100 object-fit-cover transition-zoom" style="height: 400px;" alt="Album">
                    
                    <button class="btn btn-primary rounded-circle position-absolute top-50 start-50 translate-middle play-btn opacity-0">
                        <i class="bi bi-play-fill display-4"></i>
                    </button>

                    <div class="position-absolute bottom-0 w-100 p-3">
                        <div class="glass-music p-3 rounded-4 border border-white border-opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-800 mb-0 text-white">Midnight City</h5>
                                    <p class="small text-white-50 mb-0">The Synth Project</p>
                                </div>
                                <span class="badge bg-white text-dark rounded-pill">4:12</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="music-card position-relative rounded-5 overflow-hidden shadow-hover bg-light">
                    <img src="https://images.unsplash.com/photo-1493225255756-d9584f8606e9?auto=format&fit=crop&w=600" 
                         class="w-100 object-fit-cover transition-zoom" style="height: 400px;" alt="Artist">
                    <div class="position-absolute bottom-0 w-100 p-3">
                        <div class="glass-music p-3 rounded-4 border border-white border-opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-800 mb-0 text-white">Golden Hour</h5>
                                    <p class="small text-white-50 mb-0">Summer Soul</p>
                                </div>
                                <div class="wave-animation d-flex gap-1">
                                    <span class="bg-white"></span><span class="bg-white"></span><span class="bg-white"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </div>
    </div>
</section>


<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-900 tracking-tighter m-0">Trending Reels</h2>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger pulse-dot p-1 border border-light rounded-circle"></span>
                <span class="small fw-bold text-uppercase opacity-50">Live Now</span>
            </div>
        </div>

        <div class="d-flex flex-nowrap overflow-auto gap-3 pb-5 custom-reels-scroll" style="scroll-snap-type: x mandatory;">
            
            <div class="col-8 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="reel-card position-relative rounded-5 overflow-hidden shadow-lg shadow-hover bg-dark">
                    <img src="https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&w=600&q=80" 
                         class="w-100 h-100 object-fit-cover opacity-75 transition-zoom" style="min-height: 550px;" alt="Reel">
                    
                    <div class="position-absolute top-0 start-0 w-100 p-3 d-flex align-items-center gap-2">
                        <img src="https://i.pravatar.cc/100?u=music1" class="rounded-circle border border-2 border-white shadow" width="40" height="40">
                        <span class="text-white fw-bold small shadow-sm">@Lofi_Beats</span>
                    </div>

                    <div class="position-absolute end-0 top-50 translate-middle-y d-flex flex-column gap-3 pe-3 text-white align-items-center">
                        <div class="text-center">
                            <i class="bi bi-heart-fill fs-4 text-danger drop-shadow"></i>
                            <div class="x-small fw-bold">12k</div>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-chat-dots-fill fs-4 drop-shadow"></i>
                            <div class="x-small fw-bold">420</div>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-share-fill fs-4 drop-shadow"></i>
                        </div>
                    </div>

                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                        <h5 class="text-white fw-bold mb-1">Night Drive</h5>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-music-note-beamed text-white opacity-75"></i>
                            <marquee class="text-white-50 small w-75" scrollamount="3">Original Audio - @Lofi_Beats feat. Jazzcat</marquee>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-8 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="reel-card position-relative rounded-5 overflow-hidden shadow-lg shadow-hover bg-dark">
                    <img src="https://images.unsplash.com/photo-1514525253361-bee8a487409e?auto=format&fit=crop&w=600&q=80" 
                         class="w-100 h-100 object-fit-cover opacity-75 transition-zoom" style="min-height: 550px;" alt="Reel">
                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                        <h5 class="text-white fw-bold mb-1">Festival SZN</h5>
                        <p class="text-white-50 small mb-0">Live from London</p>
                    </div>
                </div>
            </div>

            <div class="col-8 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="reel-card rounded-5 border-2 border-dashed d-flex flex-column align-items-center justify-content-center text-center p-4 bg-light shadow-hover" style="min-height: 550px; border: 3px dashed #dee2e6 !important;">
                    <div class="bg-white rounded-circle shadow-sm p-4 mb-3">
                        <i class="bi bi-plus-lg display-4 text-primary"></i>
                    </div>
                    <h4 class="fw-900">Discover More</h4>
                    <p class="text-muted small">Explore 2,000+ new reels from your community</p>
                    <button class="btn btn-dark rounded-pill px-5 py-2 mt-3 fw-bold">Explore All</button>
                </div>
            </div>

        </div>
    </div>
    <style>
        /* Typography */
.fw-900 { font-weight: 900; }
.tracking-tighter { letter-spacing: -2px; }
.x-small { font-size: 0.7rem; }

/* The Reel Card Container */
.reel-card {
    height: 550px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.shadow-hover:hover {
    transform: scale(1.02);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
}

/* Social Icon Drop Shadow */
.drop-shadow {
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
}

/* Hide Scrollbar */
.custom-reels-scroll::-webkit-scrollbar { display: none; }
.custom-reels-scroll { scrollbar-width: none; }

/* Live Pulse Animation */
.pulse-dot {
    width: 8px;
    height: 8px;
    animation: pulse-red 2s infinite;
}

@keyframes pulse-red {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

/* Marquee Width Fix */
marquee {
    display: inline-block;
    vertical-align: middle;
}
    </style>
</section>



<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-900 tracking-tighter mb-0">Video Center</h2>
                <p class="text-muted small">Trending clips from your community</p>
            </div>
            <a href="#" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">Upload Video</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 rounded-5 overflow-hidden shadow-sm bg-dark position-relative video-hero">
                    <video autoplay muted loop class="w-100 h-100 object-fit-cover opacity-75">
                        <source src="https://assets.mixkit.co/videos/preview/mixkit-girl-dancing-in-front-of-a-colorful-wall-34440-large.mp4" type="video/mp4">
                    </video>
                    <div class="position-absolute bottom-0 start-0 w-100 p-4 pb-5" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                        <span class="badge bg-danger rounded-pill mb-2 px-3">FEATURED</span>
                        <h3 class="text-white fw-800">New Wave Festival 2026</h3>
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://i.pravatar.cc/100?u=99" class="rounded-circle border border-2 border-white" width="40">
                            <span class="text-white fw-bold">Studio_X</span>
                        </div>
                    </div>
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <button class="btn btn-white rounded-circle p-4 play-pulse shadow-lg">
                            <i class="bi bi-play-fill fs-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="d-flex flex-column gap-3">
                    
                    <div class="d-flex gap-3 p-2 rounded-4 hover-bg transition-all border cursor-pointer">
                        <div class="position-relative flex-shrink-0" style="width: 120px;">
                            <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=300" class="rounded-3 w-100 h-100 object-fit-cover" alt="">
                            <span class="position-absolute bottom-0 end-0 bg-dark text-white x-small px-2 m-1 rounded">2:45</span>
                        </div>
                        <div class="overflow-hidden">
                            <h6 class="fw-bold text-truncate mb-1">Electronic Mix 2026</h6>
                            <p class="text-muted small mb-0 text-truncate">Live from the rooftop...</p>
                            <span class="text-primary x-small fw-bold">1.2M Views</span>
                        </div>
                    </div>

                    <div class="d-flex gap-3 p-2 rounded-4 hover-bg transition-all border cursor-pointer">
                        <div class="position-relative flex-shrink-0" style="width: 120px;">
                            <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=300" class="rounded-3 w-100 h-100 object-fit-cover" alt="">
                            <span class="position-absolute bottom-0 end-0 bg-dark text-white x-small px-2 m-1 rounded">0:59</span>
                        </div>
                        <div>
                            <h6 class="fw-bold text-truncate mb-1">Beat Making Tutorial</h6>
                            <p class="text-muted small mb-0 text-truncate">How to use synths...</p>
                            <span class="text-primary x-small fw-bold">450k Views</span>
                        </div>
                    </div>

                    <button class="btn btn-light rounded-pill w-100 py-3 fw-bold mt-2">View Full Gallery</button>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-white overflow-hidden">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <span class="badge bg-primary-gradient px-3 py-2 rounded-pill mb-2 shadow-sm">PLAY & WIN</span>
                <h2 class="fw-900 tracking-tighter display-5 m-0">The Music Lab</h2>
            </div>
            <div class="text-end d-none d-md-block">
                <p class="mb-0 fw-bold text-primary">Your Score: <span class="text-dark">1,250 XP</span></p>
                <div class="progress mt-1" style="height: 6px; width: 150px;">
                    <div class="progress-bar bg-primary" style="width: 70%"></div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-nowrap overflow-auto gap-4 pb-5 custom-game-scroll" style="scroll-snap-type: x mandatory;">
            
            <div class="col-11 col-md-6 col-lg-4 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="game-card position-relative rounded-5 overflow-hidden shadow-hover border-0">
                    <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600" class="w-100 h-100 object-fit-cover transition-zoom" style="min-height: 420px;" alt="">
                    
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-neon-cyan opacity-25"></div>
                    
                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, #000);">
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <h4 class="text-white fw-800 mb-1">Rhythm Clash</h4>
                                <p class="text-white-50 small mb-2">Multiplayer • 2-4 Players</p>
                                <div class="d-flex gap-2 mb-3">
                                    <span class="badge rounded-pill bg-dark border border-secondary">+500 XP</span>
                                    <span class="badge rounded-pill bg-dark border border-secondary">LIVE</span>
                                </div>
                            </div>
                            <button class="btn btn-primary rounded-circle p-3 mb-2 shadow-lg">
                                <i class="bi bi-controller fs-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-11 col-md-6 col-lg-4 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="game-card position-relative rounded-5 overflow-hidden shadow-hover border-0">
                    <img src="https://images.unsplash.com/photo-1493225255756-d9584f8606e9?auto=format&fit=crop&w=600" class="w-100 h-100 object-fit-cover transition-zoom" style="min-height: 420px;" alt="">
                    
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-neon-purple opacity-25"></div>

                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, #000);">
                        <h4 class="text-white fw-800 mb-1">Artist Quest</h4>
                        <p class="text-white-50 small mb-2">Global Leaderboard</p>
                        <button class="btn btn-outline-light w-100 rounded-pill fw-bold py-2 mt-2">Enter Lobby</button>
                    </div>
                </div>
            </div>

            <div class="col-9 col-md-4 col-lg-3 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card h-100 border-0 rounded-5 bg-light p-4 shadow-sm text-center d-flex flex-column justify-content-center">
                    <div class="bg-white rounded-circle mx-auto p-4 mb-3 shadow-sm" style="width: 100px; height: 100px;">
                        <i class="bi bi-trophy-fill display-5 text-warning"></i>
                    </div>
                    <h4 class="fw-800">Leaderboard</h4>
                    <ul class="list-unstyled mt-3 mb-4 text-start">
                        <li class="d-flex justify-content-between border-bottom py-2 small">
                            <span>1. @DJ_Nova</span> <span>9.8k</span>
                        </li>
                        <li class="d-flex justify-content-between border-bottom py-2 small">
                            <span>2. @BassKing</span> <span>8.2k</span>
                        </li>
                    </ul>
                    <a href="#" class="btn btn-dark rounded-pill fw-bold">See All Games</a>
                </div>
            </div>

        </div>
    </div>
    <style>
        /* Custom Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2px; }

/* Neon Accents */
.bg-neon-cyan { background-color: #00f2ff; }
.bg-neon-purple { background-color: #bc13fe; }
.bg-primary-gradient { 
    background: linear-gradient(45deg, #0d6efd, #6610f2);
    color: white;
}

/* The Game Card Effect */
.game-card {
    height: 420px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.shadow-hover:hover {
    transform: translateY(-10px) rotate(1deg);
    box-shadow: 0 30px 60px rgba(102, 16, 242, 0.2) !important;
}

.transition-zoom { transition: transform 0.6s ease; }
.game-card:hover .transition-zoom { transform: scale(1.1); }

/* Custom Scroll */
.custom-game-scroll::-webkit-scrollbar { display: none; }
.custom-game-scroll { scrollbar-width: none; }

/* Hide Cards on Mobile slightly to show more is coming */
@media (max-width: 576px) {
    .col-11 { width: 90%; }
}
    </style>
</section>



<section class="py-5 bg-white overflow-hidden">
    <div class="container">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
            <div>
                <h6 class="text-primary fw-bold text-uppercase tracking-widest mb-1">Interactive Hub</h6>
                <h2 class="display-5 fw-900 tracking-tighter m-0">Game Center</h2>
            </div>
            <div class="mt-3 mt-md-0 d-flex align-items-center gap-4">
                <div class="text-end">
                    <span class="d-block x-small fw-bold text-muted text-uppercase">Global Rank</span>
                    <span class="fw-900 h4 m-0">#420</span>
                </div>
                <button class="btn btn-dark rounded-pill px-4 py-2 fw-bold shadow-sm">My Inventory</button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 rounded-5 overflow-hidden shadow-lg position-relative game-hero transition-up">
                    <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200" 
                         class="w-100 h-100 object-fit-cover transition-zoom" alt="Featured Game">
                    
                    <div class="position-absolute bottom-0 start-0 w-100 p-4 p-md-5" 
                         style="background: linear-gradient(transparent, rgba(0,0,0,0.9) 80%);">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-2 mb-2 text-warning">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                                    <span class="text-white small ms-2">(2.4k playing)</span>
                                </div>
                                <h3 class="text-white fw-900 display-6">Bass Drop: Revolution</h3>
                                <p class="text-white-50">Match the beat and compete for the weekly prize pool.</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-glow">PLAY NOW</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 rounded-5 bg-light h-100 p-4 shadow-sm border">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-800 m-0">Top Players</h5>
                        <i class="bi bi-trophy text-warning fs-4"></i>
                    </div>
                    
                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded-pill shadow-sm border">
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://i.pravatar.cc/100?u=g1" class="rounded-circle" width="35">
                                <span class="fw-bold small">NeonRider</span>
                            </div>
                            <span class="badge bg-dark rounded-pill">12.5k</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2">
                            <div class="d-flex align-items-center gap-2 ps-1">
                                <img src="https://i.pravatar.cc/100?u=g2" class="rounded-circle" width="35">
                                <span class="fw-bold small opacity-75">VibeCheck</span>
                            </div>
                            <span class="text-muted small fw-bold">11.2k</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2">
                            <div class="d-flex align-items-center gap-2 ps-1">
                                <img src="https://i.pravatar.cc/100?u=g3" class="rounded-circle" width="35">
                                <span class="fw-bold small opacity-75">BeatMaster</span>
                            </div>
                            <span class="text-muted small fw-bold">9.8k</span>
                        </div>
                    </div>

                    <h6 class="fw-800 small text-muted text-uppercase mb-3 tracking-widest">More Games</h6>
                    <div class="d-flex gap-2 overflow-auto pb-2 custom-mini-scroll">
                        <div class="bg-white rounded-4 p-2 border flex-shrink-0" style="width: 100px;">
                            <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=200" class="rounded-3 w-100 mb-2">
                            <div class="x-small fw-bold text-center">Trivia</div>
                        </div>
                        <div class="bg-white rounded-4 p-2 border flex-shrink-0" style="width: 100px;">
                            <img src="https://images.unsplash.com/photo-1493225255756-d9584f8606e9?auto=format&fit=crop&w=200" class="rounded-3 w-100 mb-2">
                            <div class="x-small fw-bold text-center">DJ Mix</div>
                        </div>
                        <div class="bg-white rounded-4 p-2 border flex-shrink-0 d-flex flex-column align-items-center justify-content-center" style="width: 100px; border: 2px dashed #ccc !important;">
                            <i class="bi bi-plus-lg text-muted"></i>
                            <div class="x-small fw-bold text-muted">See All</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Modern Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2.5px; }
.x-small { font-size: 0.65rem; }

/* Effects */
.game-hero { height: 480px; }
.transition-up { transition: all 0.4s ease; }
.transition-up:hover { transform: translateY(-10px); }
.transition-zoom { transition: transform 0.8s ease; }
.game-hero:hover .transition-zoom { transform: scale(1.05); }

/* Play Button Glow */
.shadow-glow {
    box-shadow: 0 0 20px rgba(13, 110, 253, 0.5);
    transition: 0.3s;
}
.shadow-glow:hover {
    box-shadow: 0 0 35px rgba(13, 110, 253, 0.8);
    transform: scale(1.05);
}

/* Scroll Hide */
.custom-mini-scroll::-webkit-scrollbar { display: none; }
.custom-mini-scroll { scrollbar-width: none; }

@media (max-width: 991px) {
    .game-hero { height: 350px; }
}
</style>


<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 px-2">
            <div>
                <h2 class="fw-900 tracking-tighter mb-0">Popular Games</h2>
                <p class="text-muted small">Challenge your friends and climb the ranks</p>
            </div>
            <div class="d-none d-md-flex gap-2">
                <button class="btn btn-light rounded-circle border shadow-sm" onclick="document.getElementById('gameSlider').scrollBy({left: -400, behavior: 'smooth'})">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button class="btn btn-light rounded-circle border shadow-sm" onclick="document.getElementById('gameSlider').scrollBy({left: 400, behavior: 'smooth'})">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        <div id="gameSlider" class="d-flex flex-nowrap overflow-auto gap-4 pb-5 custom-game-track" style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;">
            
            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="game-list-card bg-white rounded-5 border shadow-sm overflow-hidden transition-all">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=600" class="w-100 object-fit-cover" style="height: 220px;" alt="">
                        <span class="position-absolute top-0 end-0 m-3 badge bg-blur-dark rounded-pill">EASY</span>
                    </div>
                    <div class="p-4">
                        <h5 class="fw-800 mb-1">Piano Tiles: Remix</h5>
                        <p class="text-muted x-small mb-3">Sync with the latest lofi hits.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="fw-bold text-primary small">120 XP</span>
                            <button class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">Play</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="game-list-card bg-white rounded-5 border shadow-sm overflow-hidden transition-all">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=600" class="w-100 object-fit-cover" style="height: 220px;" alt="">
                        <span class="position-absolute top-0 end-0 m-3 badge bg-danger rounded-pill">HARD</span>
                    </div>
                    <div class="p-4">
                        <h5 class="fw-800 mb-1">Dubstep Dash</h5>
                        <p class="text-muted x-small mb-3">High-speed rhythm obstacles.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="fw-bold text-primary small">450 XP</span>
                            <button class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">Play</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="game-list-card bg-white rounded-5 border shadow-sm overflow-hidden transition-all">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=600" class="w-100 object-fit-cover" style="height: 220px;" alt="">
                        <span class="position-absolute top-0 end-0 m-3 badge bg-blur-dark rounded-pill">MEDIUM</span>
                    </div>
                    <div class="p-4">
                        <h5 class="fw-800 mb-1">Vinyl Spin Quiz</h5>
                        <p class="text-muted x-small mb-3">Identify the 80s classic.</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="fw-bold text-primary small">200 XP</span>
                            <button class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">Play</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-8 col-md-4 col-lg-2 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="h-100 rounded-5 bg-light d-flex flex-column align-items-center justify-content-center text-center p-4 border border-dashed transition-all hover-see-more">
                    <div class="bg-white rounded-circle shadow-sm p-3 mb-3">
                        <i class="bi bi-grid-fill text-primary fs-3"></i>
                    </div>
                    <h6 class="fw-800 mb-1">All Games</h6>
                    <p class="x-small text-muted mb-3">View the full collection</p>
                    <a href="#" class="btn btn-outline-dark btn-sm rounded-pill px-3">Enter Arcade</a>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Modern Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2px; }
.x-small { font-size: 0.75rem; }

/* The Modern Card Look */
.game-list-card {
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.game-list-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
    border-color: #0d6efd !important;
}

/* Glass Badge */
.bg-blur-dark {
    background: rgba(0, 0, 0, 0.5) !important;
    backdrop-filter: blur(8px);
    color: white;
}

/* See More Interaction */
.hover-see-more:hover {
    background-color: #eef2ff !important;
    border-color: #0d6efd !important;
}

/* Scroll Hide */
.custom-game-track::-webkit-scrollbar { display: none; }
.custom-game-track { scrollbar-width: none; }

/* Dashboard Border style */
.border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>


<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill mb-3 fw-bold">OUR NETWORK</span>
                <h2 class="fw-900 display-4 tracking-tighter mb-4">The Agencies behind the Music.</h2>
            </div>
            <div class="col-lg-5 offset-lg-1">
                <p class="text-muted lead">We partner with the world's leading talent agencies to ensure our creators have the best management, legal, and production support in the industry.</p>
            </div>
        </div>

        <div class="d-flex flex-nowrap flex-md-wrap overflow-auto overflow-md-visible gap-4 custom-agency-scroll pb-4">
            
            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0">
                <div class="agency-card p-4 rounded-5 border bg-white h-100 transition-all shadow-sm-hover">
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-vector-pen text-white fs-5"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Top Rated</span>
                    </div>
                    <h5 class="fw-800 mb-2">Sonic Talent Group</h5>
                    <p class="text-muted small mb-4">Global management for electronic and indie artists.</p>
                    <div class="pt-3 border-top">
                        <div class="d-flex -space-x-2 mb-3">
                            <img src="https://i.pravatar.cc/100?u=ag1" class="rounded-circle border border-2 border-white" width="30">
                            <img src="https://i.pravatar.cc/100?u=ag2" class="rounded-circle border border-2 border-white ms-n2" width="30">
                            <span class="ms-2 small text-muted">+40 Artists</span>
                        </div>
                        <a href="#" class="text-dark fw-bold text-decoration-none small">View Roster <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0">
                <div class="agency-card p-4 rounded-5 border bg-white h-100 transition-all shadow-sm-hover">
                    <div class="mb-4">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-megaphone-fill text-white fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-800 mb-2">Vibe Marketing</h5>
                    <p class="text-muted small mb-4">Specializing in viral social media growth and PR.</p>
                    <div class="pt-3 border-top">
                        <div class="d-flex -space-x-2 mb-3">
                            <img src="https://i.pravatar.cc/100?u=ag3" class="rounded-circle border border-2 border-white" width="30">
                            <span class="ms-2 small text-muted">+12 Artists</span>
                        </div>
                        <a href="#" class="text-dark fw-bold text-decoration-none small">View Case Studies <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0">
                <div class="agency-card p-4 rounded-5 border bg-white h-100 transition-all shadow-sm-hover">
                    <div class="mb-4">
                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-shield-lock-fill text-dark fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-800 mb-2">Legal Beat Ltd.</h5>
                    <p class="text-muted small mb-4">Entertainment law and copyright protection experts.</p>
                    <div class="pt-3 border-top">
                        <a href="#" class="text-dark fw-bold text-decoration-none small">Legal Services <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0">
                <div class="agency-card p-4 rounded-5 bg-dark h-100 d-flex flex-column justify-content-between shadow-lg">
                    <div>
                        <h5 class="text-white fw-800">Become a Partner</h5>
                        <p class="text-white-50 small">Are you an agency looking to expand your digital footprint?</p>
                    </div>
                    <button class="btn btn-light rounded-pill fw-bold w-100">Join Network</button>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Modern Agency Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2.5px; }
.ms-n2 { margin-left: -10px !important; }

/* The Agency Card UI */
.agency-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f0f0f0 !important;
}

.shadow-sm-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.05) !important;
    border-color: #000 !important;
}

/* Scroll Management */
.custom-agency-scroll::-webkit-scrollbar { display: none; }
.custom-agency-scroll { scrollbar-width: none; }

/* Mobile logic: Ensure cards peek out */
@media (max-width: 768px) {
    .custom-agency-scroll {
        scroll-snap-type: x mandatory;
    }
    .custom-agency-scroll > div {
        scroll-snap-align: start;
    }
}
</style>


<section class="py-6 bg-white">
    <div class="container">
        
        <div class="row align-items-end mb-5">
            <div class="col-lg-7">
                <h6 class="text-secondary fw-bold text-uppercase small tracking-widest mb-2">Legacy & Assets</h6>
                <h2 class="fw-900 display-5 tracking-tighter mb-0">Estate Management</h2>
                <p class="lead text-muted mt-3">Professional oversight of intellectual property, physical assets, and digital legacies for the modern creator.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <button class="btn btn-outline-dark rounded-0 px-4 py-2 fw-bold">Request Consultation</button>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-md-6 col-lg-4">
                <div class="estate-card bg-white border p-4 h-100 transition-all">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="estate-icon-box bg-light p-3">
                            <i class="bi bi-music-note-list fs-3"></i>
                        </div>
                        <span class="x-small fw-bold text-muted">01 / ASSETS</span>
                    </div>
                    <h4 class="fw-800 mb-3">Catalog Rights</h4>
                    <p class="text-muted small mb-4">Automated royalty tracking and legal protection for master recordings and publishing catalogs.</p>
                    <ul class="list-unstyled small mb-4">
                        <li class="mb-2"><i class="bi bi-check2 text-primary me-2"></i> Royalty Collection</li>
                        <li class="mb-2"><i class="bi bi-check2 text-primary me-2"></i> Licensing Oversight</li>
                    </ul>
                    <a href="#" class="btn btn-link text-dark p-0 fw-bold text-decoration-none border-bottom border-2">Manage Catalog</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="estate-card bg-white border p-4 h-100 transition-all">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="estate-icon-box bg-light p-3">
                            <i class="bi bi-building-gear fs-3"></i>
                        </div>
                        <span class="x-small fw-bold text-muted">02 / PROPERTY</span>
                    </div>
                    <h4 class="fw-800 mb-3">Physical Assets</h4>
                    <p class="text-muted small mb-4">Comprehensive management for studio properties, equipment inventories, and physical archives.</p>
                    <div class="d-flex gap-2 mb-4">
                        <div class="bg-light rounded px-2 py-1 x-small fw-bold">Studio Hub</div>
                        <div class="bg-light rounded px-2 py-1 x-small fw-bold">Vaults</div>
                    </div>
                    <a href="#" class="btn btn-link text-dark p-0 fw-bold text-decoration-none border-bottom border-2">Facility Access</a>
                </div>
            </div>

            <div class="col-md-12 col-lg-4">
                <div class="estate-card bg-dark text-white p-4 h-100 d-flex flex-column justify-content-between shadow-lg">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-primary p-3 rounded-circle">
                                <i class="bi bi-shield-check fs-3"></i>
                            </div>
                            <span class="x-small fw-bold opacity-50">03 / TRUST</span>
                        </div>
                        <h4 class="fw-800 mb-3">Legacy Planning</h4>
                        <p class="opacity-75 small">Secure your digital and financial legacy for future generations with smart-contract trusts.</p>
                    </div>
                    <button class="btn btn-primary rounded-0 py-3 fw-bold mt-4">Secure Your Legacy</button>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Modern Estate Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2px; }
.tracking-widest { letter-spacing: 0.1em; }
.x-small { font-size: 0.7rem; }
.py-6 { padding-top: 5rem; padding-bottom: 5rem; }

/* Estate Card UI */
.estate-card {
    border: 1px solid #e9ecef !important;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.estate-card:hover {
    transform: translateY(-5px);
    border-color: #000 !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.estate-icon-box {
    transition: background 0.3s ease;
}

.estate-card:hover .estate-icon-box {
    background-color: #0d6efd !important;
    color: white !important;
}

/* Sharp Edges for Estate look */
.rounded-0 { border-radius: 0 !important; }

/* Link Animation */
.btn-link:hover {
    color: #0d6efd !important;
    border-color: #0d6efd !important;
}
</style>



<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <span class="text-primary fw-800 small text-uppercase tracking-wider">Top Creators</span>
                <h2 class="fw-900 display-5 tracking-tighter m-0">Book Performance</h2>
            </div>
            <div class="d-none d-md-block">
                <a href="#" class="btn btn-dark rounded-pill px-4 fw-bold">View All Talent</a>
            </div>
        </div>

        <div class="d-flex flex-nowrap overflow-auto gap-4 pb-4 custom-booking-scroll" style="scroll-snap-type: x mandatory;">
            
            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="card border-0 rounded-5 bg-white shadow-sm hover-up transition-all p-3">
                    <div class="position-relative mb-3">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600" 
                             class="rounded-5 w-100 object-fit-cover" style="height: 300px;" alt="Talent">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-white text-dark rounded-pill shadow-sm border px-3 py-2">
                                <i class="bi bi-star-fill text-warning"></i> 4.9
                            </span>
                        </div>
                        <div class="position-absolute bottom-0 start-0 m-3">
                            <span class="badge bg-success rounded-pill px-3 py-2 border border-2 border-white shadow-sm">
                                <span class="pulse-white me-1"></span> Available Now
                            </span>
                        </div>
                    </div>
                    <div class="px-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="fw-800 mb-0">Luna Sky</h5>
                            <i class="bi bi-patch-check-fill text-primary"></i>
                        </div>
                        <p class="text-muted small mb-3">Vocalist & Lyricist</p>
                        <div class="d-flex justify-content-between align-items-center bg-light rounded-4 p-3">
                            <div>
                                <span class="d-block x-small text-muted fw-bold">STARTING AT</span>
                                <span class="fw-800 h5 mb-0">$250</span>
                            </div>
                            <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">Book</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-5 col-lg-3 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="card border-0 rounded-5 bg-white shadow-sm hover-up transition-all p-3">
                    <div class="position-relative mb-3">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600" 
                             class="rounded-5 w-100 object-fit-cover" style="height: 300px;" alt="Talent">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-white text-dark rounded-pill shadow-sm border px-3 py-2">
                                <i class="bi bi-star-fill text-warning"></i> 5.0
                            </span>
                        </div>
                    </div>
                    <div class="px-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="fw-800 mb-0">Chloe Vibe</h5>
                            <i class="bi bi-patch-check-fill text-primary"></i>
                        </div>
                        <p class="text-muted small mb-3">DJ & Electronic Producer</p>
                        <div class="d-flex justify-content-between align-items-center bg-light rounded-4 p-3">
                            <div>
                                <span class="d-block x-small text-muted fw-bold">STARTING AT</span>
                                <span class="fw-800 h5 mb-0">$400</span>
                            </div>
                            <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">Book</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-8 col-md-4 col-lg-2 flex-shrink-0" style="scroll-snap-align: start;">
                <div class="h-100 rounded-5 border-dashed d-flex flex-column align-items-center justify-content-center text-center p-4 transition-all hover-bg-primary">
                    <div class="bg-light rounded-circle p-4 mb-3">
                        <i class="bi bi-person-plus fs-2 text-dark"></i>
                    </div>
                    <h6 class="fw-800 mb-1">Join Roster</h6>
                    <p class="x-small text-muted mb-3">Become a featured artist</p>
                    <button class="btn btn-outline-dark btn-sm rounded-pill fw-bold">Apply Now</button>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Modern Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2.5px; }
.x-small { font-size: 0.65rem; }

/* Talent Card Effects */
.hover-up:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.06) !important;
}

.transition-all { transition: all 0.4s ease; }

/* Status Pulse */
.pulse-white {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    margin-right: 5px;
    animation: pulse-ring 1.5s infinite;
}

@keyframes pulse-ring {
    0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(255, 255, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}

/* Scroll Hide */
.custom-booking-scroll::-webkit-scrollbar { display: none; }
.custom-booking-scroll { scrollbar-width: none; }

/* Border Dash */
.border-dashed { border: 2px dashed #dee2e6 !important; }
.hover-bg-primary:hover { border-color: #0d6efd !important; background-color: #f8faff !important; }
</style>


<section class="py-5 bg-white">
    <div class="container">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3">LOCAL OPPORTUNITIES</span>
                    <span class="text-muted small fw-bold"><i class="bi bi-geo-alt-fill"></i> Lagos, Nigeria</span>
                </div>
                <h2 class="fw-900 display-5 tracking-tighter m-0">Companies Hiring Now</h2>
            </div>
            <div class="mt-3 mt-md-0">
                <button class="btn btn-white border rounded-pill px-4 fw-bold shadow-sm hover-up">
                    Edit Location <i class="bi bi-sliders ms-1"></i>
                </button>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-md-6 col-lg-4">
                <div class="job-card bg-white border rounded-5 p-4 transition-all position-relative overflow-hidden h-100 shadow-sm-hover">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="bg-light rounded-4 d-flex align-items-center justify-content-center border" style="width: 60px; height: 60px;">
                            <i class="bi bi-spotify text-success fs-2"></i>
                        </div>
                        <span class="badge bg-light text-dark border-0 rounded-pill px-3 h-fit py-2 x-small fw-bold">Full-Time</span>
                    </div>

                    <h5 class="fw-800 mb-1">Senior Sound Engineer</h5>
                    <p class="text-muted small mb-4">Spotify Studios • <span class="text-dark fw-bold">2.5km away</span></p>
                    
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="bg-light rounded-pill px-3 py-1 x-small fw-bold">Hybrid</span>
                        <span class="bg-light rounded-pill px-3 py-1 x-small fw-bold">Studio Access</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <div class="fw-900 h6 mb-0 text-primary">$80k - $120k <span class="text-muted x-small">/yr</span></div>
                        <button class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">Apply</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="job-card bg-white border rounded-5 p-4 transition-all h-100 shadow-sm-hover">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="bg-dark rounded-4 d-flex align-items-center justify-content-center border" style="width: 60px; height: 60px;">
                            <i class="bi bi-apple text-white fs-2"></i>
                        </div>
                        <span class="badge bg-light text-dark border-0 rounded-pill px-3 h-fit py-2 x-small fw-bold">Remote</span>
                    </div>

                    <h5 class="fw-800 mb-1">Music Content Curator</h5>
                    <p class="text-muted small mb-4">Apple Music • <span class="text-dark fw-bold">Remote Office</span></p>
                    
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="bg-light rounded-pill px-3 py-1 x-small fw-bold">Editor</span>
                        <span class="bg-light rounded-pill px-3 py-1 x-small fw-bold">Editorial</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <div class="fw-900 h6 mb-0 text-primary">$65k - $90k <span class="text-muted x-small">/yr</span></div>
                        <button class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">Apply</button>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-4">
                <div class="card h-100 border-dashed rounded-5 d-flex flex-column align-items-center justify-content-center text-center p-5 bg-light hover-bg-white transition-all">
                    <div class="bg-white rounded-circle shadow-sm p-4 mb-3 border">
                        <i class="bi bi-plus-circle-dotted display-4 text-primary"></i>
                    </div>
                    <h4 class="fw-900">Hire Locally</h4>
                    <p class="text-muted small mb-4 px-3">Are you a studio or agency looking for talent in your area?</p>
                    <div class="d-grid gap-2 w-100 px-4">
                        <button class="btn btn-outline-dark rounded-pill fw-bold">Post a Job</button>
                        <a href="#" class="text-dark fw-bold small text-decoration-none mt-2">View 150+ more jobs</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Modern Job Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2.5px; }
.x-small { font-size: 0.7rem; }
.h-fit { height: fit-content; }

/* Job Card UI */
.job-card {
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.shadow-sm-hover:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 45px rgba(0,0,0,0.06) !important;
    border-color: #0d6efd !important;
}

/* Subtle Hover background for CTA */
.hover-bg-white:hover {
    background-color: #fff !important;
    border-color: #0d6efd !important;
}

/* Utils */
.border-dashed { border: 2px dashed #dee2e6 !important; }
.bg-primary-subtle { background-color: #e7f1ff; }

/* Sticky Apply Button logic */
.job-card .btn-dark {
    transition: 0.3s;
}

.job-card:hover .btn-dark {
    transform: scale(1.05);
}

.btn-white { background: #fff; }
</style>




<section class="py-6 bg-white">
    <div class="container">
        
        <div class="text-center mb-5 pb-3">
            <h6 class="text-primary fw-800 text-uppercase tracking-widest small mb-3">Industry Powerhouses</h6>
            <h2 class="fw-900 display-5 tracking-tighter text-dark mb-4">Partnering with the Best.</h2>
            <div class="mx-auto bg-primary rounded-pill" style="width: 60px; height: 4px;"></div>
        </div>

        <div class="row g-4 justify-content-center">
            
            <div class="col-6 col-md-4 col-lg-3">
                <div class="company-card p-4 rounded-5 border bg-white d-flex flex-column align-items-center justify-content-center transition-all shadow-sm-hover">
                    <div class="company-logo mb-3">
                        <i class="bi bi-vinyl-fill fs-1 text-dark"></i>
                    </div>
                    <h6 class="fw-800 mb-1">Global Records</h6>
                    <span class="x-small text-muted fw-bold">DISTRIBUTION</span>
                    
                    <div class="hover-info mt-3 opacity-0">
                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">View Label</button>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="company-card p-4 rounded-5 border bg-white d-flex flex-column align-items-center justify-content-center transition-all shadow-sm-hover">
                    <div class="company-logo mb-3">
                        <i class="bi bi-cpu-fill fs-1 text-primary"></i>
                    </div>
                    <h6 class="fw-800 mb-1">AudioCore AI</h6>
                    <span class="x-small text-muted fw-bold">TECH PARTNER</span>
                    <div class="hover-info mt-3 opacity-0">
                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">Integrate</button>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="company-card p-4 rounded-5 border bg-white d-flex flex-column align-items-center justify-content-center transition-all shadow-sm-hover">
                    <div class="company-logo mb-3">
                        <i class="bi bi-mic-fill fs-1 text-danger"></i>
                    </div>
                    <h6 class="fw-800 mb-1">RedRoom Studios</h6>
                    <span class="x-small text-muted fw-bold">RECORDING</span>
                    <div class="hover-info mt-3 opacity-0">
                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">Book Space</button>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="company-card p-4 rounded-5 border bg-white d-flex flex-column align-items-center justify-content-center transition-all shadow-sm-hover">
                    <div class="company-logo mb-3">
                        <i class="bi bi-shield-lock-fill fs-1 text-dark"></i>
                    </div>
                    <h6 class="fw-800 mb-1">Copyright Inc.</h6>
                    <span class="x-small text-muted fw-bold">LEGAL TEAM</span>
                    <div class="hover-info mt-3 opacity-0">
                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">Protect</button>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-5 pt-5 border-top">
            <p class="text-center x-small fw-bold text-muted text-uppercase tracking-widest mb-4">Trusted by over 500+ global brands</p>
            <div class="logo-ticker">
                <div class="ticker-track">
                    <span class="mx-5 fw-900 text-light display-6">SONY</span>
                    <span class="mx-5 fw-900 text-light display-6">WARNER</span>
                    <span class="mx-5 fw-900 text-light display-6">APPLE MUSIC</span>
                    <span class="mx-5 fw-900 text-light display-6">SPOTIFY</span>
                    <span class="mx-5 fw-900 text-light display-6">TIDAL</span>
                    <span class="mx-5 fw-900 text-light display-6">SONY</span>
                    <span class="mx-5 fw-900 text-light display-6">WARNER</span>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Modern Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2.5px; }
.tracking-widest { letter-spacing: 0.15em; }
.x-small { font-size: 0.65rem; }
.py-6 { padding: 6rem 0; }

/* Company Card Style */
.company-card {
    min-height: 200px;
    border-color: #f0f0f0 !important;
    overflow: hidden;
}

.shadow-sm-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.05) !important;
    border-color: #000 !important;
}

/* Hover Content Reveal */
.hover-info {
    transition: all 0.3s ease;
    height: 0;
}

.company-card:hover .hover-info {
    opacity: 1;
    height: auto;
}

.company-logo {
    transition: transform 0.4s ease;
}

.company-card:hover .company-logo {
    transform: scale(0.85);
}

/* Logo Ticker Animation */
.logo-ticker {
    overflow: hidden;
    white-space: nowrap;
    position: relative;
}

.ticker-track {
    display: inline-block;
    animation: scroll-ticker 20s linear infinite;
}

.text-light { color: #e9ecef !important; }

@keyframes scroll-ticker {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@media (max-width: 768px) {
    .display-6 { font-size: 1.5rem; }
}
</style>


<section class="py-5 bg-white">
    <div class="container">
        
        <div class="d-flex justify-content-between align-items-end mb-4 px-2">
            <div>
                <h6 class="text-primary fw-bold text-uppercase small tracking-widest">Artist Stays</h6>
                <h2 class="fw-900 display-5 tracking-tighter m-0">Recommended Hotels</h2>
            </div>
            <div class="d-none d-md-flex gap-2">
                <button class="btn btn-light rounded-circle border shadow-sm" onclick="document.getElementById('hotelSlider').scrollBy({left: -400, behavior: 'smooth'})">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="btn btn-light rounded-circle border shadow-sm" onclick="document.getElementById('hotelSlider').scrollBy({left: 400, behavior: 'smooth'})">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <div id="hotelSlider" class="d-flex flex-nowrap overflow-auto gap-4 pb-5 custom-hotel-scroll" style="scroll-snap-type: x mandatory;">
            
            <div class="col-11 col-md-6 col-lg-4 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card border-0 rounded-5 overflow-hidden shadow-sm-hover position-relative h-100">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800" class="w-100 object-fit-cover transition-zoom" style="height: 300px;" alt="Hotel">
                        <div class="position-absolute top-0 end-0 m-3">
                            <div class="glass-tag px-3 py-2 rounded-4 text-white fw-800">
                                $320<span class="x-small opacity-75 fw-normal">/night</span>
                            </div>
                        </div>
                        <button class="btn btn-white rounded-circle position-absolute top-0 start-0 m-3 shadow-sm p-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    
                    <div class="p-4 border border-top-0 rounded-bottom-5">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="fw-800 mb-0">The Grand Lyric</h4>
                            <span class="badge bg-light text-dark rounded-pill border small"><i class="bi bi-star-fill text-warning"></i> 4.9</span>
                        </div>
                        <p class="text-muted small mb-4"><i class="bi bi-geo-alt me-1"></i> Downtown Arts District, LA</p>
                        
                        <div class="d-flex gap-3 mb-4">
                            <div class="text-center">
                                <i class="bi bi-wifi text-primary d-block mb-1"></i>
                                <span class="x-small fw-bold text-muted">Free Wi-Fi</span>
                            </div>
                            <div class="text-center">
                                <i class="bi bi-cup-hot text-primary d-block mb-1"></i>
                                <span class="x-small fw-bold text-muted">Breakfast</span>
                            </div>
                            <div class="text-center">
                                <i class="bi bi-p-circle text-primary d-block mb-1"></i>
                                <span class="x-small fw-bold text-muted">Parking</span>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-dark rounded-pill py-3 fw-bold shadow-sm">Reserve Now</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-11 col-md-6 col-lg-4 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="card border-0 rounded-5 overflow-hidden shadow-sm-hover position-relative h-100">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1582719478250-c89cae4df85b?auto=format&fit=crop&w=800" class="w-100 object-fit-cover transition-zoom" style="height: 300px;" alt="Hotel">
                        <div class="position-absolute top-0 end-0 m-3">
                            <div class="glass-tag px-3 py-2 rounded-4 text-white fw-800">
                                $210<span class="x-small opacity-75 fw-normal">/night</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border border-top-0 rounded-bottom-5">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="fw-800 mb-0">Echo Boutique</h4>
                            <span class="badge bg-light text-dark rounded-pill border small"><i class="bi bi-star-fill text-warning"></i> 4.7</span>
                        </div>
                        <p class="text-muted small mb-4"><i class="bi bi-geo-alt me-1"></i> Shoreditch, London</p>
                        <div class="d-flex gap-3 mb-4">
                            <div class="text-center"><i class="bi bi-moisture text-primary d-block mb-1"></i><span class="x-small fw-bold text-muted">Pool</span></div>
                            <div class="text-center"><i class="bi bi-music-note-beamed text-primary d-block mb-1"></i><span class="x-small fw-bold text-muted">Studio</span></div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-dark rounded-pill py-3 fw-bold shadow-sm">Reserve Now</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-8 col-md-4 col-lg-2 flex-shrink-0" style="scroll-snap-align: center;">
                <div class="h-100 rounded-5 bg-light d-flex flex-column align-items-center justify-content-center text-center p-4 border border-dashed transition-all hover-see-more">
                    <div class="bg-white rounded-circle shadow-sm p-4 mb-3">
                        <i class="bi bi-buildings fs-2 text-primary"></i>
                    </div>
                    <h6 class="fw-800 mb-1">More Cities</h6>
                    <p class="x-small text-muted mb-4">Browse over 5,000 artist-friendly stays</p>
                    <button class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold">Explore All</button>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Modern Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2.5px; }
.x-small { font-size: 0.7rem; }

/* The Modern Card Look */
.shadow-sm-hover {
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.shadow-sm-hover:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
}

/* Glassmorphism Price Tag */
.glass-tag {
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Utilities */
.transition-zoom { transition: transform 0.6s ease; }
.shadow-sm-hover:hover .transition-zoom { transform: scale(1.1); }

.custom-hotel-scroll::-webkit-scrollbar { display: none; }
.custom-hotel-scroll { scrollbar-width: none; }

.btn-white { background-color: white; border: none; }

.border-dashed { border: 2px dashed #dee2e6 !important; }
.hover-see-more:hover { 
    background-color: #f8faff !important; 
    border-color: #0d6efd !important; 
}
</style>


<section class="py-6 bg-white border-top">
    <div class="container">
        
        <div class="text-center mb-5">
            <h6 class="text-primary fw-800 text-uppercase tracking-widest small mb-2">Platform Directory</h6>
            <h2 class="fw-900 display-4 tracking-tighter">Explore Our Services</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Everything you need to create, manage, and scale your career in the modern music ecosystem.</p>
        </div>

        <div class="row g-5">
            
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-900 mb-4 border-start border-primary border-4 ps-3">Entertainment</h5>
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none group">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-play-btn-fill"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Music Reels</span>
                                <small class="text-muted">Short-form discovery</small>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-controller"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Game Center</span>
                                <small class="text-muted">Rhythm & Trivia</small>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-camera-reels"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Video Center</span>
                                <small class="text-muted">High-res premieres</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 col-lg-3">
                <h5 class="fw-900 mb-4 border-start border-dark border-4 ps-3">Professional</h5>
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-person-badge"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Book Talent</span>
                                <small class="text-muted">Hire verified creators</small>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-briefcase"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Local Jobs</span>
                                <small class="text-muted">Opportunities near you</small>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-building"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Agency Roster</span>
                                <small class="text-muted">Management network</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 col-lg-3">
                <h5 class="fw-900 mb-4 border-start border-warning border-4 ps-3">Legacy</h5>
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-shield-check"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Estate Mgmt</span>
                                <small class="text-muted">Digital asset security</small>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-safe2"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Royalty Vault</span>
                                <small class="text-muted">Revenue & Licensing</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 col-lg-3">
                <h5 class="fw-900 mb-4 border-start border-success border-4 ps-3">Lifestyle</h5>
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-hospital"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Hotel Booking</span>
                                <small class="text-muted">Artist-friendly stays</small>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item-service">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <div class="icon-circle bg-light me-3"><i class="bi bi-geo-alt"></i></div>
                            <div>
                                <span class="d-block fw-bold text-dark">Tour Support</span>
                                <small class="text-muted">Local logistics</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</section>

<style>
/* Modern List Typography */
.fw-900 { font-weight: 900; }
.fw-800 { font-weight: 800; }
.tracking-tighter { letter-spacing: -2px; }
.tracking-widest { letter-spacing: 0.1em; }
.py-6 { padding: 6rem 0; }

/* Icon Circle Styling */
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #000;
    transition: all 0.3s ease;
}

/* Hover Interaction */
.nav-item-service a {
    padding: 8px;
    border-radius: 16px;
    transition: background 0.2s ease;
}

.nav-item-service a:hover {
    background-color: #f8f9fa;
}

.nav-item-service a:hover .icon-circle {
    background-color: #0d6efd !important;
    color: #fff !important;
    transform: scale(1.1);
}

.nav-item-service a:hover span {
    color: #0d6efd !important;
}
</style>


<div id="rooterfild"></div>




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



            </div>
        </div>
    </div>

    <script src="/api.js"></script>
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