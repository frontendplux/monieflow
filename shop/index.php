<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Premium Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }

        /* Sidebar Glassmorphism */
        .filter-sidebar {
            background: white;
            border-radius: 20px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            position: sticky;
            top: 20px;
        }

        /* Product Card Design */
        .product-card {
            border: none;
            border-radius: 20px;
            background: white;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }

        .img-container {
            position: relative;
            background: #f1f5f9;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-img {
            max-width: 80%;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.1) rotate(5deg);
        }

        /* Badge Styling */
        .promo-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #1e293b;
            color: white;
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .action-overlay {
            position: absolute;
            bottom: -50px;
            left: 0;
            width: 100%;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 15px;
            transition: bottom 0.3s ease;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .product-card:hover .action-overlay {
            bottom: 0;
        }

        .btn-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: white;
            color: #1e293b;
            transition: 0.3s;
        }

        .btn-circle:hover { background: #1e293b; color: white; border-color: #1e293b; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row g-4">
        
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="filter-sidebar">
                <h5 class="fw-bold mb-4">Filters</h5>
                
                <div class="mb-4">
                    <label class="small fw-bold text-muted text-uppercase mb-2">Category</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" checked id="cat1">
                        <label class="form-check-label small" for="cat1">Hardware</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="cat2">
                        <label class="form-check-label small" for="cat2">Software</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="cat3">
                        <label class="form-check-label small" for="cat3">Accessories</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold text-muted text-uppercase mb-2">Price Range</label>
                    <input type="range" class="form-range" min="0" max="5000">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>$0</span>
                        <span>$5000</span>
                    </div>
                </div>

                <button class="btn btn-dark w-100 rounded-pill py-2">Apply Filters</button>
            </div>
        </aside>

        <main class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                <div>
                    <h3 class="fw-bold mb-0">Shop All</h3>
                    <p class="text-muted small mb-0">Showing 24 of 142 products</p>
                </div>
                <select class="form-select w-auto rounded-pill border-0 shadow-sm">
                    <option>Sort by: Popularity</option>
                    <option>Price: Low to High</option>
                    <option>Newest Arrivals</option>
                </select>
            </div>

            <div class="row g-4">
                
                <div class="col-md-6 col-xl-4">
                    <div class="card product-card">
                        <div class="img-container">
                            <span class="promo-badge">NEW</span>
                            <img src="https://m.media-amazon.com/images/I/71fu79SjNfL._AC_SL1500_.jpg" class="product-img" alt="Product">
                            <div class="action-overlay">
                                <button class="btn-circle"><i class="ri-shopping-cart-line"></i></button>
                                <button class="btn-circle"><i class="ri-heart-line"></i></button>
                                <button class="btn-circle"><i class="ri-eye-line"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-4 text-center">
                            <small class="text-muted text-uppercase fw-bold">Gadgets</small>
                            <h5 class="fw-bold mt-1 mb-2">Pro Headphones X</h5>
                            <div class="text-warning mb-2 small">
                                <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-half-fill"></i>
                                <span class="text-muted ms-1">(42)</span>
                            </div>
                            <h4 class="fw-bold text-primary mb-0">$299.00</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card product-card">
                        <div class="img-container">
                            <span class="promo-badge bg-danger">-20%</span>
                            <img src="https://m.media-amazon.com/images/I/61mwaR0jZkL._AC_SL1500_.jpg" class="product-img" alt="Product">
                            <div class="action-overlay">
                                <button class="btn-circle"><i class="ri-shopping-cart-line"></i></button>
                                <button class="btn-circle"><i class="ri-eye-line"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-4 text-center">
                            <small class="text-muted text-uppercase fw-bold">Computing</small>
                            <h5 class="fw-bold mt-1 mb-2">Super Mouse Elite</h5>
                            <div class="text-warning mb-2 small">
                                <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i>
                            </div>
                            <div class="d-flex justify-content-center gap-2">
                                <h4 class="fw-bold text-primary mb-0">$89.00</h4>
                                <span class="text-muted text-decoration-line-through small mt-2">$110</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card product-card">
                        <div class="img-container">
                            <img src="https://m.media-amazon.com/images/I/71YvR5F1XjL._AC_SL1500_.jpg" class="product-img" alt="Product">
                            <div class="action-overlay">
                                <button class="btn-circle"><i class="ri-shopping-cart-line"></i></button>
                                <button class="btn-circle"><i class="ri-eye-line"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-4 text-center">
                            <small class="text-muted text-uppercase fw-bold">Audio</small>
                            <h5 class="fw-bold mt-1 mb-2">Bluetooth Speaker Z</h5>
                            <div class="text-warning mb-2 small">
                                <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-line"></i>
                            </div>
                            <h4 class="fw-bold text-primary mb-0">$159.00</h4>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>