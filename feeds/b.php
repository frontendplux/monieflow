<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Barn | monieFlow Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --barn-orange: #f4a261;
            --barn-green: #2a9d8f;
            --barn-dark: #1d1d1d;
            --mflow-gold: #ffcc00;
        }

        body {
            background-color: #fdfcf0; /* Warm paper-like background */
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 50px;
        }

        /* --- THE BARN HERO --- */
        .barn-hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            border-radius: 0 0 40px 40px;
            padding: 60px 20px;
            color: white;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .yield-circle {
            width: 180px; height: 180px;
            border: 8px solid var(--mflow-gold);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(5px);
        }

        /* --- HARVEST CARDS --- */
        .harvest-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-top: -40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .growth-meter {
            height: 10px;
            background: #eee;
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
        }
        .growth-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--barn-green), #80ed99);
            width: 65%; /* Dynamic width */
        }

        /* --- STAKING OPTIONS --- */
        .silo-item {
            background: white;
            border-radius: 15px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            transition: 0.2s;
            border: 1px solid transparent;
        }
        .silo-item:hover { border-color: var(--barn-orange); transform: scale(1.02); }

        .silo-icon {
            width: 50px; height: 50px;
            background: #fff4ed;
            color: var(--barn-orange);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .btn-harvest {
            background: var(--barn-green);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(42, 157, 143, 0.3);
        }
        
        .btn-stake {
            background: var(--barn-dark);
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="barn-hero">
    <div class="yield-circle">
        <small class="text-uppercase" style="letter-spacing: 2px; font-size: 10px;">Total Yield</small>
        <div class="h2 fw-bold mb-0">842.50</div>
        <small class="text-warning fw-bold">MC EARNED</small>
    </div>
    <h3 class="fw-bold">Your Digital Barn</h3>
    <p class="opacity-75">Let your monieCoins grow while you sleep.</p>
</div>

<div class="container">
    <div class="harvest-card text-center">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold">Next Harvest</span>
            <span class="text-success fw-bold">65% Grown</span>
        </div>
        <div class="growth-meter">
            <div class="growth-fill"></div>
        </div>
        <div class="d-flex justify-content-between small text-muted">
            <span>Started: Feb 1st</span>
            <span>Ready: Feb 28th</span>
        </div>
        <button class="btn btn-harvest mt-3 w-100">HARVEST NOW (240 MC)</button>
    </div>

    <h5 class="fw-bold mt-5 mb-3">Available Silos</h5>
    
    <div class="silo-item">
        <div class="d-flex align-items-center">
            <div class="silo-icon me-3"><i class="ri-seedling-line"></i></div>
            <div>
                <h6 class="mb-0 fw-bold">Starter Soil</h6>
                <small class="text-muted">7-Day Lock • 5% APY</small>
            </div>
        </div>
        <div class="text-end">
            <div class="fw-bold text-success">Low Risk</div>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </div>
    </div>

    <div class="silo-item">
        <div class="d-flex align-items-center">
            <div class="silo-icon me-3" style="background: #eef2ff; color: #4361ee;"><i class="ri-windy-line"></i></div>
            <div>
                <h6 class="mb-0 fw-bold">Deep Roots</h6>
                <small class="text-muted">30-Day Lock • 12% APY</small>
            </div>
        </div>
        <div class="text-end">
            <div class="fw-bold text-primary">High Yield</div>
            <i class="ri-arrow-right-s-line text-muted"></i>
        </div>
    </div>

    <div class="p-4 rounded-4 mt-4" style="background: rgba(244, 162, 97, 0.1); border: 1px dashed var(--barn-orange);">
        <div class="d-flex align-items-start">
            <i class="ri-information-fill fs-4 text-warning me-3"></i>
            <div>
                <h6 class="fw-bold mb-1">How it works?</h6>
                <p class="small text-muted mb-0">Moving coins to the Barn locks them for a set time. In return, you "harvest" extra monieCoins as interest!</p>
            </div>
        </div>
        <button class="btn btn-stake">STAKE monieCOINS</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>