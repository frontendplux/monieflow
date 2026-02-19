export default function profile(){
     const container=document.querySelector('.main-div-for-root-per-page  #main-profile');
    container.innerHTML=`
    <!DOCTYPE html>
<html lang="en">
<head>
    <?php 
       include __DIR__."/req.php";
       $new=new profile($conn); 
        if($new->isLoggedIn() === false) header('location:/');
        $user_id=$_GET['u'] ?? $new->getUserData()['data']['id'];
       $data=$new->profile($user_id);
       $profile=json_decode($data['profile'],true);
       $flowscount=$new->selectCountsfollower_and_following($user_id);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieflow | Facebook Style</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --fb-blue: #1877f2;
            --fb-bg: #f0f2f5;
            --fb-card: #ffffff;
            --fb-text: #050505;
            --fb-secondary-text: #65676b;
        }

        body {
            background-color: var(--fb-bg);
            font-family: Segoe UI, Helvetica, Arial, sans-serif;
            color: var(--fb-text);
        }

        /* --- Header & Cover --- */
        .fb-header-container {
            background: var(--fb-card);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .fb-cover {
            height: 200px;
            width: 100%;
            background: linear-gradient(to bottom, #ddd, #bbb);
            border-bottom-left-radius: 0px;
            border-bottom-right-radius: 0px;
            object-fit: cover;
        }

        .fb-profile-bottom {
            margin-top: -65px;
            padding-bottom: 20px;
        }

        .fb-avatar {
            width: 168px;
            height: 168px;
            border-radius: 50%;
            border: 4px solid var(--fb-card);
            object-fit: cover;
        }

        /* --- Tabs --- */
        .fb-tabs {
            border-top: 1px solid #ddd;
            display: flex;
            overflow: auto;
            padding: 4px 0;
        }
        .fb-tab-item {
            padding: 10px 16px;
            color: var(--fb-secondary-text);
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
        }
        .fb-tab-item:hover { background: #f2f2f2; }
        .fb-tab-item.active { color: var(--fb-blue); border-bottom: 3px solid var(--fb-blue); border-radius: 0; }

        /* --- Feed Layout --- */
        .fb-card {
            background: var(--fb-card);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            margin-bottom: 16px;
            padding: 16px;
        }

        /* Create Post Trigger Box */
        .create-post-input {
            background: #f0f2f5;
            border-radius: 20px;
            padding: 8px 12px;
            cursor: pointer;
            color: var(--fb-secondary-text);
            flex-grow: 1;
        }
        .create-post-input:hover { background: #e4e6eb; }

        /* --- Custom Emoji Pop --- */
        .custom-emoji-card {
            position: absolute; bottom: 60px; right: 0;
            background: white; box-shadow: 0 12px 28px rgba(0,0,0,0.2);
            border-radius: 8px; padding: 10px; z-index: 1060; width: 220px;
            animation: fbPop 0.2s ease-out;
        }
        @keyframes fbPop { from { transform: translateY(10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .emoji-grid span { cursor: pointer; font-size: 1.5rem; padding: 5px; transition: transform 0.1s; display: inline-block;}
        .emoji-grid span:hover { transform: scale(1.3); }

        /* Previews */
        .preview-img { width: 80px; height: 80px; object-fit: cover; border-radius: 0px; border: 1px solid #ddd; }
        .audio-card { background: #f0f2f5; border-radius: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
<div class="fb-header-container mb-4">
    <div class="position-fixed w-100 top-0 p-2" style="z-index: 23390; background: #f0f8ffab;">
        <div class="container px-0 d-flex justify-content-between " >
            <a href="javascript:;" onclick="history.back()" class="ri-arrow-left-s-line text-decoration-none fs-3 text-dark d-flex align-items-center"> <span class="fs-6"><?= $profile['first_name'] ?> <?= $profile['last_name'] ?></span></a>
           
        </div>
    </div>
    <div class="container px-0 px-md-5" style="margin-top: 59px;">
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80" class="fb-cover" alt="Cover">

        <div class="d-md-flex text-center text-md-start align-items-end fb-profile-bottom px-4">
            <img src="/uploads/<?= $profile['profile_pic'] ?>" class="fb-avatar me-3 shadow-sm" alt="Avatar">
            <div class="mb-2">
                <h1 class="fw-bold mb-0 fs-5"><?= $profile['first_name'] ?> <?= $profile['last_name'] ?></h1>
                <p class="text-secondary fw-bold"><span><?= $flowscount['followers'] ?> .Follower</span>  <span class="ms-2"><?= $flowscount['following'] ?> .Following</span></p>
            </div>
            <div class="ms-auto mb-3">
                <a href="/feeds/create.php" class="btn btn-primary fw-bold px-3"><i class="ri-add-line"></i> Add to Story</a>
                <button onclick="window.location.href=window.innerWidth <= 768 ? '/profile/menu.php' : '/profile/settings/edit-profile.php';" class="btn btn-light fw-bold px-3 ms-2"><i class="ri-pencil-fill"></i> Edit Profile</a>
            </div>
        </div>
        <div class="fb-tabs mt-2 px-4">
            <a href="#" class="fb-tab-item active">Posts</a>
            <a href="#" class="fb-tab-item">Pages</a>
            <a href="#" class="fb-tab-item">Friends</a>
            <a href="#" class="fb-tab-item">Photos</a>
            <a href="#" class="fb-tab-item">Videos</a>
        </div>
    </div>
</div>

<div class="container px-md-5">
    <div class="row">
        <div class="col-lg-5 d-none d-lg-block">
            <div class="fb-card">
                <h5 class="fw-bold">Intro</h5>
                <p class="text-center py-2">Software Engineer at TechCorp. Lover of pizza and clean code. 🍕💻</p>
                <a href="/profile/settings/edit-profile.php" class="btn btn-light w-100 fw-bold mb-3">Edit Bio</a>
                <div class="small text-secondary">
                    <div class="mb-2"><i class="ri-briefcase-fill me-2"></i> Works at <b>TechCorp</b></div>
                    <div class="mb-2"><i class="ri-map-pin-2-fill me-2"></i> From <b>New York, NY</b></div>
                    <div class="mb-2"><i class="ri-heart-fill me-2"></i> Single</div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="fb-card d-flex gap-2 align-items-center">
                  <a href="/profile/"><img src="/uploads/<?= $profile['profile_pic'] ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;" /></a>
                    <a href="/feeds/create.php" class="composer-input text-decoration-none create-post-input">
                        What's on your mind, <?= $profile['username'] ?>?
                    </a>
            </div>

            <div class="fb-card">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://i.pravatar.cc/40?u=me" class="rounded-circle me-2">
                    <div>
                        <div class="fw-bold">John Doe</div>
                        <small class="text-muted">2 hours ago · <i class="ri-earth-fill"></i></small>
                    </div>
                </div>
                <p>Coding all night to get this Facebook UI ready! 🚀</p>
                <div class="d-flex border-top pt-2">
                    <button class="btn btn-light flex-grow-1 text-secondary fw-bold"><i class="ri-thumb-up-line"></i> Like</button>
                    <button class="btn btn-light flex-grow-1 text-secondary fw-bold"><i class="ri-chat-3-line"></i> Comment</button>
                    <button class="btn btn-light flex-grow-1 text-secondary fw-bold"><i class="ri-share-forward-line"></i> Share</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Photo Gallery</h4>
                <p class="text-muted small mb-0">Manage your public and exclusive media.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light rounded-pill btn-sm border px-3">
                    <i class="ri-settings-3-line"></i>
                </button>
                <button class="btn btn-primary rounded-pill btn-sm fw-bold px-3">
                    <i class="ri-add-line me-1"></i> Upload
                </button>
            </div>
        </div>

        <ul class="nav nav-pills nav-fill bg-light rounded-pill p-1 mb-4" id="galleryTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active rounded-pill py-2 small fw-bold" data-bs-toggle="pill">Public</button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill py-2 small fw-bold" data-bs-toggle="pill">
                    <i class="ri-lock-2-line me-1"></i>Exclusive
                </button>
            </li>
        </ul>

        <div class="row g-2">
            <div class="col-4 col-md-3">
                <div class="ratio ratio-1x1 rounded-3 overflow-hidden position-relative group-hover">
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=400" 
                         class="object-fit-cover w-100 h-100" alt="Gallery image">
                    <div class="position-absolute top-0 end-0 p-1">
                        <span class="badge bg-dark bg-opacity-50 small"><i class="ri-eye-line"></i> 1.2k</span>
                    </div>
                </div>
            </div>

            <div class="col-4 col-md-3">
                <div class="ratio ratio-1x1 rounded-3 overflow-hidden position-relative">
                    <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&q=80&w=400" 
                         class="object-fit-cover w-100 h-100" alt="Video thumb">
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <i class="ri-play-circle-fill text-white fs-2 opacity-75"></i>
                    </div>
                </div>
            </div>

            <div class="col-4 col-md-3">
                <div class="ratio ratio-1x1 rounded-3 overflow-hidden position-relative">
                    <img src="https://images.unsplash.com/photo-1550684848-fac1c5b4e853?auto=format&fit=crop&q=80&w=400" 
                         class="object-fit-cover w-100 h-100 blur-image" alt="Exclusive">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25 d-flex align-items-center justify-content-center">
                        <i class="ri-lock-fill text-white fs-3"></i>
                    </div>
                </div>
            </div>

            <div class="col-4 col-md-3">
                <div class="ratio ratio-1x1 rounded-3 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&q=80&w=400" class="object-fit-cover w-100 h-100">
                </div>
            </div>
            <div class="col-4 col-md-3">
                <div class="ratio ratio-1x1 rounded-3 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&q=80&w=400" class="object-fit-cover w-100 h-100">
                </div>
            </div>
            
            <div class="col-4 col-md-3">
                <a href="#" class="ratio ratio-1x1 rounded-3 border border-dashed d-flex align-items-center justify-content-center text-decoration-none bg-light hover-bg">
                    <div class="text-center text-muted">
                        <i class="ri-add-line fs-3"></i>
                        <div style="font-size: 0.65rem;">Add More</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="mt-4 p-3 bg-light rounded-4">
            <div class="row text-center">
                <div class="col-4 border-end">
                    <div class="fw-bold mb-0">128</div>
                    <div class="small text-muted">Photos</div>
                </div>
                <div class="col-4 border-end">
                    <div class="fw-bold mb-0">12GB</div>
                    <div class="small text-muted">Used</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold mb-0">4.5k</div>
                    <div class="small text-muted">Likes</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .blur-image { filter: blur(8px); }
    .object-fit-cover { object-fit: cover; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
    .hover-bg:hover { background: #eef2ff !important; transition: 0.3s; }
</style>
        </div>
    </div>
</div>

<div class="modal fade" id="createPostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 8px;">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold w-100 text-center">Create Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://i.pravatar.cc/40?u=me" class="rounded-circle me-2">
                    <div>
                        <div class="fw-bold">John Doe</div>
                        <span class="badge bg-light text-dark border"><i class="ri-group-fill"></i> Friends</span>
                    </div>
                </div>
                <form id="postForm">
                    <textarea class="form-control border-0 fs-5 ps-0 mb-2" id="postText" rows="4" placeholder="What's on your mind?" style="resize:none; box-shadow:none;"></textarea>
                    
                    <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2 mb-2"></div>
                    
                    <div id="audioPreviewContainer" class="mb-3 d-none">
                        <div class="audio-card p-2 d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-primary rounded-circle btn-sm" id="playToggle"><i class="ri-play-fill" id="playIcon"></i></button>
                            <small class="text-truncate" id="audioFileName" style="max-width: 150px;"></small>
                            <div class="progress flex-grow-1" style="height: 4px;"><div id="audioProgress" class="progress-bar bg-primary" style="width: 0%"></div></div>
                            <i class="ri-close-line text-muted" style="cursor:pointer" onclick="removeAudio()"></i>
                        </div>
                    </div>
                    <audio id="hiddenPlayer"></audio>

                    <div class="border rounded-3 p-2 d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-bold small ms-2">Add to your post</span>
                        <div class="d-flex gap-1">
                            <button type="button" onclick="document.getElementById('image-open').click()" class="btn btn-light text-success"><i class="ri-image-add-fill"></i></button>
                            <button type="button" onclick="handleAudioClick()" class="btn btn-light text-danger"><i class="ri-music-2-fill"></i></button>
                            
                            <div class="position-relative">
                                <button type="button" id="emojiBtn" class="btn btn-light text-warning"><i class="ri-emotion-fill"></i></button>
                                <div id="customEmojiPicker" class="custom-emoji-card d-none">
                                    <div class="emoji-grid text-center">
                                        <span>😊</span><span>😂</span><span>😍</span><span>🔥</span>
                                        <span>❤️</span><span>✨</span><span>🚀</span><span>🎉</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="file" id="image-open" class="d-none" multiple accept="image/*">
                    <input type="file" id="music-open" class="d-none" accept="audio/*">
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 px-3 pb-3">
                <div id="uploadProgressContainer" class="w-100 mb-2 d-none">
                    <div class="progress" style="height: 5px;"><div id="uploadProgressBar" class="progress-bar" style="width: 0%"></div></div>
                </div>
                <button type="button" id="submitBtn" onclick="uploadPost()" class="btn btn-primary w-100 fw-bold py-2" style="background: var(--fb-blue); border:none;">Post</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let selectedImages = [];
    const imageInput = document.getElementById('image-open');
    const musicInput = document.getElementById('music-open');
    const player = document.getElementById('hiddenPlayer');

    // 1. Multiple Image Handling
    imageInput.addEventListener('change', function() {
        Array.from(this.files).forEach(file => {
            selectedImages.push(file);
            const reader = new FileReader();
            reader.onload = e => {
                const img = <img src="$d{e.target.result}" class="preview-img">;
                document.getElementById('imagePreviewContainer').insertAdjacentHTML('beforeend', img);
            };
            reader.readAsDataURL(file);
        });
    });

    // 2. Audio Validation
    function handleAudioClick() {
        if (selectedImages.length === 0) { alert("Please add a cover image first!"); return; }
        musicInput.click();
    }

    musicInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            document.getElementById('audioPreviewContainer').classList.remove('d-none');
            document.getElementById('audioFileName').innerText = file.name;
            player.src = URL.createObjectURL(file);
        }
    });

    // Audio Playback
    document.getElementById('playToggle').onclick = () => {
        const icon = document.getElementById('playIcon');
        if (player.paused) { player.play(); icon.className = 'ri-pause-fill'; }
        else { player.pause(); icon.className = 'ri-play-fill'; }
    };

    player.ontimeupdate = () => {
        document.getElementById('audioProgress').style.width = (player.currentTime / player.duration * 100) + '%';
    };

    function removeAudio() {
        player.pause(); document.getElementById('audioPreviewContainer').classList.add('d-none');
        musicInput.value = "";
    }

    // 3. Emoji Picker
    document.getElementById('emojiBtn').onclick = (e) => {
        e.stopPropagation(); document.getElementById('customEmojiPicker').classList.toggle('d-none');
    };
    document.querySelectorAll('.emoji-grid span').forEach(s => {
        s.onclick = () => { document.getElementById('postText').value += s.innerText; };
    });

    // 4. Upload Logic
    function uploadPost() {
        const btn = document.getElementById('submitBtn');
        const progCont = document.getElementById('uploadProgressContainer');
        const progBar = document.getElementById('uploadProgressBar');

        const formData = new FormData();
        formData.append('content', document.getElementById('postText').value);
        selectedImages.forEach(f => formData.append('images[]', f));
        if (musicInput.files[0]) formData.append('audio', musicInput.files[0]);

        const xhr = new XMLHttpRequest();
        progCont.classList.remove('d-none');
        btn.disabled = true;

        xhr.upload.onprogress = e => {
            if (e.lengthComputable) progBar.style.width = Math.round((e.loaded / e.total) * 100) + '%';
        };

        xhr.onload = () => {
            if (xhr.status === 200) { btn.innerText = "Done!"; location.reload(); }
        };

        xhr.open('POST', '/feeds/req.php', true);
        xhr.send(formData);
    }
</script>
</body>
</html>`
}