<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Smart Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --form-primary: #4f46e5;
            --form-bg: #f8fafc;
        }

        body { 
            background-color: var(--form-bg); 
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        /* Split Container */
        .form-wrapper {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            width: 100%;
            margin: auto;
        }

        /* Sidebar info */
        .info-side {
            background: var(--form-primary);
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Input Styling */
        .form-side { padding: 50px; }
        
        .custom-input {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s;
            font-size: 15px;
        }

        .custom-input:focus {
            border-color: var(--form-primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .input-group-text {
            background: transparent;
            border: 2px solid #e2e8f0;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #94a3b8;
        }

        .input-group .custom-input { border-left: none; border-radius: 0 12px 12px 0; }

        .btn-submit {
            background: var(--form-primary);
            color: white;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            transition: 0.3s;
        }

        .btn-submit:hover { transform: translateY(-2px); filter: brightness(1.1); }

        /* Progress Steps */
        .step-item { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; opacity: 0.6; }
        .step-item.active { opacity: 1; }
        .step-number {
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 14px;
        }
        .active .step-number { background: white; color: var(--form-primary); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="form-wrapper row g-0">
        
        <div class="col-lg-5 info-side d-none d-lg-flex">
            <h2 class="fw-bold mb-4">Join the Network</h2>
            <p class="mb-5 text-white-50">Setup your account in less than 2 minutes and start trading instantly.</p>
            
            <div class="step-item active">
                <div class="step-number">1</div>
                <div><h6 class="mb-0">Personal Info</h6><small class="text-white-50">Basic details</small></div>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <div><h6 class="mb-0">Security</h6><small class="text-white-50">Password & Auth</small></div>
            </div>
            <div class="step-item">
                <div class="step-number">3</div>
                <div><h6 class="mb-0">Verification</h6><small class="text-white-50">Identity check</small></div>
            </div>
        </div>

        <div class="col-lg-7 form-side">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h4 class="fw-bold mb-0">Create Account</h4>
                <a href="#" class="text-decoration-none small fw-bold">Login instead?</a>
            </div>

            <form id="proForm">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold text-muted mb-2">First Name</label>
                        <input type="text" class="form-control custom-input" placeholder="John">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold text-muted mb-2">Last Name</label>
                        <input type="text" class="form-control custom-input" placeholder="Doe">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-mail-line"></i></span>
                        <input type="email" class="form-control custom-input" placeholder="name@example.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text">+234</span>
                        <input type="tel" class="form-control custom-input" placeholder="800 000 0000">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold text-muted mb-2">Account Type</label>
                    <select class="form-select custom-input">
                        <option selected>Personal Trader</option>
                        <option>Business/Entity</option>
                        <option>Developer</option>
                    </select>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms">
                    <label class="form-check-label small text-muted" for="terms">
                        I agree to the <a href="#" class="text-primary fw-bold">Terms of Service</a> and Privacy Policy.
                    </label>
                </div>

                <button type="submit" class="btn btn-submit w-100 shadow-lg">
                    Continue to Security <i class="ri-arrow-right-line ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>