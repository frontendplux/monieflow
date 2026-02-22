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

        /* Layout Adjustments */
        .settings-container {
            padding-bottom: 50px;
        }

        /* Settings Sidebar Styling */
        .settings-group {
            background: #fff;
            border-radius: 15px;
            margin-bottom: 25px;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .settings-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s;
            border-bottom: 1px solid #f8f9fa;
        }

        .settings-item:last-child { border-bottom: none; }
        .settings-item:hover, .settings-item:active { background: #f0f2f5; color: inherit; }

        .icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        /* Icon Colors */
        .bg-blue { background: #eef5ff; color: var(--mflow-blue); }
        .bg-gold { background: #fff9e6; color: #ffcc00; }
        .bg-purple { background: #f5eeff; color: #7b2ff7; }
        .bg-green { background: #e6f7ed; color: #198754; }
        .bg-red { background: #fff0f3; color: var(--mflow-red); }

        .settings-label { flex-grow: 1; font-weight: 500; font-size: 0.9rem; }
        .settings-meta { font-size: 0.8rem; color: #888; margin-right: 5px; }

        .group-title {
            padding-left: 10px;
            margin-bottom: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logout-btn { color: var(--mflow-red) !important; font-weight: 600; }

        /* Profile Image Preview */
        .img-preview-container {
            width: 100px;
            height: 100px;
            flex-shrink: 0;
        }
        
        #img-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Mobile specific tweaks */
        @media (max-width: 767.98px) {
            .settings-sidebar { margin-bottom: 30px; }
            .group-title { padding-left: 0; }
        }
    </style>
</head>
<body>

<?php include __DIR__."/c-header.php"; ?>

<main class="container py-4">
    <div class="row">
        
        <div class="col-lg-4 col-md-5 settings-sidebar d-none d-md-block">
            <?php include __DIR__."/c-sidebar.php"; ?>
        </div>

        <div class="col-lg-8 col-md-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1">Edit Profile</h4>
                    <p class="text-muted small mb-4">Update your public information and account details.</p>
                    
                    <form action="/update-profile" method="POST" enctype="multipart/form-data">
                        
                        <div class="d-flex flex-column flex-sm-row align-items-center mb-4 pb-3 border-bottom">
                            <div class="img-preview-container mb-3 mb-sm-0">
                                <img id="img-preview" src="https://picsum.photos/seed/user1/150/150" 
                                     class="rounded-circle" alt="Avatar">
                            </div>
                            <div class="ms-sm-4 text-center text-sm-start">
                                <label for="avatar" class="form-label fw-bold small">Profile Picture</label>
                                <input type="file" class="form-control form-control-sm" id="avatar" 
                                       name="avatar" accept="image/*" onchange="previewImage(event)">
                                <div class="form-text mt-1">Recommended: Square image, max 2MB</div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label small fw-bold">Full Name</label>
                                <input type="text" class="form-control" name="full_name" value="John Doe">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label small fw-bold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">@</span>
                                    <input type="text" class="form-control" name="username" value="johndoe">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" class="form-control" name="email" value="john@example.com">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label small fw-bold">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" placeholder="+1 (555) 000-0000">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Bio</label>
                                <textarea class="form-control" name="bio" rows="3">Passionate developer and coffee enthusiast.</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Website</label>
                                <input type="url" class="form-control" name="website" placeholder="https://example.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Account Focus</label>
                                <select class="form-select" name="account_type">
                                    <option value="feed" selected>General Feed</option>
                                    <option value="market">Marketplace Seller</option>
                                    <option value="reel">Content Creator (Reels)</option>
                                    <option value="pages">Public Figure/Page</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                            <button type="button" class="btn btn-light px-4 border rounded-pill">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold" style="background-color: var(--mflow-blue);">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4 d-md-none">
                <small class="text-muted">monieFlow v2.4.0 (2026)</small>
            </div>
        </div>
    </div>
</main>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('img-preview');
            output.src = reader.result;
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>