<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>monieFlow • Forex Trading with Candlestick Chart</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
  <!-- Lightweight Charts CDN -->
  <script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>

  <style>
    :root {
      --sidebar-width: 260px;
      --primary: #0d6efd;
      --up: #28a745;
      --down: #dc3545;
    }

    body {
      background: #f8f9fa;
      font-family: system-ui, -apple-system, sans-serif;
      min-height: 100vh;
    }

    [data-bs-theme="dark"] {
      --bs-body-bg: #0d1117;
      --bs-body-color: #c9d1d9;
      background: #0d1117;
    }

    .sidebar {
      position: fixed;
      top: 0; left: 0; bottom: 0;
      width: var(--sidebar-width);
      background: var(--bs-body-bg);
      border-right: 1px solid var(--bs-border-color);
      overflow-y: auto;
      transition: transform 0.3s;
      z-index: 1030;
    }

    .main-content {
      margin-left: var(--sidebar-width);
      padding: 1rem;
      transition: margin-left 0.3s;
    }

    .card-price {
      transition: transform 0.15s;
    }
    .card-price:hover {
      transform: translateY(-3px);
    }

    .price-change.up { color: var(--up); }
    .price-change.down { color: var(--down); }

    #chartContainer {
      width: 100%;
      height: 400px;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid var(--bs-border-color);
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
      #chartContainer {
        height: 320px;
      }
    }

    .theme-toggle {
      cursor: pointer;
      font-size: 1.4rem;
    }
  </style>
