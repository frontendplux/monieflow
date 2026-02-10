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
    <title>Product Details | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --fb-bg: #f0f2f5;
            --fb-white: #ffffff;
            --fb-blue: #1877f2;
            --fb-hover: #e4e6eb;
        }

        body { background-color: var(--fb-bg); font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }

        /* Gallery Side */
        .product-gallery {
            background: #000;
            height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .main-img { max-width: 100%; max-height: 100%; object-fit: contain; }

        /* Info Sidebar */
        .details-sidebar {
            background: var(--fb-white);
            height: calc(100vh - 56px);
            overflow-y: auto;
            border-left: 1px solid var(--fb-hover);
            padding: 24px;
        }

        .seller-box {
            border: 1px solid var(--fb-hover);
            border-radius: 8px;
            padding: 15px;
        }

        .chat-input {
            background: #f0f2f5;
            border-radius: 20px;
            padding: 10px 15px;
            border: none;
            width: 100%;
        }

        .action-btn-group .btn {
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .product-gallery { height: 400px; }
            .details-sidebar { height: auto; border-left: none; }
        }
    </style>
</head>
<body>

<header class="sticky-top bg-white border-bottom d-flex align-items-center justify-content-between px-3" style="height: 56px; z-index: 1000;">
    <div class="d-flex align-items-center">
        <a href="/market" class="text-dark me-3"><i class="ri-arrow-left-line fs-4"></i></a>
        <div class="brand-logo fw-bold text-primary fs-4">monieFlow Market</div>
    </div>
    <div class="d-flex gap-2">
        <div class="fb-nav-icon bg-light rounded-circle p-2"><i class="ri-share-forward-line"></i></div>
        <div class="fb-nav-icon bg-light rounded-circle p-2"><i class="ri-heart-line"></i></div>
    </div>
</header>

<div class="container p-0">
    <div class="row g-0">
        <div class="col-lg-7 product-gallery">
            <img src="https://picsum.photos/1200/800?random=10" class="main-img" alt="Product">
            <div class="position-absolute bottom-0 start-0 p-4 w-100 d-flex gap-2 overflow-auto" style="background: linear-gradient(transparent, rgba(0,0,0,0.5));">
                <img src="https://picsum.photos/1200/800?random=10" width="60" class="rounded border border-primary">
                <img src="https://picsum.photos/1200/800?random=15" width="60" class="rounded border opacity-75">
                <img src="https://picsum.photos/1200/800?random=20" width="60" class="rounded border opacity-75">
            </div>
        </div>

        <div class="col-lg-5 details-sidebar">
            <h2 class="fw-bold">iPhone 15 Pro Max - 256GB</h2>
            <h3 class="text-primary fw-bold mb-3">₦1,200,000</h3>
            
            <div class="d-flex gap-2 mb-4 text-muted small">
                <span>Listed 2 days ago in Lagos</span>
            </div>

            <hr>

            <h5 class="fw-bold">Details</h5>
            <div class="row mb-4">
                <div class="col-6 mb-2"><b>Condition</b>: New</div>
                <div class="col-6 mb-2"><b>Brand</b>: Apple</div>
                <div class="col-6 mb-2"><b>Color</b>: Titanium</div>
                <div class="col-6 mb-2"><b>Storage</b>: 256 GB</div>
            </div>

            <p class="text-secondary">
                Brand new iPhone 15 Pro Max. Factory unlocked, still in box. Genuine buyers only. 
                Swap is not allowed. Delivery available within Lagos.
            </p>

            <hr>

            <h5 class="fw-bold mb-3">Seller Information</h5>
            <div class="seller-box mb-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://i.pravatar.cc/150?u=seller" width="50" class="rounded-circle me-3">
                    <div>
                        <div class="fw-bold">Tunde Adebayor</div>
                        <div class="small text-muted">Joined monieFlow in 2023</div>
                        <div class="text-warning small"><i class="ri-star-fill"></i> 4.8 Seller Rating</div>
                    </div>
                </div>
                <button class="btn btn-light w-100 fw-bold border mb-2">View Profile</button>
            </div>

            <div class="bg-light p-3 rounded-3 mb-4">
                <h6 class="fw-bold mb-2">Message Seller</h6>
                <div class="d-flex gap-2">
                    <input type="text" class="chat-input" value="Is this still available?">
                    <button class="btn btn-primary px-3 rounded-pill">Send</button>
                </div>
            </div>

            <div class="action-btn-group d-flex gap-2 mb-5">
                <button class="btn btn-primary flex-grow-1"><i class="ri-messenger-fill"></i> Message</button>
                <button class="btn btn-light border flex-grow-1"><i class="ri-save-line"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>