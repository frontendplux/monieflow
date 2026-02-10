<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>monieFlow • Forex Trading Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

  <!-- Lightweight Charts (standalone - no dependencies) -->
  <script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>

  <style>
    :root {
      --sidebar-width: 260px;
      --primary: #0d6efd;
      --up: #00c853;
      --down: #d50000;
      --comment-bg: #f8f9fa;
    }

    [data-bs-theme="dark"] {
      --bs-body-bg: #0d1117;
      --bs-body-color: #c9d1d9;
      --comment-bg: #161b22;
      --up: #00e676;
      --down: #ff5252;
    }

    body {
      background: var(--bs-body-bg);
      color: var(--bs-body-color);
      font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
      min-height: 100vh;
    }

    .sidebar {
      position: fixed;
      top: 0; left: 0; bottom: 0;
      width: var(--sidebar-width);
      background: var(--bs-body-bg);
      border-right: 1px solid var(--bs-border-color);
      overflow-y: auto;
      transition: transform 0.3s ease;
      z-index: 1030;
    }

    .main-content {
      margin-left: var(--sidebar-width);
      padding: 1.25rem;
      transition: margin-left 0.3s ease;
    }

    #chart-container {
      width: 100%;
      height: 520px;
      border-radius: 12px;
      overflow: hidden;
      background: var(--bs-body-bg);
    }

    .price-live {
      font-size: 1.4rem;
      font-weight: 700;
    }

    .comment-box {
      background: var(--comment-bg);
      border-radius: 12px;
      padding: 12px 16px;
      border: none;
      resize: none;
    }

    .comment-avatar {
      width: 42px;
      height: 42px;
      object-fit: cover;
      border: 2px solid var(--bs-border-color);
    }

    .nav-link.active {
      background: rgba(13, 110, 253, 0.1);
      border-radius: 8px;
      color: var(--primary) !important;
    }

    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.show {
        transform: translateX(0);
      }
      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
      <h4 class="mb-0 fw-bold text-primary">monieFlow</h4>
      <i class="ri-close-line fs-4 d-lg-none cursor-pointer" id="closeSidebar"></i>
    </div>
    <nav class="p-3">
      <ul class="nav flex-column gap-2">
        <li><a href="#" class="nav-link active d-flex align-items-center py-2 px-3"><i class="ri-dashboard-line me-3 fs-5"></i> Dashboard</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center py-2 px-3"><i class="ri-line-chart-line me-3 fs-5"></i> Charts</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center py-2 px-3"><i class="ri-wallet-3-line me-3 fs-5"></i> Positions</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center py-2 px-3"><i class="ri-history-line me-3 fs-5"></i> Trade History</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center py-2 px-3"><i class="ri-settings-4-line me-3 fs-5"></i> Settings</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary d-lg-none p-2" id="toggleSidebar">
          <i class="ri-menu-line fs-4"></i>
        </button>
        <h4 class="mb-0 fw-semibold">EUR/USD • Live Trading</h4>
      </div>

      <div class="d-flex align-items-center gap-3">
        <select class="form-select form-select-sm" id="timeframeSelect" style="width: auto;">
          <option value="1">1m</option>
          <option value="5">5m</option>
          <option value="15">15m</option>
          <option value="60" selected>1H</option>
          <option value="240">4H</option>
          <option value="D">1D</option>
        </select>

        <div class="theme-toggle cursor-pointer fs-4" id="themeToggle">
          <i class="ri-sun-line"></i>
        </div>

        <div class="dropdown">
          <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            USD • $12,450
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Switch to EUR</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Chart -->
    <div class="card shadow border-0 mb-4">
      <div class="card-body p-0">
        <div id="chart-container"></div>
      </div>
    </div>

    <!-- Stats + Quick Trade -->
    <div class="row g-4 mb-4">
      <div class="col-lg-4">
        <div class="card shadow border-0 h-100">
          <div class="card-header bg-transparent border-bottom">
            <h6 class="mb-0">Account Overview</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <small class="text-muted">Equity</small>
              <h5 class="fw-bold">$24,780.45</h5>
            </div>
            <div class="mb-3">
              <small class="text-muted">Open P/L</small>
              <h5 class="text-success fw-bold">+$1,245.60</h5>
            </div>
            <div>
              <small class="text-muted">Margin Level</small>
              <h5 class="fw-bold text-info">428%</h5>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card shadow border-0 h-100">
          <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Quick Trade • EUR/USD</h6>
            <span class="price-live" id="livePrice">1.08520</span>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small">Volume (Lots)</label>
                <input type="number" class="form-control" value="0.10" step="0.01" min="0.01">
              </div>
              <div class="col-md-4">
                <label class="form-label small">Stop Loss (pips)</label>
                <input type="number" class="form-control" placeholder="45">
              </div>
              <div class="col-md-4">
                <label class="form-label small">Take Profit (pips)</label>
                <input type="number" class="form-control" placeholder="120">
              </div>
            </div>
            <div class="d-flex gap-3 mt-4">
              <button class="btn btn-success flex-grow-1 py-3 fw-bold fs-5">BUY</button>
              <button class="btn btn-danger flex-grow-1 py-3 fw-bold fs-5">SELL</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Comments Section -->
    <div class="card shadow border-0">
      <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Community Signals & Analysis</h6>
        <button class="btn btn-sm btn-primary">Post Signal</button>
      </div>
      <div class="card-body">

        <!-- New Comment Form -->
        <div class="d-flex mb-4">
          <img src="https://i.pravatar.cc/150?u=user" class="rounded-circle me-3 comment-avatar" alt="You">
          <div class="flex-grow-1">
            <textarea class="form-control comment-box mb-2" rows="2" placeholder="Share your trade idea or analysis..."></textarea>
            <div class="d-flex justify-content-end gap-2">
              <button class="btn btn-sm btn-outline-secondary"><i class="ri-image-line"></i></button>
              <button class="btn btn-sm btn-primary">Post</button>
            </div>
          </div>
        </div>

        <!-- Example Comments -->
        <div class="border-bottom pb-4 mb-4">
          <div class="d-flex">
            <img src="https://i.pravatar.cc/150?u=traderpro" class="rounded-circle me-3 comment-avatar" alt="">
            <div class="flex-grow-1">
              <strong>EliteTraderNG</strong> <small class="text-muted">• 1h ago</small>
              <div class="mt-1">
                Clear bullish engulfing on H1 + volume spike. Long from 1.0845, TP1 1.0890, TP2 1.0935. SL 1.0810. RR 1:2.8
              </div>
              <div class="d-flex gap-4 mt-2 small text-muted">
                <span><i class="ri-thumb-up-line me-1"></i>62</span>
                <span><i class="ri-chat-3-line me-1"></i>Reply</span>
              </div>
            </div>
          </div>
        </div>

        <div class="border-bottom pb-4 mb-4">
          <div class="d-flex">
            <img src="https://i.pravatar.cc/150?u=fxqueen" class="rounded-circle me-3 comment-avatar" alt="">
            <div class="flex-grow-1">
              <strong>FXQueenLagos</strong> <small class="text-muted">• 38min ago</small>
              <div class="mt-1">
                NFP in ~5 hours. Expecting choppy range 1.0820–1.0880 until then. Sitting out this one.
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ────────────────────────────────────────────────
    // Mobile Sidebar
    // ────────────────────────────────────────────────
    const sidebar = document.querySelector('.sidebar');
    document.getElementById('toggleSidebar')?.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
    document.getElementById('closeSidebar')?.addEventListener('click', () => {
      sidebar.classList.remove('show');
    });

    // ────────────────────────────────────────────────
    // Theme Toggle
    // ────────────────────────────────────────────────
    document.getElementById('themeToggle').addEventListener('click', function() {
      const html = document.documentElement;
      const current = html.getAttribute('data-bs-theme');
      html.setAttribute('data-bs-theme', current === 'dark' ? 'light' : 'dark');
      this.querySelector('i').className = current === 'dark' ? 'ri-sun-line' : 'ri-moon-line';
    });

    // ────────────────────────────────────────────────
    // LIVE CANDLESTICK CHART
    // ────────────────────────────────────────────────
    const chartContainer = document.getElementById('chart-container');
    const chart = LightweightCharts.createChart(chartContainer, {
      width: chartContainer.clientWidth,
      height: 520,
      layout: {
        background: { type: 'solid', color: 'transparent' },
        textColor: '#d1d4dc',
      },
      grid: {
        vertLines: { color: '#2b2f36' },
        horzLines: { color: '#2b2f36' },
      },
      crosshair: {
        mode: LightweightCharts.CrosshairMode.Normal,
        vertLine: { labelVisible: true, color: '#758696' },
        horzLine: { labelVisible: true, color: '#758696' },
      },
      rightPriceScale: { borderColor: '#2b2f36' },
      timeScale: {
        borderColor: '#2b2f36',
        timeVisible: true,
        secondsVisible: false,
      },
    });

    const candleSeries = chart.addCandlestickSeries({
      upColor: '#26a69a',
      downColor: '#ef5350',
      borderVisible: false,
      wickUpColor: '#26a69a',
      wickDownColor: '#ef5350',
    });

    let candles = [];
    let currentCandle = null;
    let lastTime = Math.floor(Date.now() / 1000) - (3600 * 60); // ~60 hours ago

    function initCandles() {
      candles = [];
      let price = 1.08450;

      for (let i = 0; i < 60; i++) {
        const open = price;
        const close = open + (Math.random() - 0.5) * 0.0035;
        const high = Math.max(open, close) + Math.random() * 0.002;
        const low = Math.min(open, close) - Math.random() * 0.002;

        candles.push({ time: lastTime, open, high, low, close });
        price = close;
        lastTime += 3600; // 1 hour
      }

      candleSeries.setData(candles);
      currentCandle = { ...candles[candles.length - 1] };
    }

    function updateLiveCandle() {
      if (!currentCandle) return;

      const tick = (Math.random() - 0.5) * 0.0007;
      currentCandle.close += tick;
      currentCandle.high = Math.max(currentCandle.high, currentCandle.close);
      currentCandle.low  = Math.min(currentCandle.low,  currentCandle.close);

      candleSeries.update(currentCandle);

      // Update header live price
      const liveEl = document.getElementById('livePrice');
      liveEl.textContent = currentCandle.close.toFixed(5);
      liveEl.style.color = currentCandle.close >= currentCandle.open ? 'var(--up)' : 'var(--down)';
    }

    function createNewCandle() {
      const newTime = lastTime + 3600;
      lastTime = newTime;

      const open = currentCandle.close;
      const close = open + (Math.random() - 0.5) * 0.003;
      const high = Math.max(open, close) + Math.random() * 0.0018;
      const low  = Math.min(open, close) - Math.random() * 0.0018;

      currentCandle = { time: newTime, open, high, low, close };
      candleSeries.update(currentCandle);

      chart.timeScale().scrollToPosition(0, true);
    }

    // Initialize & start live updates
    initCandles();
    setInterval(updateLiveCandle, 3200);     // price tick ~every 3.2s
    setInterval(createNewCandle, 22000);     // new candle ~every 22s (demo speed)

    // Resize chart
    window.addEventListener('resize', () => {
      chart.resize(chartContainer.clientWidth, 520);
    });

    // Optional: fit on load
    chart.timeScale().fitContent();
  </script>
</body>
</html>