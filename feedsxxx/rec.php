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
    <title>Receive Coins | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-dark: #1a1a1a;
            --fb-bg: #f0f2f5;
        }

        body { background-color: var(--fb-bg); font-family: 'Segoe UI', sans-serif; }

        /* Main Receiving Card */
        .receive-card {
            background: #fff;
            border-radius: 24px;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .qr-wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 20px;
            display: inline-block;
            border: 2px solid #f0f2f5;
            margin-bottom: 25px;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            background: #eee; /* Placeholder for QR */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .id-badge {
            background: #f0f2f5;
            padding: 12px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #e4e6eb;
        }

        .share-option {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid #ddd;
            font-size: 20px;
            cursor: pointer;
            transition: 0.2s;
        }
        .share-option:hover {
            background: var(--mflow-blue);
            color: #fff;
            border-color: var(--mflow-blue);
        }

        @media (max-width: 992px) {
            .side-panel { display: none; }
        }
    </style>
</head>
<body>

<header class="bg-white border-bottom p-3 d-flex align-items-center sticky-top">
    <a href="/wallet" class="text-dark me-3"><i class="ri-arrow-left-line fs-4"></i></a>
    <h5 class="mb-0 fw-bold">Receive monieCoins</h5>
</header>

<div class="container py-5">
    <div class="row justify-content-center">
        
        <div class="col-lg-3 side-panel">
            <div class="bg-white rounded-4 p-4 shadow-sm mb-4">
                <h6 class="fw-bold mb-3">Security Tips</h6>
                <ul class="small text-secondary ps-3">
                    <li class="mb-2">Only share your QR code with trusted users.</li>
                    <li>monieFlow staff will never ask for your private key.</li>
                </ul>
            </div>
        </div>

        <div class="col-12 col-md-8 col-lg-5">
            <div class="receive-card">
                <div class="mb-4">
                    <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle border border-3 border-white shadow-sm" width="70">
                    <h5 class="mt-3 mb-1 fw-bold"><?= $profile['username'] ?></h5>
                    <p class="text-secondary small">Scan to send me coins</p>
                </div>

                <div class="qr-wrapper">
                    <div class="qr-code">
                        <i class="ri-qr-code-line text-muted" style="font-size: 100px;"></i>
                    </div>
                </div>

                <div class="id-badge mb-4">
                    <div class="text-start">
                        <small class="text-muted d-block" style="font-size: 10px;">YOUR WALLET ID</small>
                        <span class="fw-bold" id="walletID">MF-<?= strtoupper(substr($userData['id'], 0, 8)) ?>-2026</span>
                    </div>
                    <i class="ri-file-copy-line text-primary cursor-pointer fs-4" onclick="copyID()"></i>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <div class="share-option" onclick="share('whatsapp')"><i class="ri-whatsapp-line"></i></div>
                    <div class="share-option" onclick="share('messenger')"><i class="ri-messenger-line"></i></div>
                    <div class="share-option" onclick="share('generic')"><i class="ri-share-line"></i></div>
                </div>
                <p class="mt-3 small text-muted">Share your link to get paid faster</p>
            </div>
        </div>

        <div class="col-lg-3 side-panel">
            <div class="bg-primary bg-opacity-10 rounded-4 p-4 border border-primary border-opacity-25">
                <h6 class="fw-bold text-primary">Request Payment</h6>
                <p class="small">Want to ask for a specific amount? Create a payment link.</p>
                <button class="btn btn-primary btn-sm w-100 fw-bold rounded-pill">Create Link</button>
            </div>
        </div>

    </div>
</div>

<script>
    function copyID() {
        const id = document.getElementById('walletID').innerText;
        navigator.clipboard.writeText(id);
        alert("Wallet ID copied to clipboard!");
    }

    function share(platform) {
        const text = "Hey! Send me some monieCoins on monieFlow. My ID is: " + document.getElementById('walletID').innerText;
        if(platform === 'whatsapp') {
            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`);
        } else {
            // Web Share API for mobile
            if (navigator.share) {
                navigator.share({
                    title: 'monieFlow Payment',
                    text: text,
                    url: window.location.href
                });
            } else {
                alert("Share link copied!");
            }
        }
    }
</script>

</body>
</html>