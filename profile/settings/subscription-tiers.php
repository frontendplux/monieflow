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
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-bold mb-1">Subscription Tiers</h4>
                <p class="text-muted small mb-0">Manage your membership levels and exclusive fan perks.</p>
            </div>
            <button class="btn btn-primary rounded-pill btn-sm fw-bold px-3">
                <i class="ri-add-line me-1"></i> New Tier
            </button>
        </div>

        <div class="card border rounded-4 mb-3 overflow-hidden">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-blue rounded-pill px-3">Tier 1</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h5 class="fw-bold mb-1">Bronze Fan</h5>
                        <p class="small text-muted mb-2">Basic access to feed and stories.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark border fw-normal"><i class="ri-check-line text-success me-1"></i>Ad-free viewing</span>
                            <span class="badge bg-light text-dark border fw-normal"><i class="ri-check-line text-success me-1"></i>Early access</span>
                        </div>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <div class="h4 fw-bold mb-0">$4.99</div>
                        <div class="small text-muted">per month</div>
                        <button class="btn btn-link btn-sm text-decoration-none p-0 mt-2">Edit Tier</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-primary border-2 rounded-4 mb-3 overflow-hidden position-relative" style="background: #f0f7ff;">
            <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-primary rounded-pill">Most Popular</span>
            </div>
            <div class="card-body p-3">
                <span class="badge bg-gold text-dark rounded-pill px-3 mb-2">Tier 2</span>
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h5 class="fw-bold mb-1">Gold Supporter</h5>
                        <p class="small text-muted mb-2">Exclusive content and direct interaction.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-white text-dark border fw-normal"><i class="ri-check-line text-success me-1"></i>Private DM access</span>
                            <span class="badge bg-white text-dark border fw-normal"><i class="ri-check-line text-success me-1"></i>Exclusive Reels</span>
                            <span class="badge bg-white text-dark border fw-normal"><i class="ri-check-line text-success me-1"></i>Badges</span>
                        </div>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <div class="h4 fw-bold mb-0 text-primary">$14.99</div>
                        <div class="small text-muted">per month</div>
                        <button class="btn btn-link btn-sm text-decoration-none p-0 mt-2">Edit Tier</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border border-dashed rounded-4 mb-4 opacity-75" style="border-style: dashed !important;">
            <div class="card-body p-3 text-center py-4">
                <div class="icon-box bg-light mx-auto mb-2"><i class="ri-vip-diamond-line text-muted"></i></div>
                <h6 class="fw-bold mb-1">VIP Elite</h6>
                <p class="small text-muted">Tier currently in draft mode</p>
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-4">Complete Setup</button>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top">
            <h6 class="fw-bold mb-3">Subscription Settings</h6>
            <div class="list-group list-group-flush border rounded-4 overflow-hidden">
                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold small">Annual Discount</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Offer 2 months free for yearly subs</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold small">Public Earnings</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Show total subscribers on your profile</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </div>
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