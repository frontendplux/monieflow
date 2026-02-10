<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-gold: #ffcc00;
            --mflow-dark: #0f1419;
        }

        body { background-color: #f7f9fb; font-family: 'Inter', sans-serif; color: var(--mflow-dark); }

        /* --- SEARCH & CATEGORY --- */
        .search-container {
            background: #fff;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .search-bar {
            background: #f0f2f5;
            border-radius: 50px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
        }
        .search-bar input { border: none; background: transparent; width: 100%; outline: none; margin-left: 10px; }

        /* --- AUCTION BANNER --- */
        .auction-card {
            background: linear-gradient(135deg, #000, #333);
            border-radius: 20px;
            padding: 30px;
            color: #fff;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 2px solid var(--mflow-gold);
            position: relative;
            overflow: hidden;
        }
        .auction-card::after {
            content: 'LIVE'; position: absolute; top: 15px; right: -30px;
            background: #ff4d6d; padding: 5px 40px; transform: rotate(45deg);
            font-size: 10px; font-weight: bold;
        }

        /* --- PRODUCT GRID --- */
        .product-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            transition: 0.3s;
            border: 1px solid #f0f0f0;
            height: 100%;
        }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        
        .product-img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            background: #f8f8f8;
        }

        .price-tag {
            color: var(--mflow-blue);
            font-weight: 800;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .seller-badge {
            font-size: 11px;
            background: #eef5ff;
            color: var(--mflow-blue);
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* --- CART FAB --- */
        .cart-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px; height: 60px;
            background: var(--mflow-dark);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            z-index: 1000;
            text-decoration: none;
        }
        .cart-count {
            position: absolute; top: 0; right: 0;
            background: var(--mflow-gold); color: #000;
            width: 22px; height: 22px; border-radius: 50%;
            font-size: 11px; font-weight: bold;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid white;
        }
    </style>
</head>
<body>

<div class="search-container">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-8">
                <div class="search-bar">
                    <i class="ri-search-line text-muted"></i>
                    <input type="text" placeholder="Search products, digital art, etc...">
                </div>
            </div>
            <div class="col-4 text-end">
                <a href="#" class="text-dark"><i class="ri-equalizer-line fs-3"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="auction-card">
        <div>
            <span class="text-warning small fw-bold">FEATURED AUCTION</span>
            <h2 class="fw-bold">monieFlow Pro Max</h2>
            <p class="mb-3 opacity-75">Starting at 50,000 MC</p>
            <div class="d-flex gap-2">
                <div class="bg-white bg-opacity-10 p-2 rounded text-center">
                    <div class="fw-bold">04</div><small style="font-size: 9px;">HRS</small>
                </div>
                <div class="bg-white bg-opacity-10 p-2 rounded text-center">
                    <div class="fw-bold">12</div><small style="font-size: 9px;">MIN</small>
                </div>
                <div class="bg-white bg-opacity-10 p-2 rounded text-center">
                    <div class="fw-bold">45</div><small style="font-size: 9px;">SEC</small>
                </div>
            </div>
        </div>
        <img src="https://images.unsplash.com/photo-1510557883980-4d9562328552?auto=format&fit=crop&w=300&q=80" class="d-none d-md-block rounded-3" style="width: 150px;">
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Shop All</h4>
        <div class="dropdown">
            <button class="btn btn-sm btn-white border rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown">Sort By</button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-6 col-lg-3">
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&q=80" class="product-img">
                <div class="p-3">
                    <span class="seller-badge">Premium Shop</span>
                    <h6 class="mt-2 fw-bold text-truncate">Wireless Studio Beats</h6>
                    <div class="price-tag mt-1">
                        <i class="ri-copper-coin-fill text-warning"></i> 4,500
                    </div>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-3 rounded-pill">Add to Cart</button>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=500&q=80" class="product-img">
                <div class="p-3">
                    <span class="seller-badge">Certified</span>
                    <h6 class="mt-2 fw-bold text-truncate">Nordic Minimalist Watch</h6>
                    <div class="price-tag mt-1">
                        <i class="ri-copper-coin-fill text-warning"></i> 2,200
                    </div>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-3 rounded-pill">Add to Cart</button>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1611186871348-b1ec696e523b?auto=format&fit=crop&w=500&q=80" class="product-img">
                <div class="p-3">
                    <span class="seller-badge">Digital Content</span>
                    <h6 class="mt-2 fw-bold text-truncate">Creator Pack (LUTs)</h6>
                    <div class="price-tag mt-1">
                        <i class="ri-copper-coin-fill text-warning"></i> 850
                    </div>
                    <button class="btn btn-primary btn-sm w-100 mt-3 rounded-pill">Buy Now</button>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=500&q=80" class="product-img">
                <div class="p-3">
                    <span class="seller-badge">Top Rated</span>
                    <h6 class="mt-2 fw-bold text-truncate">Red Velocity Kicks</h6>
                    <div class="price-tag mt-1">
                        <i class="ri-copper-coin-fill text-warning"></i> 6,900
                    </div>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-3 rounded-pill">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="/cart" class="cart-fab">
    <i class="ri-shopping-cart-2-fill fs-4"></i>
    <span class="cart-count">3</span>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>