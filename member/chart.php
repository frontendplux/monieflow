<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Token Guard
$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
    if (isset($_GET['action'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Unauthorized access.']);
        exit();
    }
    header("Location: /index.php");
    exit();
}

// Fetch Logged-in User
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
$stmt->bind_param("ss", $suid, $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_unset();
    session_destroy();
    if (isset($_GET['action'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Session expired.']);
        exit();
    }
    header("Location: /index.php");
    exit();
}

// -----------------------------------------------------------------------------
// AJAX ENDPOINT: Fetch & Fluctuate Real-Time Country Coin Value
// -----------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'get_live_rate') {
    header('Content-Type: application/json');
    $countryCode = $_GET['country_code'] ?? 'NG';

    // Fetch current value
    $cStmt = $conn->prepare("SELECT * FROM monieflow_coin_values WHERE country_code = ?");
    $cStmt->bind_param("s", $countryCode);
    $cStmt->execute();
    $coinData = $cStmt->get_result()->fetch_assoc();

    if (!$coinData) {
        echo json_encode(['status' => false, 'message' => 'Country not found.']);
        exit();
    }

    $currentAmount = floatval($coinData['amount']);
    
    // Simulate real-time currency micro-fluctuation (-0.15% to +0.15%)
    $variationPercent = (mt_rand(-150, 150) / 100000); 
    $newAmount = round($currentAmount + ($currentAmount * $variationPercent), 8);
    if ($newAmount <= 0) $newAmount = $currentAmount;

    // Update database value
    // $upStmt = $conn->prepare("UPDATE monieflow_coin_values SET amount = ? WHERE country_code = ?");
    // $upStmt->bind_param("ds", $newAmount, $countryCode);
    // $upStmt->execute();

    // Store in historical chart table
    // $insStmt = $conn->prepare("INSERT INTO monieflow_coin_chart_table (country_code_id, currency_code, amount) VALUES (?, ?, ?)");
    // $insStmt->bind_param("isd", $coinData['id'], $coinData['currency_code'], $newAmount);
    // $insStmt->execute();

    echo json_encode([
        'status' => true,
        'country' => $coinData['country'],
        'currency_code' => $coinData['currency_code'],
        'amount' => $newAmount,
        'formatted_amount' => number_format($newAmount, 4),
        'timestamp' => date('H:i:s')
    ]);
    exit();
}

// Fetch All Supported Countries for Dropdown
$countriesResult = $conn->query("SELECT * FROM monieflow_coin_values ORDER BY country ASC");
$countries = $countriesResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Live Coin Chart</title>
    
    <link rel="icon" type="image/png" href="/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg-whitesmoke: #f4f9fc;
            --brand-skyblue: #00a8e8;
            --brand-skyblue-hover: #008cc3;
            --card-white: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-whitesmoke);
            color: var(--text-dark);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        @media (min-width: 992px) { body { padding-bottom: 20px; } }

        .chart-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            padding: 1.5rem;
        }

        .pulse-badge {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-green 1.5s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #ffffff;
            border-top: 1px solid rgba(0, 168, 232, 0.15);
            z-index: 1030;
        }

        .mobile-bottom-nav .nav-link { color: var(--text-muted); font-size: 0.72rem; padding: 8px 0; text-align: center; }
        .mobile-bottom-nav .nav-link.active { color: var(--brand-skyblue); }
        .mobile-bottom-nav i { font-size: 1.25rem; display: block; }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/member/index.php">
                <img src="/logo.png" alt="MonieFlow" style="width: 40px; height: 40px; background: #008cc3; border-radius: 50%; padding: 4px;">
                <span style="color: var(--brand-skyblue-hover);">MonieFlow</span>
            </a>
            <a href="/member/index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        
        <!-- Top Title & Country Selector -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="pulse-badge"></span>
                    <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 0.5px;">Live Rate (1.5s Poll)</span>
                </div>
                <h1 class="h3 fw-bold mb-0">MonieFlow Exchange Rate</h1>
            </div>

            <!-- Select Target Country -->
            <div class="d-flex align-items-center gap-2">
                <label class="small fw-semibold text-muted text-nowrap">Select Country:</label>
                <select class="form-select rounded-pill px-3 shadow-sm" id="countrySelector">
                    <?php foreach ($countries as $c): ?>
                        <option value="<?= $c['country_code'] ?>" <?= $c['country_code'] === 'NG' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['country']) ?> (<?= $c['currency_code'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Live Price Summary Card -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="chart-card">
                    <span class="text-muted small d-block mb-1">1 MonieFlow Coin (MF) Value</span>
                    <h2 class="fw-bold text-primary mb-0" id="livePriceDisplay">--.--</h2>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="chart-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block mb-1">Target Currency</span>
                        <h4 class="fw-bold mb-0" id="liveCurrencyDisplay">NGN</h4>
                    </div>
                    <i class="bi bi-graph-up-arrow text-primary fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Chart Container -->
        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-primary"></i>Real-time Rate Fluctuations</h6>
                <small class="text-muted" id="lastUpdatedTime">Updating...</small>
            </div>
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="mfChart"></canvas>
            </div>
        </div>

    </main>

    <!-- Bottom Mobile Nav -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col"><a href="/member/index.php" class="nav-link"><i class="bi bi-house-door"></i><span>Home</span></a></div>
                <div class="col"><a href="/member/deposit.php" class="nav-link"><i class="bi bi-arrow-down-circle"></i><span>Deposit</span></a></div>
                <div class="col"><a href="/member/peer2peer.php" class="nav-link"><i class="bi bi-people"></i><span>P2P</span></a></div>
                <div class="col"><a href="/member/chart.php" class="nav-link active"><i class="bi bi-graph-up"></i><span>Chart</span></a></div>
                <div class="col"><a href="/member/settings.php" class="nav-link"><i class="bi bi-gear"></i><span>Settings</span></a></div>
            </div>
        </div>
    </div>

    <!-- Chart Logic Script -->
    <script>
        const ctx = document.getElementById('mfChart').getContext('2d');
        const countrySelector = document.getElementById('countrySelector');
        
        // Initializing Chart.js with dynamic thin line styling
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Rate Value',
                    data: [],
                    borderColor: '#00a8e8',
                    borderWidth: 1.5, // Tiny, thin line width as requested
                    pointRadius: 1,
                    pointHoverRadius: 4,
                    fill: true,
                    backgroundColor: 'rgba(0, 168, 232, 0.05)',
                    tension: 0.25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Function to fetch live rates every 1.5s
        async function fetchLiveRate() {
            const countryCode = countrySelector.value;

            try {
                const response = await fetch(`/member/chart.php?action=get_live_rate&country_code=${countryCode}`);
                const data = await response.json();

                if (data.status) {
                    document.getElementById('livePriceDisplay').innerText = `${data.currency_code} ${data.formatted_amount}`;
                    document.getElementById('liveCurrencyDisplay').innerText = `${data.currency_code} (${data.country})`;
                    document.getElementById('lastUpdatedTime').innerText = `Last updated: ${data.timestamp}`;

                    // Maintain max 20 points on chart screen
                    if (chart.data.labels.length >= 20) {
                        chart.data.labels.shift();
                        chart.data.datasets[0].data.shift();
                    }

                    chart.data.labels.push(data.timestamp);
                    chart.data.datasets[0].data.push(data.amount);
                    chart.update('none'); // Fast update without full animation freeze
                }
            } catch (err) {
                console.error("Polling error:", err);
            }
        }

        // Handle Country Selector Change
        countrySelector.addEventListener('change', () => {
            chart.data.labels = [];
            chart.data.datasets[0].data = [];
            chart.update();
            fetchLiveRate();
        });

        // Run immediately then repeat every 1.5 seconds (1500ms)
        fetchLiveRate();
        setInterval(fetchLiveRate, 1500);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>