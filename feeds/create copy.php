<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__."/../main-function.php"; ?>
    <?php 
        $main = new main($conn);
        if($main->isLoggedIn() === false) header('location:/');
        $userData = $main->getUserData()['data'];
        $userProfile=json_decode($userData['profile'],true);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-gold: #ffcc00;
        }

        body { 
            background: #0f0f1a; 
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .editor-container {
            max-width: 600px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 20px;
        }

        .user-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }

        textarea.post-input {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.2rem;
            resize: none;
            width: 100%;
            min-height: 150px;
            padding: 10px 0;
        }
        textarea.post-input:focus { outline: none; box-shadow: none; }

        .toolbar {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tool-icon {
            font-size: 1.4rem;
            color: var(--mflow-blue);
            cursor: pointer;
            margin-right: 15px;
            transition: 0.3s;
        }
        .tool-icon:hover { color: #fff; transform: translateY(-2px); }

        .btn-post {
            background: var(--mflow-blue);
            color: #fff;
            border: none;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-post:disabled { opacity: 0.5; cursor: not-allowed; }

        /* monetization Badge */
        .monie-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 204, 0, 0.1);
            color: var(--mflow-gold);
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
            cursor: pointer;
        }

        /* Image Preview Area */
        #preview-area img {
            width: 100%;
            border-radius: 15px;
            margin-top: 15px;
            display: none;
        }
    </style>
</head>
<body>

<header class="p-3 d-flex align-items-center justify-content-between">
    <a href="javascript:;" onclick="history.back()" class="text-white text-decoration-none"><i class="ri-arrow-left-s-line fs-3"></i></a>
    <h6 class="mb-0 fw-bold">New Post</h6>
    <button class="btn-post" id="headerPostBtn" onclick="submitPost()">Post</button>
</header>

<div class="container">
    <div class="editor-container">
        <div class="d-flex align-items-center mb-3">
            <img src="/uploads/<?= $userProfile['profile_pic'] ?>" class="user-avatar me-3">
            <div>
                <div class="fw-bold" class="text-capitalize"><?= $userProfile['first_name'].' '.$userProfile['last_name'] ?></div>
                <div class="monie-badge my-2 text-uppercase" data-bs-toggle="modal" data-bs-target="#bountyModal">
                    <i class="ri-diamond-line me-1"></i> <?= $userProfile['level'] ?? 'newbies' ?>
                </div>
            </div>
        </div>

        <textarea class="post-input" id="postContent" placeholder="What's happening in the flow?" oninput="checkInput()"></textarea>

        <div id="preview-area">
            <img id="imagePreview" src="" alt="preview">
        </div>

        <div class="toolbar mt-3">
            <div class="d-flex">
                <label for="imgUpload" class="tool-icon"><i class="ri-image-add-line"></i></label>
                <input type="file" id="imgUpload" hidden accept="image/*" multiple  onchange="previewImage(event)">
                
                <i class="ri-bar-chart-2-line tool-icon" title="Add Poll"></i>
                <i class="ri-map-pin-2-line tool-icon" title="Location"></i>
                <i class="ri-calendar-event-line tool-icon" title="Schedule"></i>
            </div>
            
            <div class="d-flex align-items-center">
                <small class="text-muted me-3" id="charCount">0/280</small>
                <div class="vr me-3 opacity-25"></div>
                <i class="ri-earth-line text-muted"></i>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bountyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary rounded-4">
            <div class="modal-body p-4 text-center">
                <i class="ri-coin-fill text-warning" style="font-size: 50px;"></i>
                <h4 class="fw-bold mt-2">Set monieBounty</h4>
                <p class="text-secondary small">Reward the top comment or reply with monieCoins.</p>
                
                <input type="number" class="form-control bg-dark text-white border-secondary py-3 text-center fs-4 fw-bold" placeholder="0.00 MC">
                
                <button class="btn btn-warning w-100 py-3 rounded-pill fw-bold mt-4" data-bs-dismiss="modal">Attach Bounty</button>
            </div>
        </div>
    </div>
</div>

<script>
    function checkInput() {
        const content = document.getElementById('postContent');
        const count = document.getElementById('charCount');
        const btn = document.getElementById('headerPostBtn');
        
        count.innerText = `${content.value.length}/280`;
        
        if(content.value.trim().length > 0) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }


    function previewImages(event) {
    const previewArea = document.getElementById('preview-area');
    previewArea.innerHTML = ''; // clear old previews
    Array.from(event.target.files).forEach((file, index) => {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'preview-img';
        img.dataset.index = index;

        const removeBtn = document.createElement('button');
        removeBtn.innerText = 'Remove';
        removeBtn.onclick = () => {
            img.remove();
            removeBtn.remove();
        };

        previewArea.appendChild(img);
        previewArea.appendChild(removeBtn);
    });
}


    // function previewImage(event) {
    //     const preview = document.getElementById('imagePreview');
    //     preview.src = URL.createObjectURL(event.target.files[0]);
    //     preview.style.display = 'block';
    // }

    function submitPost() {
        const btn = document.getElementById('headerPostBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled = true;

        // Simulate upload
        setTimeout(() => {
            window.location.href = "/feed?posted=true";
        }, 1500);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>