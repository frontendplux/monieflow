<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
  <title>Create Reel | monieFlow</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"/>
  
  <style>
    :root {
      --record-red: #ff3366;
      --ring-bg: rgba(255, 51, 102, 0.25);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body {
      height:100%;
      width:100%;
      background:#000;
      color:#fff;
      font-family: system-ui, -apple-system, sans-serif;
      overflow:hidden;
      touch-action: manipulation;
    }
    video#cameraFeed {
      position: fixed;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 1;
    }
    .overlay {
      position: fixed;
      inset: 0;
      z-index: 2;
      pointer-events: none;
      background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, transparent 15%, transparent 85%, rgba(0,0,0,0.6) 100%);
    }
    .top-bar {
      position: absolute;
      top: 0;
      left: 0; right: 0;
      padding: 16px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      pointer-events: auto;
      z-index: 10;
    }
    .side-tools {
      position: absolute;
      right: 16px;
      top: 80px;
      display: flex;
      flex-direction: column;
      gap: 32px;
      pointer-events: auto;
      z-index: 10;
    }
    .tool-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      color: white;
      text-shadow: 0 1px 4px #0008;
    }
    .tool-btn i { font-size: 32px; }
    .tool-btn span { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

    .bottom-controls {
      position: absolute;
      bottom: 0;
      left: 0; right: 0;
      padding: 32px 20px 60px;
      display: flex;
      justify-content: center;
      pointer-events: auto;
      z-index: 10;
    }
    .record-container {
      position: relative;
      width: 90px;
      height: 90px;
    }
    .record-btn {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: var(--record-red);
      border: 5px solid white;
      transition: all 0.25s ease;
    }
    .record-btn.recording {
      transform: scale(0.72);
      border-radius: 16px;
    }
    .progress-ring {
      position: absolute;
      inset: -5px;
      width: 100px;
      height: 100px;
      transform: rotate(-90deg);
    }
    .progress-circle {
      fill: none;
      stroke: var(--record-red);
      stroke-width: 5;
      stroke-dasharray: 283;
      stroke-dashoffset: 283;
      transition: stroke-dashoffset 11s linear;
    }
    .uploading {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 100;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.4s;
    }
    .uploading.show {
      opacity: 1;
      pointer-events: auto;
    }
  </style>
</head>
<body>

  <video id="cameraFeed" autoplay playsinline muted></video>
  <div class="overlay"></div>

  <div class="top-bar">
    <i class="ri-arrow-left-s-line fs-1" onclick="history.back()"></i>
    <div></div>
  </div>

  <div class="side-tools">
    <div class="tool-btn" id="flipCamera">
      <i class="ri-camera-switch-line"></i>
      <span>Flip</span>
    </div>
    <div class="tool-btn" onclick="document.getElementById('videoUpload').click()">
      <i class="ri-gallery-line" style="font-size:34px;"></i>
      <span>Gallery</span>
    </div>
    <input type="file" id="videoUpload" accept="video/*" hidden/>
  </div>

  <div class="bottom-controls">
    <div class="record-container" id="recordTrigger">
      <svg class="progress-ring" viewBox="0 0 100 100">
        <circle class="progress-circle" cx="50" cy="50" r="45"/>
      </svg>
      <button class="record-btn" id="recordBtn"></button>
    </div>
  </div>

  <div class="uploading" id="uploadingScreen">
    <div class="spinner-border text-light mb-3" style="width:3.5rem;height:3.5rem;"></div>
    <h5>Uploading your 11-second reel...</h5>
    <small class="text-white-50 mt-2">Please wait • Do not close the page</small>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.12.10/dist/umd/ffmpeg.js"></script>
  <script>
    const RECORD_SEC = 11;
    const CIRCUMFERENCE = 283;

    let stream = null;
    let recorder = null;
    let chunks = [];
    let isRecording = false;
    let facingMode = "user";

    const videoEl     = document.getElementById('cameraFeed');
    const recordBtn   = document.getElementById('recordBtn');
    const trigger     = document.getElementById('recordTrigger');
    const progress    = document.querySelector('.progress-circle');
    const uploading   = document.getElementById('uploadingScreen');
    const flipBtn     = document.getElementById('flipCamera');

    // ── Start Camera ────────────────────────────────────────
    async function startCamera() {
      if (stream) stream.getTracks().forEach(t => t.stop());
      try {
        stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
          audio: true
        });
        videoEl.srcObject = stream;
        const isFront = stream.getVideoTracks()[0].getSettings().facingMode === 'user';
        videoEl.style.transform = isFront ? 'scaleX(-1)' : 'scaleX(1)';
      } catch (err) {
        alert("Camera / microphone access denied.");
      }
    }

    startCamera();

    // ── Flip Camera ─────────────────────────────────────────
    flipBtn.onclick = () => {
      facingMode = facingMode === "user" ? "environment" : "user";
      startCamera();
    };

    // ── Record Logic ────────────────────────────────────────
    trigger.onclick = async () => {
      if (isRecording) return;
      isRecording = true;

      recordBtn.classList.add('recording');
      progress.style.transition = `stroke-dashoffset ${RECORD_SEC}s linear`;
      progress.style.strokeDashoffset = '0';

      chunks = [];
      recorder = new MediaRecorder(stream, { mimeType: 'video/webm;codecs=vp9,opus' });
      recorder.ondataavailable = e => e.data.size > 0 && chunks.push(e.data);
      recorder.onstop = onStop;

      recorder.start();
      setTimeout(() => recorder.state === 'recording' && recorder.stop(), RECORD_SEC * 1000 + 200);
    };

    function onStop() {
      isRecording = false;
      recordBtn.classList.remove('recording');
      progress.style.transition = 'none';
      progress.style.strokeDashoffset = CIRCUMFERENCE;

      const blob = new Blob(chunks, { type: 'video/webm' });
      processAndUpload(blob);
    }

    // ── Gallery Upload ──────────────────────────────────────
    document.getElementById('videoUpload').onchange = e => {
      const file = e.target.files?.[0];
      if (!file || !file.type.startsWith('video/')) return;
      processAndUpload(file);
    };

    // ── FFmpeg + Upload ─────────────────────────────────────
    let ffmpeg = null;

    async function initFFmpeg() {
      if (ffmpeg) return;
      const { FFmpeg } = FFmpegWASM;
      ffmpeg = new FFmpeg();
      await ffmpeg.load({
        coreURL: 'https://cdn.jsdelivr.net/npm/@ffmpeg/core@0.12.10/dist/umd/ffmpeg-core.js'
      });
    }

    async function processAndUpload(input) {
      uploading.classList.add('show');

      try {
        await initFFmpeg();

        await ffmpeg.writeFile('input.webm', await fetchFile(input));

        await ffmpeg.exec([
          '-i', 'input.webm',
          '-t', RECORD_SEC.toString(),
          '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '23',
          '-c:a', 'aac', '-b:a', '128k',
          '-movflags', '+faststart',
          'output.mp4'
        ]);

        const data = await ffmpeg.readFile('output.mp4');
        const finalBlob = new Blob([data.buffer], { type: 'video/mp4' });

        const formData = new FormData();
        formData.append('video', finalBlob, `reel-${Date.now()}.mp4`);

        const res = await fetch('upload.php', { method: 'POST', body: formData });

        if (res.ok) {
          alert('Reel uploaded successfully! 🎉');
        } else {
          alert('Upload failed. Server responded with error.');
        }
      } catch (err) {
        console.error(err);
        alert('Processing or upload failed. Try again.');
      } finally {
        uploading.classList.remove('show');
      }
    }

    async function fetchFile(source) {
      let buffer;
      if (source instanceof Blob || source instanceof File) {
        buffer = await source.arrayBuffer();
      } else {
        buffer = await (await fetch(source)).arrayBuffer();
      }
      return new Uint8Array(buffer);
    }

    // Reset progress ring when page loads
    progress.style.strokeDashoffset = CIRCUMFERENCE;
  </script>
</body>
</html>