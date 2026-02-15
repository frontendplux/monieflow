<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Create Reel | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root{
            --mflow-blue: #1877f2;
            --mflow-gold: #ffcc00;
        }

        body, html {
            margin: 0; padding: 0;
            height: 100%; width: 100%;
            background: #000;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
            touch-action: manipulation;
        }

        video#cameraFeed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            position: fixed;
            inset: 0;
            transform: translateZ(0);
            z-index: 0;
        }

        .viewfinder {
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(0,0,0,0.3), transparent 20%, transparent 80%, rgba(0,0,0,0.6));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1;
            pointer-events: none;
        }

        .top-nav, .footer-controls, .side-tools, .monie-settings {
            pointer-events: auto;
        }

        .top-nav {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .music-picker {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .side-tools {
            position: absolute;
            right: 15px;
            top: 100px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .tool-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            text-shadow: 0 1px 3px rgba(0,0,0,0.7);
        }

        .tool-btn i { font-size: 28px; }
        .tool-btn span { font-size: 10px; font-weight: 700; text-transform: uppercase; }

        .footer-controls {
            padding: 20px 30px 40px;
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        .upload-preview {
            width: 48px; height: 48px;
            border-radius: 10px;
            border: 2px solid white;
            background: url('https://picsum.photos/120') center/cover;
        }

        .record-btn-container {
            position: relative;
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 5px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .record-btn {
            width: 100%; height: 100%;
            background: #ff4d6d;
            border-radius: 50%;
            transition: all 0.25s ease;
        }

        .record-btn.recording {
            transform: scale(0.65);
            border-radius: 12px;
            background: #ff3366;
        }

        .record-spinner {
            position: absolute;
            inset: 4px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .record-btn.recording + .record-spinner {
            opacity: 1;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .monie-settings {
            background: rgba(0,0,0,0.65);
            border-radius: 16px;
            padding: 12px 18px;
            margin: 0 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(255,204,0,0.4);
        }

        .bottom-sheet-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.65);
            z-index: 18;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        .bottom-sheet-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .bottom-sheet {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: rgba(18,18,23,0.98);
            backdrop-filter: blur(12px);
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            z-index: 19;
            height: 48vh;
            transform: translateY(100%);
            transition: transform 0.42s cubic-bezier(0.32, 0.72, 0, 1);
            box-shadow: 0 -10px 40px rgba(0,0,0,0.6);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .bottom-sheet.active {
            transform: translateY(0);
        }

        .sheet-handle {
            width: 42px;
            height: 5px;
            background: rgba(255,255,255,0.45);
            border-radius: 999px;
            margin: 14px auto 8px;
        }

        .sheet-header {
            padding: 8px 20px 12px;
            text-align: center;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sheet-content {
            padding: 20px 24px;
            flex: 1;
            overflow-y: auto;
        }

        .sheet-content label {
            display: block;
            margin: 18px 0 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .sheet-actions {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            gap: 12px;
        }

        .btn-mflow {
            background: var(--mflow-blue);
            border: none;
            color: white;
            flex: 1;
            border-radius: 50px;
            padding: 14px;
            font-weight: 600;
            font-size: 15px;
        }

        .btn-outline-light {
            border-color: rgba(255,255,255,0.4);
            color: white;
        }
      
    </style>
</head>
<body>

    <video id="cameraFeed" autoplay playsinline muted></video>

    <div class="viewfinder">
        <div class="top-nav">
            <i class="ri-arrow-left-s-line fs-1" onclick="history.back()"></i>
            <!-- <div class="music-picker" onclick="alert('Music selection coming soon')">
                <i class="ri-music-fill"></i> Add Sound
            </div> -->
            <div></div>
            <!-- <i onclick="alert('yes')" class="ri-settings-4-line fs-2"></i> -->
        </div>

        <div class="side-tools">
            <div class="tool-btn" id="flipBtn"><i class="ri-repeat-line"></i><span>Flip</span></div>
            <!-- <div class="tool-btn"><i class="ri-speed-line"></i><span>Speed</span></div> -->
            <div class="tool-btn"><i class="ri-magic-line"></i><span>Filters</span></div>
            <!-- <div class="tool-btn"><i class="ri-timer-line"></i><span>Timer</span></div> -->
            <!-- <div class="tool-btn text-warning"><i class="ri-coin-line"></i><span>Price</span></div> -->
        </div>

        <div>
            <!-- <div class="monie-settings">
                <div class="d-flex align-items-center">
                    <i class="ri-lock-password-line text-warning me-2 fs-4"></i>
                    <div>
                        <div class="small fw-bold">Paid Access</div>
                        <div class="text-white-50" style="font-size:10px;">Set MC price to watch</div>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="payToView">
                </div>
            </div> -->

            <div class="footer-controls">
                <div class="tool-btn" onclick="document.getElementById('reelUpload').click()">
                    <div class="upload-preview"></div>
                    <span class="mt-1">Upload</span>
                    <input type="file" id="reelUpload" hidden accept="video/*">
                </div>

                <div class="record-btn-container" id="recordTrigger">
                    <div class="record-btn" id="recButton"></div>
                    <div class="record-spinner"></div>
                </div>

                <div class="tool-btn" id="doneBtn">
                    <i class="ri-checkbox-circle-fill text-success" style="font-size:48px;"></i>
                    <span>Done</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-sheet-backdrop" id="sheetBackdrop" onclick="hideBottomSheet()"></div>

    <div class="bottom-sheet" id="bottomSheet">
        <div class="sheet-handle"></div>
        <div class="sheet-header">Edit Your Reel (exports as 15s)</div>
        <div class="sheet-content">
            <label>Trim Start: <span id="startVal">0.0</span>s</label>
            <input type="range" id="trimStart" min="0" value="0" step="0.1">

            <label>Trim End: <span id="endVal">10.0</span>s</label>
            <input type="range" id="trimEnd" min="0" value="10" step="0.1">

            <label>Video Volume</label>
            <input type="range" id="videoVolume" min="0" max="1" step="0.05" value="1">

            <label>Music Volume</label>
            <input type="range" id="musicVolume" min="0" max="1" step="0.05" value="0.7">
        </div>
        <div class="sheet-actions">
            <button class="btn btn-outline-light flex-grow-1 py-3" onclick="hideBottomSheet()">Cancel</button>
            <button class="btn-mflow" onclick="exportFinalVideo()">Export & Upload (15s)</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.12.10/dist/umd/ffmpeg.js"></script>
    <script>
        let stream = null;
        let mediaRecorder = null;
        let recordedChunks = [];
        let isRecording = false;
        let facingMode = "user";
        const maxRecordDuration = 20;
        let timer = null;
        const bgm = new Audio();
        let musicFile = null;
        let recordedBlob = null;
        let currentPreviewUrl = null;
        let trimWatcher = null;

        const video = document.getElementById("cameraFeed");
        const recButton = document.getElementById("recButton");
        const recordTrigger = document.getElementById("recordTrigger");
        const flipBtn = document.getElementById("flipBtn");
        const doneBtn = document.getElementById("doneBtn");

        const trimStart = document.getElementById("trimStart");
        const trimEnd   = document.getElementById("trimEnd");
        const videoVolume = document.getElementById("videoVolume");
        const musicVolume = document.getElementById("musicVolume");

        const FINAL_DURATION = 15;  // fixed 15 seconds output

        function revokeCurrentUrl() {
            if (currentPreviewUrl) {
                URL.revokeObjectURL(currentPreviewUrl);
                currentPreviewUrl = null;
            }
        }

        function updateTrimDisplay() {
            document.getElementById("startVal").textContent = Number(trimStart.value).toFixed(1);
            document.getElementById("endVal").textContent   = Number(trimEnd.value).toFixed(1);
        }

    

        async function startCamera() {
    try {
        if (stream) stream.getTracks().forEach(t => t.stop());

        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: true
        });

        video.srcObject = stream;
        video.muted = true;
        video.playsInline = true;

        // Detect if front camera → decide mirroring
        const track = stream.getVideoTracks()[0];
        const settings = track.getSettings();
        const isFront = settings.facingMode === 'user';   // or !settings.facingMode for fallback

        video.style.transform = isFront ? 'scaleX(-1)' : 'scaleX(1)';
        video.style.webkitTransform = isFront ? 'scaleX(-1)' : 'scaleX(1)';
    } catch (err) {
        alert("Camera/mic access denied or not available");
        console.error(err);
    }
}

        startCamera();

        flipBtn.onclick = () => {
            facingMode = facingMode === "user" ? "environment" : "user";
            startCamera();
        };

        recordTrigger.onclick = () => {
            if (!isRecording) startRecording();
            else stopRecording();
        };

        function startRecording() {
            if (!stream) return;
            isRecording = true;
            recButton.classList.add("recording");
            recordedChunks = [];
            recordedBlob = null;

            mediaRecorder = new MediaRecorder(stream, { mimeType: 'video/webm;codecs=vp9,opus' });
            mediaRecorder.ondataavailable = e => { if (e.data.size > 0) recordedChunks.push(e.data); };
            mediaRecorder.onstop = onRecordingStopped;

            mediaRecorder.start();
            timer = setTimeout(stopRecording, maxRecordDuration * 1000);
        }

        function stopRecording() {
            isRecording = false;
            recButton.classList.remove("recording");
            clearTimeout(timer);
            if (mediaRecorder && mediaRecorder.state !== "inactive") mediaRecorder.stop();
        }

        function onRecordingStopped() {
            revokeCurrentUrl();
            recordedBlob = new Blob(recordedChunks, { type: "video/webm" });
            currentPreviewUrl = URL.createObjectURL(recordedBlob);

            video.srcObject = null;
            video.src = currentPreviewUrl;
            video.muted = false;
            video.loop = true;
            video.controls = false;
            video.play().catch(() => {});

            video.onloadedmetadata = () => {
                initializeTrimSliders();
                applyTrimPreview();
            };
            showBottomSheet();
        }

        function initializeTrimSliders() {
            const dur = isNaN(video.duration) ? maxRecordDuration : video.duration;
            trimStart.max = dur;
            trimEnd.max = dur;
            trimStart.value = 0;
            trimEnd.value = Math.min(FINAL_DURATION, dur);
            updateTrimDisplay();
        }

        function applyTrimPreview() {
            if (trimWatcher) clearInterval(trimWatcher);

            const start = Number(trimStart.value);
            const end = Number(trimEnd.value);
            if (end <= start + 0.2 || isNaN(video.duration)) return;

            video.currentTime = start;
            video.play().catch(() => {});

            trimWatcher = setInterval(() => {
                if (video.currentTime >= end - 0.1) {
                    video.currentTime = start;
                }
            }, 180);
        }

        function stopTrimPreview() {
            if (trimWatcher) {
                clearInterval(trimWatcher);
                trimWatcher = null;
            }
            if (!video.loop) video.pause();
        }

        trimStart.oninput = trimEnd.oninput = () => {
            updateTrimDisplay();
            applyTrimPreview();
        };

        videoVolume.oninput = e => video.volume = Number(e.target.value);
        musicVolume.oninput = e => bgm.volume = Number(e.target.value);

        document.getElementById("reelUpload").onchange = e => {
            const file = e.target.files?.[0];
            if (!file || !file.type.startsWith("video/")) return;

            revokeCurrentUrl();
            currentPreviewUrl = URL.createObjectURL(file);
            video.srcObject = null;
            video.src = currentPreviewUrl;
            video.muted = false;
            video.loop = true;
            video.play().catch(() => {});

            video.onloadedmetadata = () => {
                initializeTrimSliders();
                applyTrimPreview();
            };
            showBottomSheet();
        };

        function showBottomSheet() {
            document.getElementById("bottomSheet").classList.add("active");
            document.getElementById("sheetBackdrop").classList.add("active");
        }

        function hideBottomSheet() {
            document.getElementById("bottomSheet").classList.remove("active");
            document.getElementById("sheetBackdrop").classList.remove("active");
            stopTrimPreview();
        }

        doneBtn.onclick = () => {
            if (recordedBlob || currentPreviewUrl) showBottomSheet();
            else alert("Record or upload something first");
        };

        const { FFmpeg } = FFmpegWASM;
        let ffmpeg = null;
        let ffmpegReady = false;

        async function loadFFmpeg() {
            if (ffmpegReady) return;
            try {
                ffmpeg = new FFmpeg();
                await ffmpeg.load({
                    coreURL: "https://cdn.jsdelivr.net/npm/@ffmpeg/core@0.12.10/dist/umd/ffmpeg-core.js"
                });
                ffmpegReady = true;
            } catch (err) {
                alert("Video processor failed to load");
                console.error(err);
            }
        }

        recordTrigger.addEventListener("click", loadFFmpeg, { once: true });

        async function exportFinalVideo() {
            if (!recordedBlob) return alert("No video to export");

            const userStart = Number(trimStart.value);
            const userEnd   = Number(trimEnd.value);
            const userDur   = userEnd - userStart;

            const vVol = Number(videoVolume.value);
            const mVol = Number(musicVolume.value);

            if (!ffmpegReady) {
                await loadFFmpeg();
                if (!ffmpegReady) return;
            }

            try {
                await ffmpeg.writeFile("input.webm", await fetchFile(recordedBlob));

                let cmd = [
                    "-ss", userStart.toFixed(2),
                    "-i", "input.webm"
                ];

                let afilter = `volume=${vVol},loudnorm=I=-16:TP=-1.5:LRA=11[audio]`;

                if (musicFile) {
                    await ffmpeg.writeFile("bgm.mp3", await fetchFile(musicFile));
                    cmd.push("-i", "bgm.mp3");
                    afilter = `[0:a]volume=${vVol},loudnorm[a0];[1:a]volume=${mVol},loudnorm[a1];[a0][a1]amix=inputs=2:duration=shortest[audio]`;
                }

                // Always output exactly 15s
                // If userDur < 15 → freeze last frame with tpad
                // If userDur > 15 → cut to 15s
                const padFilter = userDur < FINAL_DURATION 
                    ? `tpad=stop_mode=clone:stop_duration=${(FINAL_DURATION - userDur).toFixed(2)}` 
                    : "";

                const videoFilter = padFilter 
                    ? `[0:v]${padFilter}[v]` 
                    : "[0:v][v]";

                cmd = cmd.concat([
                    "-filter_complex", `${afilter};${videoFilter}`,
                    "-map", "[v]",
                    "-map", "[audio]",
                    "-t", FINAL_DURATION.toFixed(2),          // enforce max 15s
                    "-c:v", "libx264", "-preset", "veryfast", "-crf", "24",
                    "-c:a", "aac", "-b:a", "128k",
                    "-movflags", "+faststart",
                    "output.mp4"
                ]);

                await ffmpeg.exec(cmd);

                const data = await ffmpeg.readFile("output.mp4");
                const finalBlob = new Blob([data.buffer], { type: "video/mp4" });

                revokeCurrentUrl();
                currentPreviewUrl = URL.createObjectURL(finalBlob);
                video.src = currentPreviewUrl;
                video.loop = false;
                video.controls = true;
                video.play();

                uploadToServer(finalBlob);
                hideBottomSheet();

            } catch (err) {
                console.error("Export failed:", err);
                alert("Export failed – try a shorter selection or check device storage");
            }
        }

        async function uploadToServer(blob) {
            try {
                const formData = new FormData();
                formData.append("video", blob, "reel-" + Date.now() + ".mp4");

                const res = await fetch("upload.php", { method: "POST", body: formData });
                const msg = await res.text();
                alert(res.ok ? `Uploaded! ${msg}` : "Upload failed – server error");
            } catch (err) {
                alert("Upload error – check internet connection");
            }
        }

        // Init
        video.volume = 1;
        bgm.volume = 0.7;
    </script>
    <script>
        // ────────────────────────────────────────────────
        //  MONIEFLOW REEL - 15s fixed + speed + filters + music
        // ────────────────────────────────────────────────

        const FINAL_DURATION = 15;
        let currentSpeed = 1.0;
        let currentFilter = "none";

        // ... (keep all previous variables: stream, mediaRecorder, recordedBlob, etc.)

        // ── Speed & Filter Handlers ───────────────────────────────
        document.querySelectorAll('[data-speed]').forEach(btn => {
            btn.onclick = () => {
                document.querySelectorAll('[data-speed]').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentSpeed = parseFloat(btn.dataset.speed);
                video.playbackRate = currentSpeed;
            };
        });

        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.onclick = () => {
                document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.dataset.filter;
                applyFilter();
            };
        });

        function applyFilter() {
            video.style.filter = currentFilter;
        }

        // ── Music Upload ─────────────────────────────────────────────
        document.getElementById("musicPicker").onclick = () => {
            document.getElementById("musicUpload").click();
        };

        document.getElementById("musicUpload").onchange = e => {
            const file = e.target.files[0];
            if (!file || !file.type.startsWith("audio/")) return;

            const url = URL.createObjectURL(file);
            bgm.src = url;
            bgm.loop = true;
            musicFile = file;

            if (!video.paused) {
                bgm.currentTime = video.currentTime;
                bgm.play().catch(() => {});
            }

            alert("Music added! Adjust volume in editor.");
        };

        // ── In preview functions (onRecordingStopped & upload onchange) ──
        // Add this after video.play():
        video.playbackRate = currentSpeed;
        applyFilter();

        // ── Export (with speed & filter applied via FFmpeg) ───────────
        async function exportFinalVideo() {
            if (!recordedBlob) return alert("No video recorded or uploaded");

            const start = Number(trimStart.value);
            let dur = FINAL_DURATION;

            const vVol = Number(videoVolume.value);
            const mVol = Number(musicVolume.value);

            await loadFFmpeg();

            try {
                await ffmpeg.writeFile("input.webm", await fetchFile(recordedBlob));

                let cmd = [
                    "-ss", start.toFixed(2),
                    "-i", "input.webm"
                ];

                let vfilter = "";
                if (currentFilter !== "none") vfilter += currentFilter + ",";
                vfilter += `setpts=${1/currentSpeed}*PTS`;  // speed adjustment

                let afilter = `volume=${vVol},loudnorm=I=-16:TP=-1.5:LRA=11[audio]`;

                if (musicFile) {
                    await ffmpeg.writeFile("music.mp3", await fetchFile(musicFile));
                    cmd.push("-i", "music.mp3");
                    afilter = `[0:a]volume=${vVol},loudnorm[a0];[1:a]volume=${mVol},loudnorm[a1];[a0][a1]amix=inputs=2:duration=shortest[audio]`;
                }

                // Pad/freeze if needed
                const pad = `tpad=stop_mode=clone:stop_duration=${dur.toFixed(2)}`;

                cmd = cmd.concat([
                    "-filter_complex", `[0:v]${vfilter}${pad}[v];${afilter}`,
                    "-map", "[v]",
                    "-map", "[audio]",
                    "-t", dur.toFixed(2),
                    "-c:v", "libx264", "-preset", "veryfast", "-crf", "23",
                    "-c:a", "aac", "-b:a", "128k",
                    "-movflags", "+faststart",
                    "output.mp4"
                ]);

                await ffmpeg.exec(cmd);

                const data = await ffmpeg.readFile("output.mp4");
                const finalBlob = new Blob([data.buffer], { type: "video/mp4" });

                // Preview result
                revokeCurrentUrl();
                currentPreviewUrl = URL.createObjectURL(finalBlob);
                video.src = currentPreviewUrl;
                video.loop = false;
                video.controls = true;
                video.play();

                uploadToServer(finalBlob);
                hideBottomSheet();

            } catch (err) {
                console.error(err);
                alert("Export failed. Try shorter clip or check console.");
            }
        }

        // ... keep all other functions (startCamera, recording, trim preview, uploadToServer, etc.) ...

        // Init
        video.volume = 1;
        bgm.volume = 0.7;
    </script>
</body>
</html>