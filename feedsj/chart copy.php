<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Pro Trader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --trade-bg: #0b0e11;
            --trade-card: #161a1e;
            --trade-border: #2b3139;
            --up-color: #0ecb81;
            --down-color: #f6465d;
        }

        body { 
            background-color: var(--trade-bg); 
            color: #eaecef;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
        }

        /* Dashboard Layout */
        .trade-grid {
            display: grid;
            grid-template-columns: 1fr 300px 300px;
            grid-template-rows: 60px 1fr 250px;
            height: 100vh;
            gap: 2px;
            background-color: var(--trade-border);
        }

        /* Components */
        .trade-panel { background: var(--trade-card); padding: 15px; }
        .ticker-bar { grid-column: 1 / span 3; display: flex; align-items: center; gap: 30px; }
        .chart-area { grid-column: 1 / span 1; grid-row: 2 / span 1; position: relative; }
        .order-book { grid-column: 2 / span 1; grid-row: 2 / span 2; }
        .trade-form { grid-column: 3 / span 1; grid-row: 2 / span 2; }
        .history-panel { grid-column: 1 / span 1; grid-row: 3 / span 1; }

        /* Typography & UI */
        .text-up { color: var(--up-color); }
        .text-down { color: var(--down-color); }
        .btn-buy { background-color: var(--up-color); color: #000; font-weight: bold; border: none; }
        .btn-sell { background-color: var(--down-color); color: #fff; font-weight: bold; border: none; }
        
        .form-control-dark {
            background: #2b3139;
            border: 1px solid #474d57;
            color: white;
            font-size: 12px;
        }

        /* Fake Candlestick Chart Placeholder */
        .chart-placeholder {
            height: 100%;
            width: 100%;
            background: linear-gradient(transparent 95%, rgba(255,255,255,0.05) 5%),
                        linear-gradient(90deg, transparent 95%, rgba(255,255,255,0.05) 5%);
            background-size: 40px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--trade-border);
        }
    </style>
</head>
<body>

<div class="trade-grid">
    
    <header class="trade-panel ticker-bar border-bottom border-secondary">
        <div class="d-flex align-items-center gap-2">
            <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.png" width="24">
            <span class="fw-bold fs-5">BTC/MC</span>
        </div>
        <div>
            <div class="text-up fs-6 fw-bold">42,530.12</div>
            <div class="small">$42,530.12</div>
        </div>
        <div class="d-none d-md-block">
            <div class="text-muted small">24h Change</div>
            <div class="text-up">+2.45%</div>
        </div>
        <div class="d-none d-md-block">
            <div class="text-muted small">24h High</div>
            <div>43,100.00</div>
        </div>
        <div class="d-none d-md-block">
            <div class="text-muted small">24h Volume(BTC)</div>
            <div>12,450.80</div>
        </div>
    </header>

    <main class="trade-panel chart-area p-0 overflow-hidden">
        <div class="p-2 d-flex gap-2 border-bottom border-secondary">
            <button class="btn btn-sm btn-outline-secondary py-0">1H</button>
            <button class="btn btn-sm btn-outline-secondary py-0 active">4H</button>
            <button class="btn btn-sm btn-outline-secondary py-0">1D</button>
            <i class="ri-settings-4-line ms-auto"></i>
        </div>
        <div class="chart-placeholder">
            <div class="text-muted text-center">
                <i class="ri-bar-chart-fill display-1 opacity-25"></i>
                <p>TradingView Chart Engine Loading...</p>
            </div>
        </div>
    </main>

    <aside class="trade-panel order-book border-start border-secondary">
        <h6 class="small fw-bold text-muted mb-3 uppercase">Order Book</h6>
        <div class="mb-2">
            <div class="d-flex justify-content-between text-down opacity-75 py-1"><span>42,535.0</span><span>0.054</span><span>2,296.8</span></div>
            <div class="d-flex justify-content-between text-down opacity-75 py-1"><span>42,534.5</span><span>0.112</span><span>4,763.8</span></div>
            <div class="d-flex justify-content-between text-down opacity-75 py-1"><span>42,532.0</span><span>1.405</span><span>59,757.4</span></div>
        </div>
        <div class="text-center py-2 border-top border-bottom border-secondary my-2">
            <span class="text-up fs-5 fw-bold">42,530.12 <i class="ri-arrow-up-fill"></i></span>
        </div>
        <div>
            <div class="d-flex justify-content-between text-up opacity-75 py-1"><span>42,528.0</span><span>0.850</span><span>36,148.8</span></div>
            <div class="d-flex justify-content-between text-up opacity-75 py-1"><span>42,525.5</span><span>0.021</span><span>893.0</span></div>
            <div class="d-flex justify-content-between text-up opacity-75 py-1"><span>42,524.0</span><span>2.110</span><span>89,725.6</span></div>
        </div>
    </aside>

    <aside class="trade-panel trade-form border-start border-secondary">
        <nav class="nav nav-pills nav-justified mb-4 bg-dark rounded-pill p-1">
            <a class="nav-link active bg-secondary py-1 small rounded-pill" href="#">Limit</a>
            <a class="nav-link py-1 small" href="#">Market</a>
        </nav>
        
        <div class="mb-3">
            <label class="small text-muted mb-1">Price (MC)</label>
            <input type="text" class="form-control form-control-dark" value="42530.12">
        </div>
        <div class="mb-3">
            <label class="small text-muted mb-1">Amount (BTC)</label>
            <input type="text" class="form-control form-control-dark" placeholder="0.00">
        </div>
        
        <div class="progress mb-3" style="height: 4px; background: #2b3139;">
            <div class="progress-bar bg-primary" style="width: 25%"></div>
        </div>

        <button class="btn btn-buy w-100 py-2 mb-3">Buy BTC</button>
        <button class="btn btn-sell w-100 py-2">Sell BTC</button>
    </aside>

    <section class="trade-panel history-panel border-top border-secondary">
        <ul class="nav nav-tabs border-0 mb-3">
            <li class="nav-item"><a class="nav-link active bg-transparent text-white border-0 border-bottom border-primary" href="#">Open Orders (2)</a></li>
            <li class="nav-item"><a class="nav-link bg-transparent text-muted border-0" href="#">Trade History</a></li>
        </ul>
        <table class="table table-dark table-borderless small">
            <thead>
                <tr class="text-muted">
                    <th>Date</th>
                    <th>Pair</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>10:45:22</td>
                    <td>BTC/MC</td>
                    <td class="text-up">Buy</td>
                    <td>42,510.00</td>
                    <td>0.1200</td>
                    <td class="text-end text-primary">Cancel</td>
                </tr>
            </tbody>
        </table>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>