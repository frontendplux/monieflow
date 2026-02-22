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
        <div class="text-center mb-4">
            <div class="icon-box bg-blue rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; font-size: 2rem;">
                <i class="ri-customer-service-2-line"></i>
            </div>
            <h4 class="fw-bold mb-1">How can we help?</h4>
            <p class="text-muted small">Search our knowledge base or contact support.</p>
        </div>

        <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border">
            <span class="input-group-text bg-white border-0 ps-3">
                <i class="ri-search-line text-muted"></i>
            </span>
            <input type="text" class="form-control border-0 py-2" placeholder="Search for articles (e.g. payouts, verification)...">
        </div>

        <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Top Categories</h6>
        <div class="row g-3 mb-4">
            <div class="col-6">
                <a href="#" class="card border text-center p-3 text-decoration-none hover-shadow transition">
                    <i class="ri-bank-card-line fs-3 text-warning mb-2"></i>
                    <div class="small fw-bold text-dark">Payments</div>
                </a>
            </div>
            <div class="col-6">
                <a href="#" class="card border text-center p-3 text-decoration-none">
                    <i class="ri-shield-user-line fs-3 text-primary mb-2"></i>
                    <div class="small fw-bold text-dark">Account</div>
                </a>
            </div>
            <div class="col-6">
                <a href="#" class="card border text-center p-3 text-decoration-none">
                    <i class="ri-vip-crown-line fs-3 text-success mb-2"></i>
                    <div class="small fw-bold text-dark">Creator Lab</div>
                </a>
            </div>
            <div class="col-6">
                <a href="#" class="card border text-center p-3 text-decoration-none">
                    <i class="ri-flag-line fs-3 text-danger mb-2"></i>
                    <div class="small fw-bold text-dark">Safety</div>
                </a>
            </div>
        </div>

        <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Popular Questions</h6>
        <div class="accordion accordion-flush border rounded-4 overflow-hidden" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed small fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        When do I get my payouts?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Payouts are processed every 14 days once you reach the minimum threshold of $50. You can track status in Payout Methods.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed small fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        How to verify my ID?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Go to Settings > Security and upload a clear photo of your Government issued ID. Review takes 24-48 hours.
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 p-4 rounded-4 bg-light border-0 d-flex align-items-center">
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">Still need help?</h6>
                <p class="small text-muted mb-0">Our team typically replies in 2 hours.</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background-color: var(--mflow-blue);">Chat Now</button>
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