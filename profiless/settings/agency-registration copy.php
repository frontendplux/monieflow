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
            <div class="icon-box bg-purple rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; font-size: 2rem;">
                <i class="ri-briefcase-line"></i>
            </div>
            <h4 class="fw-bold mb-1">Agent Registration</h4>
            <p class="text-muted small">Join the monieFlow network as a certified agent.</p>
        </div>

        <div class="d-flex justify-content-between mb-4 position-relative px-5">
            <div class="text-center" style="z-index: 2;">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 30px; height: 30px; font-size: 0.8rem;">1</div>
                <span class="small fw-bold">KYC</span>
            </div>
            <div class="text-center" style="z-index: 2;">
                <div class="rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 30px; height: 30px; font-size: 0.8rem;">2</div>
                <span class="small">Business</span>
            </div>
            <div class="text-center" style="z-index: 2;">
                <div class="rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 30px; height: 30px; font-size: 0.8rem;">3</div>
                <span class="small">Review</span>
            </div>
            <div class="position-absolute top-50 start-0 translate-middle-y w-100 px-5" style="z-index: 1;">
                <div class="progress" style="height: 2px;">
                    <div class="progress-bar w-0" role="progressbar"></div>
                </div>
            </div>
        </div>

        <form action="/register-agent" method="POST">
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Identity Verification</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold">Government ID Type</label>
                        <select class="form-select border-0 bg-light rounded-3">
                            <option selected>National ID Card</option>
                            <option>International Passport</option>
                            <option>Driver's License</option>
                            <option>Voter's Card</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">ID Number</label>
                        <input type="text" class="form-control border-0 bg-light rounded-3" placeholder="Enter ID number">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Upload ID Photo</label>
                        <div class="border border-dashed rounded-4 p-4 text-center bg-light-subtle">
                            <i class="ri-upload-cloud-2-line fs-2 text-muted"></i>
                            <p class="small text-muted mb-2">Click to upload or drag and drop</p>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3">Choose File</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-uppercase small text-muted mb-3" style="letter-spacing: 1px;">Business Address</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">State/Province</label>
                        <input type="text" class="form-control border-0 bg-light rounded-3" placeholder="Lagos">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">City</label>
                        <input type="text" class="form-control border-0 bg-light rounded-3" placeholder="Ikeja">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Physical Address</label>
                        <textarea class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Street number and name"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-check small mb-4">
                <input class="form-check-input" type="checkbox" id="terms" required>
                <label class="form-check-label text-muted" for="terms">
                    I agree to the <a href="#" class="text-decoration-none">Agent Terms of Service</a> and confirm that all provided data is accurate.
                </label>
            </div>

            <div class="d-grid gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold" style="background-color: var(--mflow-blue);">
                    Submit Application
                </button>
                <button type="button" class="btn btn-light border-0 py-2 small">Save for Later</button>
            </div>
        </form>
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