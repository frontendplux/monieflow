<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/asset/bootstrap-5.3.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/asset/RemixIcon_Fonts_v4.1.0/rimix-icon/remixicon.css">
  <link rel="stylesheet" href="/style.css">
  <!-- Emoji Picker -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emoji-mart@5.6.0/dist/browser.css">
  <script src="https://cdn.jsdelivr.net/npm/emoji-mart@5.6.0/dist/browser.js"></script>
  <!-- FFmpeg -->
  <script src="https://unpkg.com/@ffmpeg/ffmpeg@0.12.6/dist/ffmpeg.min.js"></script>
  <script src="https://unpkg.com/@ffmpeg/util@0.12.6/dist/util.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="/asset/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="script.js"></script>
  <title>Monieflow</title>
<style>
html, body {
  height: 100%;
}

/* Modal input spacing */
.modal-body input {
  margin-bottom: 0.5rem;
}

/* Optional: make offcanvas links same style as your sidebar */
.offcanvas-body a {
  display: block;
  margin-bottom: 10px;
  text-decoration: none;
  color: #000;
}

.offcanvas-body a span {
  margin-right: 5px;
}
</style>
</head>
<body>


<?php include __dir__.'/pages/createfeeds.php'; ?>

<!-- MAIN LAYOUT -->
<div class="h-100 d-flex justify-content-center overflow-auto">
  <!-- LEFT NAV (desktop) -->
  <div class="w-100 link_nav sticky-top  d-none d-md-block" style="max-width: 300px;">
    <h1 class="text-uppercase fs-5 text-center my-3">
      <img src="/MONIEFLOW.png" style="width: 70px;" alt="">
    </h1>
    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#feedPostModal"><span class="text-secondary ri-sailboat-fill"></span>Create Flows</a>
    <a href="" ><span class="text-secondary ri-add-fill"></span>explore</a>
    <a href=""><span class="text-secondary ri-group-fill"></span>friends</a>
    <a href=""><span class="ri-message-2-fill text-secondary"></span>message</a>
    <a href=""><span class="ri-user-6-fill text-secondary"></span>profile</a>
  </div>
  <!-- CENTER CONTENT -->
  <div id="roots" class="w-100 p-3 d-flex" style="max-width: 950px;">
  </div>
</div>

<footer class="d-sm-none d-flex bg-white position-fixed bottom-0 w-100 p-2  justify-content-around">
     <a href="/home?" class="text-decoration-none text-center">
        <i class="ri-sailboat-fill"></i>
        <div  style="font-size: small;" class="text-dark">Explore</div>
     </a>
     <a href="" class="text-decoration-none text-center">
        <i class="ri-group-fill"></i>
        <div  style="font-size: small;" class="text-dark">Friends</div>
     </a>
     <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#feedPostModal" class="text-decoration-none text-center">
        <div class="ri-wifi-fill fs-1 bg-light p-3 py-2 shadow-lg rounded-circle" style="margin-top: -20px;"></div>
        <!-- <div style="font-size: small;">message</div> -->
     </a>
     <a href="" class="text-decoration-none text-center">
        <i class="ri-exchange-2-fill"></i>
        <div  style="font-size: small;" class="text-dark">Exchange</div>
     </a>
     <a href="" class="text-decoration-none text-center">
        <i class="ri-user-6-fill"></i>
        <div style="font-size: small;" class="text-dark">profile</div>
     </a>
</footer>

<!-- OFFCANVAS SIDEBAR (small screens) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu">
  <div class="offcanvas-header">
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body text-center">
    <h1 class="text-uppercase fs-5 text-center my-3">
      <img src="/MONIEFLOW.png" style="width: 70px;" alt="">
    </h1>
    <a href="/feed" class="my-4"><span class="ri-home-2-fill"></span> Feed</a>
    <a href="" class="my-4"><span class="ri-sailboat-fill"></span> Explore</a>
    <a href="" class="my-4"><span class="ri-message-2-fill"></span> Message</a>
    <a href="" class="my-4"><span class="ri-user-6-fill"></span> Profile</a>
    <a href="javascript:;" class="my-4" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
      <span class="ri-user-add-fill"></span> Sign in
    </a>
  </div>
</div>

<?php include __dir__."/pages/login.php"; ?>

<script src="auth.js"></script>
<script src="libery.js"></script>
<script>
/* ---------------- STATE ---------------- */
const state = {
  images: [],
  video: null,
  trimmedVideo: null,
  audio: null,
  audioCover: null,
  videoStart: 0,
  videoEnd: 0,
  videoDuration: 0
};

/* ---------------- HELPERS ---------------- */
function clearAllMedia() {
  state.images = [];
  state.video = null;
  state.trimmedVideo = null;
  state.audio = null;
  state.audioCover = null;
}

function block(type) {
  if (type === "visual") {
    postAudio.disabled = true;
    audioCover.disabled = true;
  }
  if (type === "audio") {
    postImages.disabled = true;
    postVideo.disabled = true;
  }
}

