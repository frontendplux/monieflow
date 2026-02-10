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
    <title>Marketplace | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --fb-bg: #f0f2f5;
            --fb-white: #ffffff;
            --fb-blue: #1877f2;
            --fb-hover: #e4e6eb;
        }

        body { background-color: var(--fb-bg); font-family: 'Segoe UI', sans-serif; }

        /* Sidebar Styling */
        .market-sidebar {
            background: var(--fb-white);
            height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
            border-right: 1px solid var(--fb-hover);
            padding: 15px;
            overflow-y: auto;
        }

        .category-link {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            color: #050505;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .category-link:hover { background: var(--fb-hover); }
        .category-link i {
            width: 36px; height: 36px;
            background: #e4e6eb;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-right: 12px; font-size: 18px;
        }

        /* Product Card Styling */
        .product-card {
            background: var(--fb-white);
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .product-card:hover { transform: scale(1.02); }

        .product-img-container {
            width: 100%;
            aspect-ratio: 1/1;
            position: relative;
        }

        .product-img {
            width: 100%; height: 100%; object-fit: cover;
        }

        .price-badge {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .market-search {
            background: #f0f2f5;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            width: 100%;
        }
    </style>
</head>
<body>

<header class="sticky-top bg-white border-bottom d-flex align-items-center justify-content-between px-3" style="height: 56px; z-index: 1050;">
    <div class="d-flex align-items-center">
        <a href="/home" class="text-decoration-none brand-logo me-3" style="font-weight: 800; font-size: 1.5rem; color: var(--fb-blue);">monieFlow</a>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary rounded-pill fw-bold px-3"><i class="ri-add-line"></i> Create Listing</button>
        <div class="fb-nav-icon bg-light rounded-circle p-2"><i class="ri-user-3-fill"></i></div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 d-none d-md-block market-sidebar">
            <h4 class="fw-bold mb-3">Marketplace</h4>
            <input type="text" class="market-search mb-4" placeholder="Search Marketplace">
            
            <nav>
                <a href="#" class="category-link"><i class="ri-layout-grid-fill"></i> Browse All</a>
                <a href="#" class="category-link"><i class="ri-notification-3-line"></i> Notifications</a>
                <a href="#" class="category-link"><i class="ri-inbox-line"></i> Inbox</a>
                <a href="#" class="category-link"><i class="ri-shopping-bag-3-line"></i> Buying</a>
                <a href="#" class="category-link"><i class="ri-price-tag-3-line"></i> Selling</a>
                <hr>
                <h6 class="text-secondary px-2">Categories</h6>
                <a href="#" class="category-link"><i class="ri-car-fill"></i> Vehicles</a>
                <a href="#" class="category-link"><i class="ri-home-4-fill"></i> Property Rentals</a>
                <a href="#" class="category-link"><i class="ri-t-shirt-fill"></i> Apparel</a>
                <a href="#" class="category-link"><i class="ri-smartphone-fill"></i> Electronics</a>
                <a href="#" class="category-link"><i class="ri-mickey-fill"></i> Family</a>
            </nav>
        </div>

        <div class="col-12 col-md-9 p-4">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h5 class="fw-bold mb-0">Today's Picks</h5>
                    <small class="text-muted">Lagos, Nigeria · Within 60 km</small>
                </div>
            </div>

            <div class="row g-3">
                <?php 
                $items = [
                    ['name' => 'iPhone 15 Pro Max', 'price' => '₦1,200,000', 'loc' => 'Lekki', 'img' => '1'],
                    ['name' => '2022 Toyota Camry', 'price' => '₦25,000,000', 'loc' => 'Ikeja', 'img' => '2'],
                    ['name' => 'Designer Leather Sofa', 'price' => '₦450,000', 'loc' => 'Victoria Island', 'img' => '3'],
                    ['name' => 'Gaming PC - RTX 4090', 'price' => '₦1,800,000', 'loc' => 'Surulere', 'img' => '4'],
                    ['name' => 'Modern Apartment', 'price' => '₦3,500,000/yr', 'loc' => 'Ajah', 'img' => '5'],
                    ['name' => 'Sony PlayStation 5', 'price' => '₦650,000', 'loc' => 'Gbagada', 'img' => '6'],
                ];
                foreach($items as $item): 
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <div class="product-img-container">
                            <img src="https://picsum.photos/400/400?random=<?= $item['img'] ?>" class="product-img">
                            <div class="price-badge"><?= $item['price'] ?></div>
                        </div>
                        <div class="p-2">
                            <div class="fw-bold text-truncate" style="font-size: 0.95rem;"><?= $item['name'] ?></div>
                            <div class="text-muted small"><?= $item['loc'] ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>