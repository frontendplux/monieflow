<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Book Your Stay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        
        /* Search Bar Glassmorphism */
        .search-hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1350&q=80') center/cover;
            padding: 60px 0;
            border-radius: 0 0 30px 30px;
        }

        .booking-bar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        /* Hotel Card Styling */
        .hotel-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
            background: white;
        }

        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }

        .hotel-img-container {
            position: relative;
            height: 220px;
        }

        .badge-deal {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #ff4757;
            color: white;
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 50px;
            z-index: 2;
        }

        .price-tag {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1877f2;
        }

        .rating-box {
            background: #fff9db;
            color: #f08c00;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Custom Checkbox for Filters */
        .form-check-input:checked {
            background-color: #1877f2;
            border-color: #1877f2;
        }
    </style>
</head>
<body>

<section class="search-hero mb-5">
    <div class="container">
        <div class="text-center text-white mb-4">
            <h1 class="fw-bold">Find Your Perfect Stay</h1>
            <p class="opacity-75">Compare prices and book the best hotels in Lagos</p>
        </div>
        
        <div class="booking-bar">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="small fw-bold mb-2 text-muted">Location</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="ri-map-pin-line"></i></span>
                        <input type="text" class="form-control border-start-0" value="Lagos, Nigeria">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="small fw-bold mb-2 text-muted">Dates</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="ri-calendar-line"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Feb 11 - Feb 12">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="small fw-bold mb-2 text-muted">Guests</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="ri-user-line"></i></span>
                        <input type="text" class="form-control border-start-0" value="2 Adults, 0 Children">
                    </div>
                </div>
                <div class="col-lg-2">
                    <button class="btn btn-primary w-100 py-2 fw-bold rounded-pill">Search</button>
                </div>
            </div>
        </div>
    </div>
</section>

<main class="container mb-5">
    <div class="row g-4">
        
        <aside class="col-lg-3">
            <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 20px; border-radius: 20px;">
                <h6 class="fw-bold mb-4">Filter by</h6>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Nightly Price</label>
                    <input type="range" class="form-range" min="0" max="500000">
                    <div class="d-flex justify-content-between small">
                        <span>₦0</span>
                        <span>₦500,000+</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted mb-3">Popular Filters</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label small">Free Breakfast</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label small">Swimming Pool</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label small">Free WiFi</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label small">Spa & Wellness</label>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted mb-3">Star Rating</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-light text-dark border flex-grow-1 px-0 small">3★</button>
                        <button class="btn btn-outline-light text-dark border flex-grow-1 px-0 small">4★</button>
                        <button class="btn btn-outline-light text-dark border flex-grow-1 px-0 small">5★</button>
                    </div>
                </div>
            </div>
        </aside>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                <h5 class="fw-bold mb-0">124 hotels found in Lagos</h5>
                <select class="form-select w-auto border-0 bg-transparent fw-bold text-primary">
                    <option>Recommended</option>
                    <option>Price: Low to High</option>
                    <option>Top Rated</option>
                </select>
            </div>

            <div class="card hotel-card shadow-sm mb-4">
                <div class="row g-0">
                    <div class="col-md-4">
                        <div class="hotel-img-container">
                            <span class="badge-deal">44% Off Today</span>
                            <img src="https://picsum.photos/400/300?random=1" class="w-100 h-100 object-fit-cover" alt="Hotel">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-4 d-flex flex-column h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="fw-bold mb-1">HOTEL BON VOYAGE</h5>
                                    <p class="text-muted small mb-2"><i class="ri-map-pin-line"></i> Victoria Island, Lagos • 4-star hotel</p>
                                </div>
                                <div class="rating-box">4.1 <i class="ri-star-fill"></i></div>
                            </div>
                            <p class="text-muted small flex-grow-1">Laid-back hotel offering 2 bars, a spa & a pool, plus dining & a complimentary breakfast buffet.</p>
                            
                            <div class="d-flex justify-content-between align-items-end mt-3">
                                <div>
                                    <div class="text-muted small text-decoration-line-through">₦137,500</div>
                                    <div class="price-tag">₦77,000 <span class="text-muted fw-normal fs-6">/ night</span></div>
                                    <small class="text-success fw-bold"><i class="ri-check-line"></i> Free Cancellation</small>
                                </div>
                                <a href="https://www.google.com/travel/search?q=HOTEL+BON+VOYAGE" target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold">Select Room</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card hotel-card shadow-sm mb-4">
                <div class="row g-0">
                    <div class="col-md-4">
                        <div class="hotel-img-container">
                            <img src="https://picsum.photos/400/300?random=2" class="w-100 h-100 object-fit-cover" alt="Hotel">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="fw-bold mb-1">The Borough Lagos</h5>
                                    <p class="text-muted small mb-2"><i class="ri-map-pin-line"></i> Lekki Phase 1, Lagos • 4-star hotel</p>
                                </div>
                                <div class="rating-box">4.3 <i class="ri-star-fill"></i></div>
                            </div>
                            <p class="text-muted small mb-4">Luxury boutique experience with modern amenities and high-speed fiber internet.</p>
                            
                            <div class="d-flex justify-content-between align-items-end mt-3">
                                <div class="price-tag">₦170,000 <span class="text-muted fw-normal fs-6">/ night</span></div>
                                <button class="btn btn-primary rounded-pill px-4 fw-bold">Select Room</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>