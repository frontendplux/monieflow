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
            <div class="group-title">Account & Profile</div>
            <div class="settings-group shadow-sm">
                <a href="/profile/settings/edit-profile.php" class="settings-item"> 
                    <div class="icon-box bg-blue"><i class="ri-user-settings-line"></i></div>
                    <div class="settings-label">Edit Profile</div>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
                <a href="/profile/settings/notification.php" class="settings-item">
                    <div class="icon-box bg-purple"><i class="ri-notification-3-line"></i></div>
                    <div class="settings-label">Notifications</div>
                    <span class="settings-meta">On</span>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
            </div>

            <div class="group-title">Monetization</div>
            <div class="settings-group shadow-sm">
                <a href="/profile/settings/payout-mode.php" class="settings-item">
                    <div class="icon-box bg-gold"><i class="ri-bank-card-line"></i></div>
                    <div class="settings-label">Payout Methods</div>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
                <a href="/profile/settings/subscription-tiers.php" class="settings-item">
                    <div class="icon-box bg-green"><i class="ri-vip-crown-line"></i></div>
                    <div class="settings-label">Subscription Tiers</div>
                    <span class="settings-meta">2 Active</span>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
                <a href="/profile/settings/agency-registration.php" class="settings-item active">
                    <div class="icon-box bg-purple"><i class="ri-briefcase-line"></i></div>
                    <div class="settings-label">Agency Center</div>
                    <span class="settings-meta text-primary">Reviewing</span>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
            </div>

            <div class="group-title">Security</div>
            <div class="settings-group shadow-sm">
                <a href="/profile/settings/privacy.php" class="settings-item">
                    <div class="icon-box bg-blue"><i class="ri-lock-2-line"></i></div>
                    <div class="settings-label">Privacy Center</div>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
                <a href="/profile/settings/2-factor-auth.php" class="settings-item">
                    <div class="icon-box bg-red"><i class="ri-shield-keyhole-line"></i></div>
                    <div class="settings-label">Two-Factor Auth</div>
                    <span class="settings-meta text-danger">Disabled</span>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
            </div>

            <div class="group-title">More</div>
            <div class="settings-group shadow-sm">
                <a href="/profile/settings/help-center.php" class="settings-item">
                    <div class="icon-box bg-blue"><i class="ri-question-line"></i></div>
                    <div class="settings-label">Help Center</div>
                    <i class="ri-arrow-right-s-line text-muted"></i>
                </a>
                <a href="/logout" class="settings-item logout-btn">
                    <div class="icon-box bg-red"><i class="ri-logout-box-r-line"></i></div>
                    <div class="settings-label">Log Out</div>
                </a>
            </div>
            
            <div class="text-center mt-3 d-none d-md-block">
                <small class="text-muted">monieFlow v2.4.0 (2026)</small>
            </div>
        </div>

        <div class="col-lg-8 col-md-7" id="checkers">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    
                    <div class="text-center mb-4">
                        <div class="icon-box bg-purple rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; font-size: 2rem;">
                            <i class="ri-briefcase-line"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Agency Business Portal</h4>
                        <p class="text-muted small">Manage your legal business information and application status.</p>
                    </div>

                    <div class="alert alert-light border rounded-4 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold small mb-0 text-uppercase" style="letter-spacing: 1px;">Application Status</h6>
                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3">Under Review</span>
                        </div>
                        <div class="d-flex align-items-center mt-3">
                            <div class="flex-grow-1 pe-3">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 66%"></div>
                                </div>
                                <p class="text-muted mb-0 mt-2" style="font-size: 0.75rem;">Step 2 of 3: Compliance Team Review</p>
                            </div>
                            <i class="ri-time-line fs-3 text-muted"></i>
                        </div>
                    </div>

                    <form action="/update-agency" method="POST">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Business Registration</h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label small fw-bold">Legal Business Name</label>
                                <input type="text" class="form-control border-0 bg-light rounded-3" placeholder="e.g. MonieFlow Global Agents Ltd">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Business RC Number</label>
                                <input type="text" class="form-control border-0 bg-light rounded-3" placeholder="RC-7722110">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Company Tax ID (TIN)</label>
                                <input type="text" class="form-control border-0 bg-light rounded-3" placeholder="TIN-092283">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Upload Business License</label>
                                <div class="border border-dashed rounded-4 p-3 text-center bg-light-subtle">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="ri-file-pdf-line fs-4 me-2 text-primary"></i>
                                        <span class="small fw-bold">certificate_of_inc.pdf</span>
                                        <button type="button" class="btn btn-link text-danger btn-sm text-decoration-none ms-2">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Compliance Checklist</h6>
                        <div class="list-group list-group-flush border rounded-4 overflow-hidden mb-4">
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <i class="ri-checkbox-circle-fill text-success me-3 fs-5"></i>
                                    <div class="small fw-bold">Contact Details Verified</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light-subtle">
                                <div class="d-flex align-items-center">
                                    <i class="ri-refresh-line text-primary me-3 fs-5"></i>
                                    <div class="small fw-bold">Document Authentication</div>
                                </div>
                                <span class="badge bg-primary-subtle text-primary rounded-pill">In Progress</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="ri-checkbox-blank-circle-line me-3 fs-5"></i>
                                    <div class="small fw-bold">Final Approval</div>
                                </div>
                                <span class="badge bg-light text-muted border rounded-pill">Pending</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold" style="background-color: var(--mflow-blue);">
                                Update Business Details
                            </button>
                            <p class="text-center text-muted small mt-2">
                                <i class="ri-information-line me-1"></i> Editing details while under review may reset your queue position.
                            </p>
                        </div>
                    </form>
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