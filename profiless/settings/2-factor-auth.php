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
            <h4 class="fw-bold mb-0">Two-Factor Authentication</h4>
            <span class="badge bg-danger-subtle text-danger ms-2 rounded-pill">Recommended</span>
        </div>
        <p class="text-muted small mb-4">Add an extra layer of security to your monieFlow account by requiring a code in addition to your password.</p>

        <div class="alert alert-light border rounded-4 d-flex align-items-center p-3 mb-4">
            <div class="icon-box bg-red rounded-circle me-3">
                <i class="ri-shield-flash-line"></i>
            </div>
            <div>
                <div class="fw-bold small">2FA is currently OFF</div>
                <div class="text-muted" style="font-size: 0.75rem;">Your account is at higher risk of unauthorized access.</div>
            </div>
        </div>

        <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Security Methods</h6>
        <div class="list-group list-group-flush border rounded-4 overflow-hidden mb-4">
            
            <div class="list-group-item d-flex align-items-center py-3">
                <div class="icon-box bg-blue rounded-circle me-3">
                    <i class="ri-apps-2-line"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">Authenticator App</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Use Google Authenticator or Authy</div>
                </div>
                <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" style="background-color: var(--mflow-blue);">Setup</button>
            </div>

            <div class="list-group-item d-flex align-items-center py-3">
                <div class="icon-box bg-green rounded-circle me-3">
                    <i class="ri-message-3-line"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">Text Message (SMS)</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Receive a code on your phone</div>
                </div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3">Enable</button>
            </div>

            <div class="list-group-item d-flex align-items-center py-3 opacity-50">
                <div class="icon-box bg-light text-dark rounded-circle me-3">
                    <i class="ri-file-list-3-line"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">Backup Codes</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Setup 2FA first to see backup codes</div>
                </div>
                <i class="ri-lock-line text-muted"></i>
            </div>
        </div>

        <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Trusted Devices</h6>
        <div class="list-group list-group-flush border rounded-4 overflow-hidden">
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center">
                    <i class="ri-smartphone-line fs-4 me-3 text-muted"></i>
                    <div>
                        <div class="fw-bold small">iPhone 15 Pro • Lagos, NG</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Current Device • Active now</div>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success rounded-pill px-2">This Device</span>
            </div>
            <a href="#" class="list-group-item list-group-item-action text-center py-2 small text-primary fw-bold">
                Log out from all other devices
            </a>
        </div>

        <div class="mt-4 text-center">
            <p class="text-muted small">Having trouble? <a href="/help" class="text-decoration-none">Contact Security Support</a></p>
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