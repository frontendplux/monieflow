<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-red: #ff4d6d;
            --bg-light: #f7f8fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1a1a1a;
        }

        .settings-group {
            background: #fff;
            border-radius: 20px;
            margin-bottom: 25px;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .settings-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s;
            border-bottom: 1px solid #f8f9fa;
        }

        .settings-item:last-child { border-bottom: none; }
        .settings-item:hover { background: #f8f9fa; color: inherit; }
        .settings-item.active { background: #eef5ff; border-left: 4px solid var(--mflow-blue); }

        .icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        /* Icon Colors */
        .bg-blue { background: #eef5ff; color: var(--mflow-blue); }
        .bg-gold { background: #fff9e6; color: #ffcc00; }
        .bg-purple { background: #f5eeff; color: #7b2ff7; }
        .bg-green { background: #e6f7ed; color: #198754; }
        .bg-red { background: #fff0f3; color: var(--mflow-red); }

        .settings-label { flex-grow: 1; font-weight: 500; font-size: 0.95rem; }
        .settings-meta { font-size: 0.85rem; color: #888; margin-right: 5px; }

        .group-title {
            padding-left: 20px;
            margin-bottom: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logout-btn { color: var(--mflow-red) !important; font-weight: 600; }
        
        /* Form Styling */
        .card { border-radius: 20px; }
        .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
        .list-group-item { border-color: #f1f1f1; }
    </style>
</head>
<body>

<?php include __DIR__."/c-header.php"; ?>

<main class="container py-4">
    <div class="row">
        
        <div class="col-lg-4 col-md-5 settings-sidebar d-none d-md-block">
            <?php include __DIR__."/c-sidebar.php"; ?>
        </div>

        <div class="col-lg-8 col-md-7" id="checkers">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-1">
            <h4 class="fw-bold mb-0">Payout Methods</h4>
            <span class="badge bg-success-subtle text-success ms-2 rounded-pill">Secure</span>
        </div>
        <p class="text-muted small mb-4">Manage how you receive your earnings and subscriptions.</p>

        <div class="p-3 mb-4 rounded-4 text-white d-flex justify-content-between align-items-center" 
             style="background: linear-gradient(135deg, var(--mflow-blue), #0056b3);">
            <div>
                <div class="small opacity-75">Available for Payout</div>
                <h3 class="fw-bold mb-0">$1,240.50</h3>
            </div>
            <button class="btn btn-light btn-sm rounded-pill fw-bold px-3">Withdraw Now</button>
        </div>

        <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Saved Methods</h6>
        
        <div class="d-flex align-items-center p-3 border rounded-4 mb-3">
            <div class="icon-box bg-blue rounded-circle">
                <i class="ri-bank-line"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold mb-0">Chase Bank •••• 8842</div>
                <div class="small text-muted">Primary Payout Method</div>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                    <i class="ri-more-2-fill fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Edit Details</a></li>
                    <li><a class="dropdown-item text-danger" href="#">Remove</a></li>
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-center p-3 border rounded-4 mb-4">
            <div class="icon-box bg-gold rounded-circle">
                <i class="ri-paypal-line"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold mb-0">paypal.me/johndoe</div>
                <div class="small text-muted">Connected 2 months ago</div>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                    <i class="ri-more-2-fill fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="#">Set as Primary</a></li>
                    <li><a class="dropdown-item text-danger" href="#">Disconnect</a></li>
                </ul>
            </div>
        </div>

        <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Add New Method</h6>
        <div class="list-group list-group-flush border rounded-4 overflow-hidden">
            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                <div class="icon-box bg-light text-dark"><i class="ri-add-line"></i></div>
                <div class="settings-label">Bank Account (ACH/Swift)</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                <div class="icon-box bg-light text-dark"><i class="ri-wallet-3-line"></i></div>
                <div class="settings-label">Crypto Wallet (USDC/SOL)</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
        </div>

        <div class="mt-5 pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Recent Payouts</h6>
                <a href="#" class="small text-decoration-none">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <tbody class="small">
                        <tr>
                            <td class="ps-0 text-muted">Feb 10, 2026</td>
                            <td class="fw-bold">$450.00</td>
                            <td><span class="badge bg-success-subtle text-success rounded-pill">Completed</span></td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-muted">Jan 15, 2026</td>
                            <td class="fw-bold">$890.00</td>
                            <td><span class="badge bg-success-subtle text-success rounded-pill">Completed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
            </div>
            
            <div class="text-center mt-4 d-md-none pb-4">
                <small class="text-muted">monieFlow v2.4.0 (2026)</small>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>