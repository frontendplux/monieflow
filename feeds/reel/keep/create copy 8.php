<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>MonieFlow • Create Reel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" />

  <style>
    :root {
      --primary: #1877f2;
      --accent: #ffcc00;
      --danger: #ff3366;
    }

    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      background: black;
      color: white;
      font-family: system-ui, sans-serif;
      touch-action: manipulation;
    }

    #preview {
      position: fixed;
      inset: 0;
      width: 100vw;
      height: 100vh;
      object-fit: cover;
      object-position: center;
      background: #111;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, transparent 40%, transparent 60%, rgba(0,0,0,0.6) 100%);
      pointer-events: none;
      z-index: 2;
    }

    .top-bar {
      position: relative;
      z-index: 10;
      padding: 16px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .music-btn {
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(12px);
      padding: 8px 18px;
      border-radius: 999px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }

    .bottom-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      padding: 24px 32px 48px;
      display: flex;
      justify-content: space-around;
      align-items: center;
      z-index: 10;
    }

    .record-ring {
      position: relative;
      width: 90px;
      height: 90px;
      border: 5px solid white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .record-dot {
      width: 100%;
      height: 100%;
      background: var(--danger);
      border-radius: 50%;
      transition: all 0.3s ease;
    }

    .record-dot.rec {
      transform: scale(0.6);
      border-radius: 14px;
      background: #ff1a4d;
    }

    .spinner {
      position: absolute;
      inset: 0;
      border: 5px solid rgba(255,255,255,0.2);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      opacity: 0;
    }

    .rec .spinner { opacity: 1; }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* Bottom Sheet */
    #sheetBackdrop {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.75);
      z-index: 98;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.4s;
    }

    #sheetBackdrop.active { opacity: 1; pointer-events: all; }

    #bottomSheet {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(20,20,25,0.96);
      backdrop-filter: blur(16px);
      border-top-left-radius: 28px;
      border-top-right-radius: 28px;
      z-index: 99;
      height: 56vh;
      transform: translateY(100%);
      transition: transform 0.45s cubic-bezier(0.32,0.72,0,1);
      box-shadow: 0 -16px 48px rgba(0,0,0,0.7);
    }

    #bottomSheet.active { transform: translateY(0); }

    .sheet-header {
      padding: 14px;
      text-align: center;
      font-size: 1.1rem;
      font-weight: 700;
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .sheet-content {
      padding: 24px;
      overflow-y: auto;
      height: calc(100% - 120px);
    }

    .btn-mflow {
      background: var(--primary);
      border: none;
      color: white;
      border-radius: 999px;
      padding: 14px;
      font-weight: 600;
      flex: 1;
    }

    .btn-outline-light {
      border: 1px solid rgba(255,255,255,0.4);
      color: white;
      border-radius: 999px;
      padding: 14px;
      font-weight: 600;
      flex: 1;
    }
  </style>
</head>
<body>

<video id="preview" autoplay playsinline muted></video>
<div class="overlay"></div>

<div class="top-bar">
  <i class="ri-close-line fs-1 pointer" onclick="history.back()"></i>
  <div class="music-btn" id="musicPicker">
    <i class="ri-music-fill"></i> Add Music
  </div>
  <i class="ri-settings-4-line fs-1"></i>
</div>

<div class="bottom-bar">
  <div onclick="document.getElementById('vidUpload').click()" class="text-center pointer">
    <div style="width:52px;height:52px;border-radius:12px;border:2px solid white;background:#222 url('https://picsum.photos/140') center/cover;"></div>
    <div class="small mt-1">Upload</div>
  </div>

  <div class="record-ring" id="recTrigger">
    <div class="record-dot" id="recDot"></div>
    <div class="spinner"></div>
  </div>

  <div id="finishTrigger" class="text-center pointer">
    <i class="ri-checkbox-circle-fill text-success" style="font-size:54px;"></i>
    <div class="small mt-1">Finish</div>
  </div>
</div>

<!-- Bottom Sheet -->
<div id="sheetBackdrop"></div>
<div id="bottomSheet">
  <div class="sheet-header" id="sheetTitle">Edit Reel</div>
  <div class="sheet-content">

    <!-- Edit controls -->
    <div id="editControls">
      <label class="form-label mt-2">Start <span id="startVal">0.0</span>s</label>
      <input type="range" id="trimStart" class="form-range" min="0" value="0" step="0.1" />

      <label class="form-label mt-3">End <span id="endVal">15.0</span>s</label>
      <input type="range" id="trimEnd" class="form-range" min="0" value="15" step="0.1" />

      <label class="form-label mt-4">Video Volume</label>
      <input type="range" id="vidVolume" class="form-range" min="0" max="1" step="0.05" value="1" />

      <div class="mt-4">
        <strong>Speed</strong>
        <div class="d-flex flex-wrap gap-2 mt-2">
          <button class="btn btn-sm btn-outline-light speed-btn" data-rate="0.5">0.5×</button>
          <button class="btn btn-sm btn-outline-light speed-btn active" data-rate="1">1×</button>
          <button class="btn btn-sm btn-outline-light speed-btn" data-rate="2">2×</button>
          <button class="btn btn-sm btn-outline-light speed-btn" data-rate="3">3×</button>
        </div>
      </div>
    </div>

    <!-- Music step (hidden initially) -->
    <div id="musicControls" style="display:none; text-align:center; padding: 60px 20px;">
      <i class="ri-music-note-2-line" style="font-size:90px; opacity:0.7;"></i>
      <h5 class="mt-4">Add Music Track</h5>
      <p class="text-white-50 mb-4">Choose audio from your phone</p>
      <button class="btn btn-lg btn-primary px-5" onclick="document.getElementById('musicInput').click()">
        Select Music File
      </button>
      <input type="file" id="musicInput" accept="audio/*" hidden />
      <div id="musicStatus" class="mt-4 small text-white-75"></div>
    </div>
  </div>

  <div class="sheet-footer d-flex gap-3 p-4 border-top border-secondary">
    <button class="btn-outline-light" onclick="closeSheet()">Cancel</button>
    <button class="btn-mflow" id="actionBtn">Next</button>
  </div>
</div>

<!-- Hidden file input -->
<input type="file" id="vidUpload" accept="video/*" hidden />

<script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.12.10/dist/umd/ffmpeg.js"></script>
<script>
  // ────────────────────────────────────────────────
  //  Core state
  // ────────────────────────────────────────────────

  let stream = null;
  let recorder = null;
  let chunks = [];
  let isRec = false;
  let facing = "user";
  const MAX_REC = 25;

  let videoBlob = null;
  let previewURL = null;
  let musicFile = null;

  let startSec = 0;
  let endSec = 15;
  let speed = 1;

  const video = document.getElementById("preview");
  const recDot = document.getElementById("recDot");
  const recTrigger = document.getElementById("recTrigger");
  const finishTrigger = document.getElementById("finishTrigger");
  const backdrop = document.getElementById("sheetBackdrop");
  const sheet = document.getElementById("bottomSheet");
  const title = document.getElementById("sheetTitle");
  const actionBtn = document.getElementById("actionBtn");
  const editCtrls = document.getElementById("editControls");
  const musicCtrls = document.getElementById("musicControls");

  // ────────────────────────────────────────────────
  //  Camera
  // ────────────────────────────────────────────────

  async function startCamera() {
    try {
      if (stream) stream.getTracks().forEach(t => t.stop());
      stream = await navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: facing,
          width: { ideal: 1280 },
          height: { ideal: 720 },
          aspectRatio: { ideal: 9/16 }
        },
        audio: true
      });
      video.srcObject = stream;
      video.muted = true;
      video.playsInline = true;
    } catch (e) {
      alert("Cannot access camera / microphone");
      console.error(e);
    }
  }

  startCamera();

  // Flip
  document.querySelector("#flipCamera")?.addEventListener("click", () => {
    facing = facing === "user" ? "environment" : "user";
    startCamera();
  });

  // ────────────────────────────────────────────────
  //  Recording
  // ────────────────────────────────────────────────

  recTrigger.onclick = () => {
    if (isRec) stopRec();
    else startRec();
  };

  function startRec() {
    if (!stream) return;
    isRec = true;
    recDot.classList.add("rec");
    chunks = [];

    recorder = new MediaRecorder(stream);
    recorder.ondataavailable = e => e.data.size > 0 && chunks.push(e.data);
    recorder.onstop = finishRecording;

    recorder.start();
    setTimeout(stopRec, MAX_REC * 1000);
  }

  function stopRec() {
    isRec = false;
    recDot.classList.remove("rec");
    if (recorder?.state !== "inactive") recorder.stop();
  }

  function finishRecording() {
    videoBlob = new Blob(chunks, { type: "video/webm" });
    previewURL = URL.createObjectURL(videoBlob);

    video.srcObject = null;
    video.src = previewURL;
    video.muted = false;
    video.loop = true;
    video.play();

    showSheet("Edit Reel");
  }

  // ────────────────────────────────────────────────
  //  Sheet & Steps
  // ────────────────────────────────────────────────

  function showSheet(titleText = "Edit Reel") {
    title.textContent = titleText;
    backdrop.classList.add("active");
    sheet.classList.add("active");
    editCtrls.style.display = "block";
    musicCtrls.style.display = "none";
    actionBtn.textContent = "Add Music →";
    actionBtn.onclick = openMusicStep;
  }

  function closeSheet() {
    backdrop.classList.remove("active");
    sheet.classList.remove("active");
    video.pause();
  }

  function openMusicStep() {
    editCtrls.style.display = "none";
    musicCtrls.style.display = "block";
    title.textContent = "Add Music";
    actionBtn.textContent = "Upload Reel";
    actionBtn.onclick = finalizeAndUpload;
  }

  finishTrigger.onclick = () => {
    if (videoBlob) showSheet();
    else alert("Record or upload first");
  };

  // ────────────────────────────────────────────────
  //  Trim & Volume
  // ────────────────────────────────────────────────

  const trimStartEl = document.getElementById("trimStart");
  const trimEndEl   = document.getElementById("trimEnd");
  const vidVolEl    = document.getElementById("vidVolume");

  trimStartEl.oninput = trimEndEl.oninput = () => {
    startSec = +trimStartEl.value;
    endSec   = +trimEndEl.value;
    document.getElementById("startVal").textContent = startSec.toFixed(1);
    document.getElementById("endVal").textContent   = endSec.toFixed(1);
    syncTrimPreview();
  };

  vidVolEl.oninput = e => video.volume = +e.target.value;

  let trimTimer = null;
  function syncTrimPreview() {
    if (trimTimer) clearInterval(trimTimer);
    if (endSec <= startSec) return;

    video.currentTime = startSec;
    video.play().catch(()=>{});

    trimTimer = setInterval(() => {
      if (video.currentTime >= endSec - 0.1) {
        video.currentTime = startSec;
      }
    }, 120);
  }

  // Speed buttons
  document.querySelectorAll(".speed-btn").forEach(btn => {
    btn.onclick = () => {
      document.querySelectorAll(".speed-btn").forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      speed = +btn.dataset.rate;
      video.playbackRate = speed;
    };
  });

  // ────────────────────────────────────────────────
  //  Music
  // ────────────────────────────────────────────────

  document.getElementById("musicInput").onchange = e => {
    const file = e.target.files[0];
    if (!file) return;

    musicFile = file;
    const url = URL.createObjectURL(file);
    audioTrack.src = url;
    audioTrack.loop = true;

    document.getElementById("musicStatus").textContent = `Selected: ${file.name}`;
  };

  // ────────────────────────────────────────────────
  //  Final Export + Upload
  // ────────────────────────────────────────────────

  async function finalizeAndUpload() {
    if (!videoBlob) return alert("No video");

    const start = startSec;
    const dur = 15;

    try {
      const { FFmpeg } = FFmpegWASM;
      const ffmpeg = new FFmpeg();
      await ffmpeg.load();

      await ffmpeg.writeFile("in.webm", await fetchFile(videoBlob));

      let cmd = ["-ss", start.toFixed(2), "-i", "in.webm"];

      if (musicFile) {
        await ffmpeg.writeFile("music.mp3", await fetchFile(musicFile));
        cmd.push("-i", "music.mp3");
      }

      cmd = cmd.concat([
        "-filter_complex", musicFile
          ? "[0:a]volume=1[a0];[1:a]volume=0.7[a1];[a0][a1]amix=inputs=2:duration=shortest[a]"
          : "volume=1[a]",
        "-map", "0:v", "-map", "[a]",
        "-t", dur.toString(),
        "-c:v", "libx264", "-preset", "veryfast", "-crf", "23",
        "-c:a", "aac", "-b:a", "128k",
        "-movflags", "+faststart",
        "out.mp4"
      ]);

      await ffmpeg.exec(cmd);

      const data = await ffmpeg.readFile("out.mp4");
      const finalBlob = new Blob([data.buffer], { type: "video/mp4" });

      // Preview result
      if (previewURL) URL.revokeObjectURL(previewURL);
      previewURL = URL.createObjectURL(finalBlob);
      video.src = previewURL;
      video.loop = false;
      video.controls = true;
      video.play();

      // Upload
      const fd = new FormData();
      fd.append("video", finalBlob, "reel.mp4");

      const res = await fetch("upload.php", { method: "POST", body: fd });
      const txt = await res.text();

      alert(res.ok ? `Done! ${txt}` : `Upload failed: ${txt}`);

      closeSheet();

    } catch (e) {
      console.error(e);
      alert("Processing failed – try shorter clip");
    }
  }

  // ────────────────────────────────────────────────
  //  Video upload from gallery
  // ────────────────────────────────────────────────

  document.getElementById("vidUpload").onchange = e => {
    const file = e.target.files[0];
    if (!file || !file.type.startsWith("video")) return;

    videoBlob = file;
    previewURL = URL.createObjectURL(file);
    video.srcObject = null;
    video.src = previewURL;
    video.muted = false;
    video.loop = true;
    video.play();

    video.onloadedmetadata = () => {
      const d = video.duration || 15;
      trimStart.max = d;
      trimEnd.max = d;
      trimEnd.value = Math.min(15, d);
      syncTrimPreview();
      showSheet();
    };
  };
</script>
</body>
</html>