<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Responsive Candle Chart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #0b0e11;
            --bg-card: #161a1e;
            --brd: #2b3139;
            --up: #0ecb81;
            --down: #f6465d;
        }

        body { background: var(--bg-main); color: #eaecef; font-family: 'Inter', sans-serif; overflow-x: hidden; }

        .trade-container { display: flex; flex-direction: column; height: 100vh; }
        
        /* Navbar Info */
        .ticker-header { background: var(--bg-card); border-bottom: 1px solid var(--brd); padding: 10px 20px; }

        /* Chart Canvas */
        .chart-container {
            background: var(--bg-card);
            position: relative;
            flex-grow: 1;
            min-height: 400px;
            background-image: linear-gradient(var(--brd) 1px, transparent 1px), linear-gradient(90deg, var(--brd) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        /* CSS CANDLESTICK SIMULATION */
        .candle-wrapper {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            height: 200px;
            position: absolute;
            bottom: 100px;
            left: 50px;
        }

        .candle {
            width: 12px;
            position: relative;
            border-radius: 2px;
        }

        /* The wick (line through the candle) */
        .candle::before {
            content: '';
            position: absolute;
            width: 2px;
            height: 140%;
            top: -20%;
            left: 5px;
            background: inherit;
            opacity: 0.5;
        }

        .bullish { background: var(--up); }
        .bearish { background: var(--down); }

        /* Layout Panels */
        .side-panel { background: var(--bg-card); border-left: 1px solid var(--brd); width: 320px; }
        .order-panel { background: var(--bg-card); border-top: 1px solid var(--brd); height: 280px; }

        @media (max-width: 992px) {
            .side-panel { width: 100%; border-left: none; border-top: 1px solid var(--brd); }
            .trade-container { height: auto; }
        }

        .btn-buy { background: var(--up); color: black; font-weight: 700; }
        .btn-sell { background: var(--down); color: white; font-weight: 700; }
        .form-input { background: #2b3139; border: 1px solid #474d57; color: white; }
    </style>
</head>
<body>

<div class="trade-container">
    <div class="ticker-header d-flex align-items-center flex-wrap gap-4">
        <div class="d-flex align-items-center gap-2">
            <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.png" width="28">
            <span class="fs-5 fw-bold">BTC/MC</span>
        </div>
        <div>
            <div class="text-success fw-bold fs-5">42,530.12</div>
            <div class="small text-muted">$42,530.12</div>
        </div>
        <div class="d-none d-sm-block">
            <div class="small text-muted">24h Change</div>
            <div class="text-success">+2.45%</div>
        </div>
        <div class="d-none d-md-block">
            <div class="small text-muted">24h High</div>
            <div>43,100.00</div>
        </div>
    </div>

    <div class="d-flex flex-column flex-lg-row flex-grow-1 overflow-hidden">
        <div class="chart-container overflow-hidden">
            <div class="p-3 d-flex gap-2">
                <span class="badge bg-dark border border-secondary">Time: 4H</span>
                <span class="badge bg-dark border border-secondary text-success">MA(7): 41230</span>
                <span class="badge bg-dark border border-secondary text-warning">MA(25): 40500</span>
            </div>

            <div class="candle-wrapper">
                <div class="candle bullish" style="height: 60px;"></div>
                <div class="candle bullish" style="height: 90px;"></div>
                <div class="candle bearish" style="height: 120px;"></div>
                <div class="candle bearish" style="height: 40px;"></div>
                <div class="candle bullish" style="height: 150px;"></div>
                <div class="candle bullish" style="height: 110px;"></div>
                <div class="candle bearish" style="height: 80px;"></div>
                <div class="candle bullish" style="height: 130px;"></div>
            </div>
        </div>

        <div class="side-panel d-flex flex-column">
            <div class="p-3 border-bottom border-secondary overflow-auto" style="height: 50%;">
                <h6 class="small fw-bold text-muted mb-3">ORDER BOOK</h6>
                <div class="d-flex justify-content-between text-danger small opacity-75 mb-1">
                    <span>42,535.0</span><span>0.421</span>
                </div>
                <div class="d-flex justify-content-between text-danger small opacity-75 mb-1">
                    <span>42,532.5</span><span>1.205</span>
                </div>
                <div class="text-center my-2 py-1 bg-dark rounded fw-bold text-success">
                    42,530.12 <i class="ri-arrow-up-fill"></i>
                </div>
                <div class="d-flex justify-content-between text-success small opacity-75 mb-1">
                    <span>42,528.0</span><span>0.850</span>
                </div>
                <div class="d-flex justify-content-between text-success small opacity-75 mb-1">
                    <span>42,525.5</span><span>2.110</span>
                </div>
            </div>

            <div class="p-3 flex-grow-1">
                <ul class="nav nav-pills nav-justified mb-3 bg-dark rounded-pill p-1">
                    <li class="nav-item"><a class="nav-link active bg-secondary py-1 small rounded-pill" href="#">Buy</a></li>
                    <li class="nav-item"><a class="nav-link py-1 small text-white" href="#">Sell</a></li>
                </ul>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Price</label>
                    <input type="text" class="form-control form-input" value="42530.12">
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Amount</label>
                    <input type="text" class="form-control form-input" placeholder="0.00">
                </div>
                <button class="btn btn-buy w-100 py-2">Buy BTC</button>
            </div>
        </div>
    </div>

    <div class="order-panel p-3 overflow-auto">
        <h6 class="small fw-bold text-muted border-bottom border-secondary pb-2 mb-3">ASSETS & POSITIONS</h6>
        <div class="table-responsive">
            <table class="table table-dark table-hover small">
                <thead>
                    <tr class="text-muted">
                        <th>Asset</th>
                        <th>Balance</th>
                        <th>In Order</th>
                        <th>Equity (MC)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="https://cryptologos.cc/logos/bitcoin-btc-logo.png" width="16" class="me-2"> BTC</td>
                        <td>0.4502</td>
                        <td>0.1200</td>
                        <td>19,147.25</td>
                    </tr>
                    <tr>
                        <td><img src="https://cryptologos.cc/logos/ethereum-eth-logo.png" width="16" class="me-2"> ETH</td>
                        <td>4.2500</td>
                        <td>0.0000</td>
                        <td>10,625.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>