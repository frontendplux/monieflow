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
            <h4 class="fw-bold mb-0">Privacy Center</h4>
            <span class="badge bg-info-subtle text-info ms-2 rounded-pill">Secure</span>
        </div>
        <p class="text-muted small mb-4">Control who sees your content and how your data is used.</p>

        <div class="mb-4">
            <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Account Visibility</h6>
            <div class="list-group list-group-flush border rounded-4 overflow-hidden">
                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">Private Account</div>
                        <div class="small text-muted">Only followers can see your posts and reels</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch">
                    </div>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">Show Online Status</div>
                        <div class="small text-muted">Allow others to see when you are active</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" checked>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Interactions</h6>
            <div class="list-group list-group-flush border rounded-4 overflow-hidden">
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">Comments</div>
                        <div class="small text-muted">Everyone</div>
                    </div>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">Tags & Mentions</div>
                        <div class="small text-muted">Followers Only</div>
                    </div>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">Blocked Accounts</div>
                        <div class="small text-muted">12 users</div>
                    </div>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Your Data</h6>
            <div class="list-group list-group-flush border rounded-4 overflow-hidden">
                <button type="button" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                    <div class="icon-box bg-blue me-3"><i class="ri-download-2-line"></i></div>
                    <div class="settings-label">Download Your Information</div>
                </button>
                <button type="button" class="list-group-item list-group-item-action d-flex align-items-center py-3 text-danger">
                    <div class="icon-box bg-red me-3"><i class="ri-delete-bin-line"></i></div>
                    <div class="settings-label fw-bold">Delete Account</div>
                </button>
            </div>
        </div>

        <div class="alert alert-warning rounded-4 border-0 small mb-0 mt-2">
            <div class="d-flex">
                <i class="ri-information-line fs-5 me-2"></i>
                <div>
                    Deactivating or deleting your account will permanently remove your monetization progress.
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