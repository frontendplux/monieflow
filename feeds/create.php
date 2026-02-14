<?php
include __DIR__."/../main-function.php";

$main = new main($conn);

if ($main->isLoggedIn() === false) {
    header('Location: /');
    exit;
}

$userData   = $main->getUserData()['data'] ?? null;
$userProfile = json_decode($userData['profile'] ?? '{}', true);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    <style>
        :root {
            --mflow-blue: #1877f2;
            --mflow-gold: #ffcc00;
        }
        body {
            background: #0a0a15;
            color: #e0e0ff;
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }
        header {
            background: rgba(10,10,21,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .editor-container {
            max-width: 620px;
            margin: 1.5rem auto;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .user-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255,255,255,0.12);
        }
        textarea.post-input {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.25rem;
            resize: none;
            width: 100%;
            min-height: 140px;
            padding: 0.5rem 0;
            line-height: 1.45;
        }
        textarea.post-input:focus { outline: none; }
        textarea.post-input::placeholder { color: #8888aa; }
        .toolbar {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .tool-icon {
            font-size: 1.55rem;
            color: var(--mflow-blue);
            cursor: pointer;
            margin-right: 1.25rem;
            transition: all 0.2s;
        }
        .tool-icon:hover { color: white; transform: translateY(-1px); }
        .btn-post {
            background: var(--mflow-blue);
            color: white;
            border: none;
            padding: 0.55rem 1.8rem;
            border-radius: 999px;
            font-weight: 600;
            min-width: 90px;
        }
        .btn-post:hover:not(:disabled) { background: #1f6be0; }
        .btn-post:disabled { opacity: 0.4; cursor: not-allowed; }
        .monie-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255,204,0,0.14);
            color: var(--mflow-gold);
            padding: 0.35rem 0.9rem;
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
        }
        .monie-badge:hover { background: rgba(255,204,0,0.25); }
        #preview-area {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin: 1rem 0;
        }
        .preview-wrapper {
            position: relative;
            width: 118px;
            height: 118px;
        }
        .preview-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .remove-btn {
            position: absolute;
            top: 6px; right: 6px;
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 1.1rem;
            line-height: 1;
            cursor: pointer;
        }
        #charCount { font-size: 0.9rem; }
        #charCount.warning { color: #ffaa66; }
        #charCount.danger   { color: #ff5555; font-weight: 600; }
        .emoji-span {
            font-size: 1.9rem;
            padding: 0.4rem;
            cursor: pointer;
            display: inline-block;
            transition: transform 0.1s;
        }
        .emoji-span:hover { transform: scale(1.25); }
    </style>
</head>
<body>

<header class="p-3 d-flex align-items-center justify-content-between position-sticky top-0 z-3">
    <a href="javascript:history.back()" class="text-white text-decoration-none">
        <i class="ri-arrow-left-s-line fs-3"></i>
    </a>
    <h6 class="mb-0 fw-bold">New Post</h6>
    <button class="btn-post" id="postButton" onclick="submitPost()">Post</button>
</header>

<div class="container">
    <div class="editor-container">
        <div class="d-flex align-items-start mb-4">
            <img src="/uploads/<?= htmlspecialchars($userProfile['profile_pic'] ?? 'default-avatar.jpg') ?>"
                 class="user-avatar me-3" alt="You">
            <div>
                <div class="fw-bold"><?= htmlspecialchars($userProfile['first_name'] . ' ' . ($userProfile['last_name'] ?? '')) ?></div>
                <div class="monie-badge mt-1 text-uppercase" onclick="attachLocation()">
                    <span id="preview-location" class="d-flex gap-1 align-items-center">
                        <i class="ri-map-pin-2-line"></i> Add location
                    </span>
                </div>
            </div>
        </div>

        <textarea class="post-input" id="postContent" placeholder="What's happening in the flow?"
                  maxlength="280" oninput="updateUI(); autoResizeTextarea()"></textarea>

        <div id="preview-area" class="mt-3"></div>

        <div class="toolbar">
            <div class="d-flex align-items-center">
                <label for="imgUpload" class="tool-icon me-4" title="Add photos/videos">
                    <i class="ri-image-add-line"></i>
                </label>
                <input type="file" id="imgUpload" accept="image/jpeg,image/png,image/webp" multiple hidden>

                <i class="ri-map-pin-2-line tool-icon me-4" title="Add location" onclick="attachLocation()"></i>

                <i class="ri-emotion-laugh-fill tool-icon" title="Emoji" onclick="openEmojiPicker()"></i>
            </div>

            <div class="d-flex align-items-center gap-3">
                <small id="charCount" class="text-muted">0/280</small>
                <i class="ri-earth-line text-muted"></i>
            </div>
        </div>
    </div>
</div>

<!-- Bounty Modal (placeholder) -->
<div class="modal fade" id="bountyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-body text-center p-4">
                <i class="ri-coin-fill text-warning fs-1 mb-3"></i>
                <h5 class="fw-bold">Attach monieBounty</h5>
                <p class="text-secondary small">Reward top reply / comment</p>
                <input type="number" step="0.01" min="0" class="form-control text-center fs-4 fw-bold bg-dark border-secondary mb-3" placeholder="0.00 MC">
                <button class="btn btn-warning w-100 py-3 rounded-pill fw-bold" data-bs-dismiss="modal">Attach</button>
            </div>
        </div>
    </div>
</div>

<!-- Emoji Modal -->
<div class="modal fade" id="emojiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-0 pb-0">
                <input type="text" id="emojiSearch" class="form-control bg-dark-subtle text-white border-0" placeholder="Search emoji..." autocomplete="off">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 320px; overflow-y: auto;" id="emojiContainer"></div>
        </div>
    </div>
</div>

<script>
// ────────────────────────────────────────────────
// Globals
// ────────────────────────────────────────────────
let selectedFiles = [];
const MAX_IMAGES = 4;
const MAX_CHARS  = 280;

// ────────────────────────────────────────────────
// UI Helpers
// ────────────────────────────────────────────────
function updateUI() {
    const ta = document.getElementById('postContent');
    const cnt = document.getElementById('charCount');
    const btn = document.getElementById('postButton');

    if (!ta || !cnt || !btn) return;

    const len = ta.value.length;
    cnt.textContent = `${len}/${MAX_CHARS}`;

    if (len > 275)      cnt.className = 'danger';
    else if (len > 260) cnt.className = 'warning';
    else                cnt.className = 'text-muted';

    btn.disabled = len === 0 || len > MAX_CHARS;
}

function autoResizeTextarea() {
    const ta = document.getElementById('postContent');
    if (ta) {
        ta.style.height = 'auto';
        ta.style.height = ta.scrollHeight + 'px';
    }
}

// ────────────────────────────────────────────────
// Image Preview
// ────────────────────────────────────────────────
function previewImages(e) {
    const preview = document.getElementById('preview-area');
    if (!preview) return;

    const files = Array.from(e.target.files)
        .filter(f => f.type.startsWith('image/') && f.size <= 5 * 1024 * 1024); // max 5MB

    files.forEach(file => {
        if (selectedFiles.length >= MAX_IMAGES) return;
        selectedFiles.push(file);
        const idx = selectedFiles.length - 1;

        const reader = new FileReader();
        reader.onload = ev => {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-wrapper';
            wrapper.innerHTML = `
                <img src="${ev.target.result}" alt="preview">
                <button type="button" class="remove-btn" onclick="removeImage(${idx})">×</button>
            `;
            preview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });

    e.target.value = '';
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    const preview = document.getElementById('preview-area');
    preview.innerHTML = '';
    selectedFiles.forEach((f, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'preview-wrapper';
            div.innerHTML = `
                <img src="${e.target.result}" alt="preview">
                <button type="button" class="remove-btn" onclick="removeImage(${i})">×</button>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(f);
    });
}

// ────────────────────────────────────────────────
// Location
// ────────────────────────────────────────────────
async function attachLocation() {
    const el = document.getElementById('preview-location');
    if (!el) return;

    el.innerHTML = `<i class="spinner-border spinner-border-sm me-1"></i>Connecting...`;

    if (!navigator.geolocation) {
        el.textContent = 'Location not supported';
        return;
    }

    try {
        const pos = await new Promise((res, rej) => 
            navigator.geolocation.getCurrentPosition(res, rej, { timeout: 10000 })
        );
        const { latitude, longitude } = pos.coords;

        const resp = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=16&addressdetails=1`
        );
        const data = await resp.json();

        const place = data.address?.city || data.address?.town || data.address?.state || data.display_name || 'Unknown';
        el.innerHTML = `<i class="ri-map-pin-2-line me-1"></i>${place}`;
    } catch (err) {
        el.textContent = 'Could not get location';
    }
}

// ────────────────────────────────────────────────
// Emoji Picker (simplified)
// ────────────────────────────────────────────────
function openEmojiPicker() {
    const ta = document.getElementById('postContent');
    if (!ta) return;
    // Example: insert a smiley directly
    ta.value += ' 😀';
    updateUI();
    autoResizeTextarea();
}

// ────────────────────────────────────────────────
// Submit Post
// ────────────────────────────────────────────────
async function submitPost() {
    const btn = document.getElementById('postButton');
    const ta  = document.getElementById('postContent');
    if (!btn || !ta) return;
    const content = ta.value.trim();
    if (!content) {
        alert("Please write something first");
        return;
    }

    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Posting...';

    const formData = new FormData();
    formData.append('action', 'create_feeds');
    formData.append('content', content);

    // Append all selected files correctly
    selectedFiles.forEach(file => formData.append('images[]', file));

    try {
        const res = await fetch('/feeds/req.php', {
            method: 'POST',
            body: formData
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        // Expect JSON from backend
        const result = await res.json();
        console.log(result);

        if (result.success) {
            window.location.href = '/feeds/';
        } else {
            alert(result.message || result.error || 'Failed to post');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    } catch (err) {
        console.error(err);
        alert('Network error — please try again');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

// ────────────────────────────────────────────────
// Init
// ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const ta = document.getElementById('postContent');
    const img = document.getElementById('imgUpload');

    if (ta) {
        ta.addEventListener('input', () => {
            updateUI();
            autoResizeTextarea();
        });
        autoResizeTextarea();
        updateUI();
    }

    if (img) {
        img.addEventListener('change', previewImages);
    }
});
</script>

</body>
</html>