</head>
<body>

  <!-- Sidebar (same as before) -->
  <aside class="sidebar">
    <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
      <h4 class="mb-0 fw-bold text-primary">monieFlow</h4>
      <i class="ri-close-line fs-4 d-lg-none" id="closeSidebar"></i>
    </div>

    <nav class="p-3">
      <ul class="nav flex-column gap-2">
        <li><a href="#" class="nav-link active d-flex align-items-center"><i class="ri-dashboard-line me-3"></i> Dashboard</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center"><i class="ri-currency-line me-3"></i> Major Pairs</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center"><i class="ri-line-chart-line me-3"></i> Charts</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center"><i class="ri-wallet-3-line me-3"></i> Positions</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center"><i class="ri-history-line me-3"></i> Trade History</a></li>
        <li><a href="#" class="nav-link d-flex align-items-center"><i class="ri-settings-4-line me-3"></i> Settings</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <!-- Topbar (same as before) -->
    <header class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary d-lg-none" id="toggleSidebar">
          <i class="ri-menu-line fs-4"></i>
        </button>
        <h4 class="mb-0">Forex Dashboard</h4>
      </div>

      <div class="d-flex align-items-center gap-3">
        <div class="input-group" style="max-width: 240px;">
          <input type="text" class="form-control" placeholder="Search pair...">
          <button class="btn btn-outline-secondary"><i class="ri-search-line"></i></button>
        </div>
        <div class="theme-toggle" id="themeToggle">
          <i class="ri-sun-line"></i>
        </div>
        <div class="dropdown">
          <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            USD Account
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">USD • $12,450.80</a></li>
            <li><a class="dropdown-item" href="#">EUR • €8,920.50</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Stats Row (same as before) -->
    <div class="row g-3 mb-4">
      <!-- ... (equity, margin, p/l cards) ... -->
    </div>

    <!-- Watchlist + Candlestick Chart -->
    <div class="row g-4">
      <!-- Live Watchlist (same as before) -->
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <!-- ... -->
        </div>
      </div>

      <!-- Candlestick Chart Area -->
      <div class="col-lg-8">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0">EUR/USD • H1 Candlestick Chart</h6>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-secondary active">1m</button>
              <button class="btn btn-outline-secondary">5m</button>
              <button class="btn btn-outline-secondary">15m</button>
              <button class="btn btn-outline-secondary">1h</button>
              <button class="btn btn-outline-secondary">4h</button>
              <button class="btn btn-outline-secondary">1D</button>
            </div>
          </div>
          <div class="card-body p-0">
            <div id="chartContainer"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Comments Section -->
    <div class="card shadow-sm mt-4">
      <div class="card-header bg-transparent border-bottom">
        <h6 class="mb-0">Chart Comments & Analysis</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <textarea class="form-control" rows="3" placeholder="Add your analysis or comment here..."></textarea>
          <button class="btn btn-primary mt-2">Post Comment</button>
        </div>

        <!-- Sample Comments -->
        <div class="comment mb-3 border-bottom pb-3">
          <div class="d-flex align-items-center mb-2">
            <img src="https://i.pravatar.cc/40?u=analyst1" class="rounded-circle me-2" width="32" height="32">
            <strong>AnalystJane</strong> <small class="text-muted ms-2">2h ago</small>
          </div>
          <p>Strong bullish engulfing candle on H1. Targeting 1.0900 resistance. SL below recent low.</p>
        </div>

        <div class="comment mb-3 border-bottom pb-3">
          <div class="d-flex align-items-center mb-2">
            <img src="https://i.pravatar.cc/40?u=trader2" class="rounded-circle me-2" width="32" height="32">
            <strong>ForexPro</strong> <small class="text-muted ms-2">45m ago</small>
          </div>
          <p>Watch for breakout above 1.0860. RSI showing overbought, possible pullback first.</p>
        </div>

        <!-- Add more dynamically via JS/backend -->
      </div>
    </div>

    <!-- Quick Trade Panel (same as before) -->
    <!-- ... -->
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ... (mobile sidebar, theme toggle, watchlist updates same as before) ...

    // Candlestick Chart Setup with Lightweight Charts
    const chartContainer = document.getElementById('chartContainer');
    const chart = LightweightCharts.createChart(chartContainer, {
      layout: {
        background: { type: 'solid', color: 'transparent' },
        textColor: '#333',
      },
      grid: {
        vertLines: { color: '#e0e0e0' },
        horzLines: { color: '#e0e0e0' },
      },
      width: chartContainer.clientWidth,
      height: chartContainer.clientHeight,
      timeScale: { timeVisible: true, secondsVisible: false },
    });

    const candlestickSeries = chart.addCandlestickSeries({
      upColor: '#26a69a',
      downColor: '#ef5350',
      borderVisible: false,
      wickUpColor: '#26a69a',
      wickDownColor: '#ef5350',
    });

    // Sample OHLC data (time in UNIX seconds, replace with real API data)
    const candleData = [
      { time: 1669852800, open: 1.0800, high: 1.0825, low: 1.0790, close: 1.0815 },
      { time: 1669856400, open: 1.0815, high: 1.0830, low: 1.0805, close: 1.0828 },
      { time: 1669860000, open: 1.0828, high: 1.0842, low: 1.0810, close: 1.0835 },
      { time: 1669863600, open: 1.0835, high: 1.0850, low: 1.0820, close: 1.0842 },
      { time: 1669867200, open: 1.0842, high: 1.0865, low: 1.0830, close: 1.0858 },
      // Add 50-100 more candles for realism (fetch from API like Alpha Vantage)
    ];

    candlestickSeries.setData(candleData);

    // Resize handler
    window.addEventListener('resize', () => {
      chart.applyOptions({
        width: chartContainer.clientWidth,
        height: chartContainer.clientHeight,
      });
    });

    // Fake live update (append new candle every 30s)
    setInterval(() => {
      const lastCandle = candleData[candleData.length - 1];
      const newTime = lastCandle.time + 3600; // +1 hour
      const fluctuation = (Math.random() - 0.5) * 0.005;
      const newClose = lastCandle.close + fluctuation;
      const newHigh = Math.max(lastCandle.close, newClose) + Math.random() * 0.001;
      const newLow = Math.min(lastCandle.close, newClose) - Math.random() * 0.001;

      candlestickSeries.update({
        time: newTime,
        open: lastCandle.close,
        high: newHigh,
        low: newLow,
        close: newClose,
      });

      candleData.push({ time: newTime, open: lastCandle.close, high: newHigh, low: newLow, close: newClose });
    }, 30000);

    // Dark mode chart theme sync
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', () => {
      const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
      chart.applyOptions({
        layout: {
          background: { type: 'solid', color: isDark ? '#0d1117' : '#ffffff' },
          textColor: isDark ? '#c9d1d9' : '#333',
        },
        grid: {
          vertLines: { color: isDark ? '#21262d' : '#e0e0e0' },
          horzLines: { color: isDark ? '#21262d' : '#e0e0e0' },
        },
      });
    });
  </script>
</body>
</html>