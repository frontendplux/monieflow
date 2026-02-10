<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>monieFlow • Forex Trading</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

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

    .chart-placeholder {
      height: 320px;
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #666;
      font-size: 1.1rem;
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

    .theme-toggle {
      cursor: pointer;
      font-size: 1.4rem;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
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
    <!-- Topbar -->
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

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="card card-price shadow-sm">
          <div class="card-body">
            <p class="text-muted mb-1">Equity</p>
            <h5 class="fw-bold">$24,780.45</h5>
            <small class="price-change up"><i class="ri-arrow-up-line"></i> +2.4%</small>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card card-price shadow-sm">
          <div class="card-body">
            <p class="text-muted mb-1">Margin Level</p>
            <h5 class="fw-bold">428%</h5>
            <small class="price-change up"><i class="ri-arrow-up-line"></i> Safe</small>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card card-price shadow-sm">
          <div class="card-body">
            <p class="text-muted mb-1">Open P/L</p>
            <h5 class="fw-bold text-success">+$1,245.60</h5>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card card-price shadow-sm">
          <div class="card-body">
            <p class="text-muted mb-1">Today's Change</p>
            <h5 class="fw-bold text-danger">–$320.15</h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Watchlist + Chart -->
    <div class="row g-4">
      <!-- Live Watchlist -->
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-transparent border-bottom">
            <h6 class="mb-0">Watchlist • Majors</h6>
          </div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush" id="watchlist">
              <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                <div>EUR/USD</div>
                <div>
                  <span class="fw-bold">1.0852</span>
                  <small class="price-change up ms-2">+18 <i class="ri-arrow-up-s-line"></i></small>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                <div>GBP/USD</div>
                <div>
                  <span class="fw-bold">1.2658</span>
                  <small class="price-change down ms-2">-42 <i class="ri-arrow-down-s-line"></i></small>
                </div>
              </li>
              <!-- more pairs added via JS -->
            </ul>
          </div>
        </div>
      </div>

      <!-- Main Chart Area -->
      <div class="col-lg-8">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0">EUR/USD • H1 Chart</h6>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-secondary active">1m</button>
              <button class="btn btn-outline-secondary">5m</button>
              <button class="btn btn-outline-secondary">15m</button>
              <button class="btn btn-outline-secondary">1h</button>
              <button class="btn btn-outline-secondary">4h</button>
              <button class="btn btn-outline-secondary">1D</button>
            </div>
          </div>
          <div class="card-body p-3">
            <div class="chart-placeholder">
              <div class="text-center">
                <i class="ri-line-chart-line fs-1 mb-2 d-block"></i>
                TradingView / Lightweight Chart Placeholder<br>
                <small>(In production: integrate TradingView widget or Chart.js / Lightweight Charts)</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Trade Panel (bottom) -->
    <div class="card shadow-sm mt-4">
      <div class="card-header bg-transparent">
        <h6 class="mb-0">Quick Trade • EUR/USD</h6>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-4">
            <label class="form-label">Volume (Lots)</label>
            <input type="number" class="form-control" value="0.1" step="0.01" min="0.01">
          </div>
          <div class="col-md-4">
            <label class="form-label">Stop Loss (pips)</label>
            <input type="number" class="form-control" placeholder="50">
          </div>
          <div class="col-md-4">
            <label class="form-label">Take Profit (pips)</label>
            <input type="number" class="form-control" placeholder="100">
          </div>
        </div>
        <div class="d-flex gap-3 mt-4">
          <button class="btn btn-success flex-grow-1 py-3 fw-bold">BUY</button>
          <button class="btn btn-danger flex-grow-1 py-3 fw-bold">SELL</button>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Mobile sidebar toggle
    const sidebar = document.querySelector('.sidebar');
    document.getElementById('toggleSidebar')?.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
    document.getElementById('closeSidebar')?.addEventListener('click', () => {
      sidebar.classList.remove('show');
    });

    // Dark / Light mode
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', () => {
      const html = document.documentElement;
      const isDark = html.getAttribute('data-bs-theme') === 'dark';
      html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
      themeToggle.innerHTML = isDark ? '<i class="ri-sun-line"></i>' : '<i class="ri-moon-line"></i>';
    });

    // Fake live price updates (demo only)
    const pairs = [
      { symbol: 'EUR/USD', base: 1.0852 },
      { symbol: 'GBP/USD', base: 1.2658 },
      { symbol: 'USD/JPY', base: 151.45 },
      { symbol: 'AUD/USD', base: 0.6521 },
      { symbol: 'USD/CAD', base: 1.3687 }
    ];

    function updateWatchlist() {
      const list = document.getElementById('watchlist');
      list.innerHTML = ''; // clear

      pairs.forEach(pair => {
        const change = (Math.random() - 0.5) * 80; // -40 to +40 pips
        const direction = change > 0 ? 'up' : 'down';
        const sign = change > 0 ? '+' : '';
        const price = (pair.base + change/10000).toFixed(4);

        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center px-4 py-3';
        li.innerHTML = `
          <div>${pair.symbol}</div>
          <div>
            <span class="fw-bold">${price}</span>
            <small class="price-change ${direction} ms-2">
              ${sign}${Math.round(change)} <i class="ri-arrow-${direction === 'up' ? 'up' : 'down'}-s-line"></i>
            </small>
          </div>
        `;
        list.appendChild(li);
      });
    }

    // Initial render + fake updates every 6 seconds
    updateWatchlist();
    setInterval(updateWatchlist, 6000);
  </script>
</body>
</html>