function unblockAll() {
  postImages.disabled = false;
  postVideo.disabled = false;
  postAudio.disabled = false;
  audioCover.disabled = false;
}

/* ---------------- VALIDATION ---------------- */
function validateRules() {
  if ((state.images.length || state.trimmedVideo) && state.audio) {
    alert("Images or video cannot be mixed with audio.");
    return false;
  }
  if (state.audio && !state.audioCover) {
    alert("Audio requires a cover image.");
    return false;
  }
  return true;
}

/* ---------------- IMAGE ---------------- */
postImages.onchange = e => {
  clearAllMedia();
  unblockAll();
  block("visual");

  state.images = [...e.target.files];
  renderPreview();
};

/* ---------------- VIDEO (TRIM) ---------------- */
const { FFmpeg } = FFmpegWASM;
const { fetchFile } = FFmpegUtil;
const ffmpeg = new FFmpeg();

postVideo.onchange = e => {
  clearAllMedia();
  unblockAll();
  block("visual");

  const file = e.target.files[0];
  if (!file) return;

  if (file.size > 15 * 1024 * 1024) {
    alert("Video must be under 15MB");
    return;
  }

  state.video = file;
  renderVideoEditor(file);
};

function renderVideoEditor(file) {
  previewArea.innerHTML = `
    <video id="videoPreview" class="w-100 mb-2" controls></video>

    <label class="form-label">Start</label>
    <input id="startRange" type="range" min="0" step="0.1" class="form-range">

    <label class="form-label">End</label>
    <input id="endRange" type="range" step="0.1" class="form-range">

    <button class="btn btn-sm btn-primary mt-2" onclick="trimVideo()">Trim Video</button>
  `;

  const videoEl = document.getElementById("videoPreview");
  videoEl.src = URL.createObjectURL(file);

  videoEl.onloadedmetadata = () => {
    state.videoDuration = videoEl.duration;
    state.videoStart = 0;
    state.videoEnd = videoEl.duration;

    startRange.max = state.videoDuration;
    endRange.max = state.videoDuration;
    endRange.value = state.videoDuration;

    startRange.oninput = e => state.videoStart = parseFloat(e.target.value);
    endRange.oninput = e => state.videoEnd = parseFloat(e.target.value);
  };
}

async function trimVideo() {
  if (state.videoEnd <= state.videoStart) {
    alert("End time must be greater than start time");
    return;
  }

  await ffmpeg.load();
  await ffmpeg.writeFile("input.mp4", await fetchFile(state.video));

  await ffmpeg.exec([
    "-ss", String(state.videoStart),
    "-to", String(state.videoEnd),
    "-i", "input.mp4",
    "-c:v", "libx264",
    "-preset", "veryfast",
    "-crf", "28",
    "-movflags", "+faststart",
    "output.mp4"
  ]);

  const data = await ffmpeg.readFile("output.mp4");
  state.trimmedVideo = new File([data.buffer], "video.mp4", {
    type: "video/mp4"
  });

  renderPreview();
}

/* ---------------- AUDIO ---------------- */
postAudio.onchange = e => {
  clearAllMedia();
  unblockAll();
  block("audio");

  state.audio = e.target.files[0];
  renderPreview();
};

audioCover.onchange = e => {
  state.audioCover = e.target.files[0];
  renderPreview();
};

/* ---------------- PREVIEW ---------------- */
function renderPreview() {
  previewArea.innerHTML = "";

  if (state.images.length) {
    state.images.forEach(img => {
      const url = URL.createObjectURL(img);
      previewArea.innerHTML += `<img src="${url}" width="80" class="me-2 mb-2">`;
    });
  }

  if (state.trimmedVideo) {
    const url = URL.createObjectURL(state.trimmedVideo);
    previewArea.innerHTML += `<video src="${url}" controls class="w-100"></video>`;
  }

  if (state.audio) {
    previewArea.innerHTML += `
      ${state.audioCover ? `<img src="${URL.createObjectURL(state.audioCover)}" width="120" class="mb-2">` : ""}
      <audio controls src="${URL.createObjectURL(state.audio)}"></audio>
    `;
  }
}

/* ---------------- EMOJI ---------------- */
function openEmojiPicker() {
  postText.value += " 😊🔥🎉";
}

/* ---------------- SUBMIT ---------------- */
async function submitPost() {
  if (!validateRules()) return;

  const fd = new FormData();
  fd.append("action", "createPost");
  fd.append("text", postText.value);

  state.images.forEach(img => fd.append("images[]", img));
  if (state.trimmedVideo) fd.append("video", state.trimmedVideo);
  if (state.audio) fd.append("audio", state.audio);
  if (state.audioCover) fd.append("cover", state.audioCover);

  const res = await fetch("/api/post", {
    method: "POST",
    body: fd
  });

  const json = await res.json();
  alert(json.message);
}
</script>

</body>
</html>
