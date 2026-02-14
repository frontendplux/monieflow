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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
            font-family: system-ui, sans-serif;
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
        }
        .btn-post:hover:not(:disabled) { background: #1f6be0; }
        .btn-post:disabled { opacity: 0.4; }

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


<header class="p-3 d-flex align-items-center justify-content-between">
    <a href="javascript:;" onclick="history.back()" class="text-white text-decoration-none"><i class="ri-arrow-left-s-line fs-3"></i></a>
    <h6 class="mb-0 fw-bold">New Post</h6>
    <button class="btn-post" id="headerPostBtn" onclick="submitPost()">Post</button>
</header>

<div class="container">
    <div class="editor-container">
        <div class="d-flex align-items-start  mb-4">
            <img src="/uploads/<?= htmlspecialchars($userProfile['profile_pic'] ?? 'default-avatar.jpg') ?>"
                 class="user-avatar me-3" alt="Profile">
            <div>
                <div class="fw-bold"><?= htmlspecialchars($userProfile['first_name'] . ' ' . $userProfile['last_name']) ?></div>
                <div class="monie-badge mt-1 text-uppercase" data-bs-toggle="modal" data-bs-target="#bountyModal">
                   <span id="preview-location" style="font-size:small" class="d-flex gap-1 align-items-center">location</span>
                </div>
            </div>
        </div>
        <span id="preview-location" class="d-flex gap-2 mb-4 align-items-center"></span>
        <textarea class="post-input" id="postContent" placeholder="What's happening in the flow?"
                  maxlength="280" oninput="updateUI()"></textarea>
        
        <div id="preview-area"></div>
        <span id="preview-location"></span>

        <div class="toolbar">
            <div class="d-flex align-items-center">
                <label for="imgUpload" class="tool-icon" title="Add photos">
                    <i class="ri-image-add-line"></i>
                </label>
                <input type="file" id="imgUpload" accept="image/jpeg,image/png,image/webp" multiple hidden>

                <i class="ri-map-pin-2-line tool-icon" title="Add location" onclick="attachLocation()"></i>

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
<div class="modal fade" id="bountyModal">
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
// Globals & constants
// ────────────────────────────────────────────────
let selectedFiles = [];
const MAX_IMAGES = 4;
const MAX_CHARS   = 280;

// ────────────────────────────────────────────────
// UI update (char count + button state)
// ────────────────────────────────────────────────
function updateUI() {
    const textarea = document.getElementById('postContent');
    const counter  = document.getElementById('charCount');
    const button   = document.getElementById('postButton');

    if (!textarea || !counter || !button) return;

    const len = textarea.value.length;
    counter.textContent = `${len}/${MAX_CHARS}`;

    if (len > 260)      counter.className = 'text-warning fw-bold';
    else if (len > 275) counter.className = 'text-danger fw-bold';
    else                counter.className = 'text-muted';

    button.disabled = len === 0 || len > MAX_CHARS;
}

// Auto-resize textarea
function autoResizeTextarea() {
    const ta = document.getElementById('postContent');
    if (!ta) return;
    ta.style.height = 'auto';
    ta.style.height = (ta.scrollHeight) + 'px';
}

// ────────────────────────────────────────────────
// Image preview handling
// ────────────────────────────────────────────────
function previewImages(e) {
    const preview = document.getElementById('preview-area');
    if (!preview) return;

    const newFiles = Array.from(e.target.files)
        .filter(f => f.type.startsWith('image/') && selectedFiles.length < MAX_IMAGES);

    newFiles.forEach(file => {
        selectedFiles.push(file);
        const idx = selectedFiles.length - 1;

        const reader = new FileReader();
        reader.onload = ev => {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-wrapper';
            wrapper.dataset.index = idx;
            wrapper.innerHTML = `
                <img src="${ev.target.result}" alt="preview">
                <button type="button" class="remove-btn" onclick="removeImage(${idx})">×</button>
            `;
            preview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });

    e.target.value = ''; // clear input for next selection
}

function removeImage(index) {
    if (index < 0 || index >= selectedFiles.length) return;

    selectedFiles.splice(index, 1);

    // Re-render all previews (simple & reliable)
    const preview = document.getElementById('preview-area');
    if (!preview) return;

    preview.innerHTML = '';

    selectedFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-wrapper';
            wrapper.dataset.index = i;
            wrapper.innerHTML = `
                <img src="${e.target.result}" alt="preview">
                <button type="button" class="remove-btn" onclick="removeImage(${i})">×</button>
            `;
            preview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

// ────────────────────────────────────────────────
// Location (reverse geocoding)
// ────────────────────────────────────────────────
async function attachLocation() {
    const locationEl = document.getElementById('preview-location');
    if (!locationEl) return;

    locationEl.innerHTML = `<i class="spinner-border spinner-border-sm"></i> connecting...`;

    if (!navigator.geolocation) {
        locationEl.textContent = "Geolocation not supported";
        return;
    }

    try {
        const pos = await new Promise((resolve, reject) =>
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            })
        );

        const { latitude, longitude } = pos.coords;

        const res = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=16&addressdetails=1`
        );

        if (!res.ok) throw new Error("Nominatim request failed");

        const data = await res.json();

        const place =
            data.address?.city ||
            data.address?.town ||
            data.address?.state ||
            data.display_name ||
            `${latitude.toFixed(5)}, ${longitude.toFixed(5)}`;

        locationEl.innerHTML = `<i class="ri-map-pin-2-line"></i> ${place}`;
    } catch (err) {
        console.warn("Location failed:", err);
        locationEl.textContent = "Could not get location";
    }
}

// ────────────────────────────────────────────────
// Emoji picker
// ────────────────────────────────────────────────
function openEmojiPicker() {
    const modalEl   = document.getElementById('emojiModal');
    const container = document.getElementById('emojiContainer');
    const search    = document.getElementById('emojiSearch');
    const textarea  = document.getElementById('postContent');

    if (!modalEl || !container || !search || !textarea) return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    // Extended emoji list (you can load from CDN or JSON later)
    const emojis = [
        '😀','😃','😄','😁','😆','😅','😂','🤣','🥲','☺️','😊','😇','🙂','🙃','😉','😌','😍','🥰','😘','😗','😙','😚','😋','😛','😝','😜','🤪','🤨','🧐','🤓','😎','🥸','🤩','🥳','😏','😒','😞','😔','😟','😕','🙁','☹️','😣','😖','😫','😩','🥺','😢','😭','😤','😠','😡','🤬','🤯','😳','🥵','🥶','😱','😨','😰','😥','😓','🤗','🤔','🤭','🤫','🤥','😶','😐','😑','😬','🙄','😯','😦','😧','😮','😲','🥱','😴','🤤','😪','😵','🥴','🤢','🤮','🤧','😷','🤒','🤕','🤑','🤠','😈','👿','👹','👺','💀','☠️','💩','🤡','👻','👽','👾','🤖','🎃','😺','😸','😹','😻','😼','😽','🙀','😿','😾',
        // hearts, symbols, objects...
        '❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','🔥','⭐','✨','⚡','💫','💥','☄️','🌪️','🌈','☀️','🌤️','⛅','🌥️','☁️','🌧️','⛈️','🌩️','❄️','☃️','⛄','🌊','💧','💦','🌫️','🌬️','🎈','🎉','🎊','🎁','🎂','🍰','🍕','🍔','🍟','🌮','🍣','🍣','🍱','🥟','🍜','🍲','🍛','🍝','🍤','🍙','🍚','🍘','🍥','🥠','🥮','🍪','🍫','🍬','🍭','🍮','🍯','🍼','🥛','☕','🍵','🍺','🍻','🥂','🍷','🥃','🍸','🍹','🧉','🍾','🥤','🧃','🥢','🍽️','🍴','🥄','🔪','🏺','⚽','🏀','🏈','⚾','🥎','🎾','🏐','🏉','🎱','🪀','🏓','🏸','🏒','🏑','🥍','🏏','🪃','🥅','⛳','🪁','🎣','🤿','🎽','🥋','🛹','🛼','🛷','⛸️','🥌','🎿','⛷️','🏂','🪂','🏋️','🤸','⛹️','🤼','🤽','🤾','🥊','🥋','🛡️','⚔️','🔫','🪚','🔨','🪓','⛏️','⚒️','🛠️','🗡️','⚙️','🔧','🔩','⚙️','🧲','🪜','🪞','🪟','🪑','🛋️','🪑','🛏️','🪞','🧴','🪒','🧴','🧴','🪒','🪒','🧴','🪒','🧴','🪒','🧴','🪒','🧴','🪒','🧴','🪒','🧴','🪒','🧴','🪒'
    ];

    function render(filter = '') {
        const html = emojis
            .filter(e => !filter || e.toLowerCase().includes(filter.toLowerCase()))
            .map(e => `<span class="emoji-span" data-emoji="${e}">${e}</span>`)
            .join('');

        container.innerHTML = html || '<p class="text-muted text-center py-4">No matching emojis</p>';

        container.querySelectorAll('.emoji-span').forEach(span => {
            span.onclick = () => {
                const emoji = span.dataset.emoji;
                const start = textarea.selectionStart;
                textarea.value =
                    textarea.value.substring(0, start) +
                    emoji +
                    textarea.value.substring(textarea.selectionEnd);
                textarea.focus();
                textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
                updateUI();
                autoResizeTextarea();
                modal.hide();
            };
        });
    }

    search.value = '';
    search.focus();
    search.oninput = () => render(search.value.trim());
    render();

    // Optional: close modal on outside click / escape
    modalEl.onclick = e => {
        if (e.target === modalEl) modal.hide();
    };

    modal.show();
}

// ────────────────────────────────────────────────
// Submit post (multipart form)
// ────────────────────────────────────────────────
async function submitPost() {
    const btn      = document.getElementById('postButton');
    const textarea = document.getElementById('postContent');
    if (!btn || !textarea) return;

    const content = textarea.value.trim();
    if (!content) return;

    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Posting...';

    const formData = new FormData();
    formData.append('action', 'feeds');   // adjust if your backend expects different key
    formData.append('content', content);

    selectedFiles.forEach(file => {
        formData.append('images[]', file);
    });

    try {
        const res = await fetch('/feeds/req.php', {
            method: 'POST',
            body: formData
            // DO NOT set Content-Type — browser sets multipart/form-data + boundary automatically
        });

        if (!res.ok) {
            throw new Error(`Server error ${res.status}`);
        }

        let result;
        try {
            result = await res.json();
        } catch {
            throw new Error("Invalid response from server");
        }

        if (result.success) {
            // Success path
            window.location.href = '/feed';  // or '/feeds', adjust as needed
        } else {
            alert(result.message || result.error || "Posting failed");
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    } catch (err) {
        console.error("Post failed:", err);
        alert("Could not post — check your connection or try again later.");
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// ────────────────────────────────────────────────
// Init
// ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.getElementById('postContent');
    const imgInput = document.getElementById('imgUpload');

    if (textarea) {
        textarea.addEventListener('input', () => {
            updateUI();
            autoResizeTextarea();
        });
        autoResizeTextarea();
        updateUI();
    }

    if (imgInput) {
        imgInput.addEventListener('change', previewImages);
    }

    // Optional: attach location on button click
    // document.getElementById('locationBtn')?.addEventListener('click', attachLocation);
});
</script>
</body>
</html>