<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Reel | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
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
        }

        video#cameraFeed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: fixed;
            top: 0; left: 0;
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

        /* Editor panel (hidden by default) */
        #editorPanel {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            z-index: 20;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            gap: 20px;
            overflow-y: auto;
        }

        #editorPanel button {
            padding: 12px 32px;
            background: var(--mflow-blue);
            border: none;
            border-radius: 30px;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <video id="cameraFeed" autoplay playsinline muted></video>

    <div class="viewfinder">
        <div class="top-nav">
            <i class="ri-close-line fs-1" onclick="history.back()"></i>
            <div class="music-picker" onclick="alert('Music picker coming soon – upload your track!')">
                <i class="ri-music-fill"></i> Add Sound
            </div>
            <i class="ri-settings-4-line fs-2"></i>
        </div>

        <div class="side-tools">
            <div class="tool-btn" id="flipBtn"><i class="ri-repeat-line"></i><span>Flip</span></div>
            <div class="tool-btn"><i class="ri-speed-line"></i><span>Speed</span></div>
            <div class="tool-btn"><i class="ri-magic-line"></i><span>Filters</span></div>
            <div class="tool-btn"><i class="ri-timer-line"></i><span>Timer</span></div>
            <div class="tool-btn text-warning"><i class="ri-coin-line"></i><span>Price</span></div>
        </div>

        <div>
            <div class="monie-settings">
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
            </div>

            <div class="footer-controls">
                <div class="tool-btn" onclick="document.getElementById('reelUpload').click()">
                    <div class="upload-preview"></div>
                    <span class="mt-1">Upload</span>
                    <input type="file" id="reelUpload" hidden accept="video/*">
                </div>

                <div class="record-btn-container" id="recordTrigger">
                    <div class="record-btn" id="recButton"></div>
                </div>

                <div class="tool-btn" id="doneBtn">
                    <i class="ri-checkbox-circle-fill text-success" style="font-size:48px;"></i>
                    <span>Done</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Editor Panel -->
    <div id="editorPanel">
        <h4 class="mb-4">Edit Your Reel</h4>

        <label>Trim Start: <span id="startVal">0</span>s</label>
        <input type="range" id="trimStart" min="0" value="0" step="0.1">

        <label>Trim End: <span id="endVal">10</span>s</label>
        <input type="range" id="trimEnd" min="0" value="10" step="0.1">

        <label>Video Volume</label>
        <input type="range" id="videoVolume" min="0" max="1" step="0.05" value="1">

        <label>Music Volume</label>
        <input type="range" id="musicVolume" min="0" max="1" step="0.05" value="0.7">

        <button onclick="exportFinalVideo()">Export & Upload Reel</button>
        <button onclick="hideEditor()" class="btn btn-secondary mt-3">Cancel</button>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.12.10/dist/umd/ffmpeg.js"></script>
    <script>
        // ────────────────────────────────────────────────
        //  MONIEFLOW REEL ENGINE – Integrated Version
        // ────────────────────────────────────────────────

        let stream = null;
        let mediaRecorder = null;
        let recordedChunks = [];
        let isRecording = false;
        let facingMode = "user";
        const maxDuration = 15; // increased slightly – feel free to change
        let timer = null;
        const audio = new Audio();
        let musicFile = null;
        let recordedBlob = null;

        const video = document.getElementById("cameraFeed");
        const recButton = document.getElementById("recButton");
        const recordTrigger = document.getElementById("recordTrigger");
        const flipBtn = document.getElementById("flipBtn");
        const doneBtn = document.getElementById("doneBtn");
        const editor = document.getElementById("editorPanel");

        const trimStart = document.getElementById("trimStart");
        const trimEnd   = document.getElementById("trimEnd");
        const videoVolume = document.getElementById("videoVolume");
        const musicVolume = document.getElementById("musicVolume");

        // ── Init Camera ─────────────────────────────────────
        async function startCamera() {
            try {
                if (stream) {
                    stream.getTracks().forEach(t => t.stop());
                }
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode },
                    audio: true
                });
                video.srcObject = stream;
                video.muted = true;
            } catch (err) {
                alert("Camera or mic access denied.\nPlease allow in browser settings.");
                console.error(err);
            }
        }

        startCamera();

        // ── Flip Camera ─────────────────────────────────────
        flipBtn.onclick = () => {
            facingMode = facingMode === "user" ? "environment" : "user";
            startCamera();
        };

        // ── Record Toggle ───────────────────────────────────
        recordTrigger.onclick = () => {
            if (!isRecording) startRecording();
            else stopRecording();
        };

        function startRecording() {
            if (!stream) return alert("Camera not ready");
            isRecording = true;
            recButton.classList.add("recording");
            recordedChunks = [];
            recordedBlob = null;

            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.ondataavailable = e => {
                if (e.data.size > 0) recordedChunks.push(e.data);
            };

            mediaRecorder.onstop = () => {
                previewRecording();
                showEditor();
            };

            mediaRecorder.start();
            timer = setTimeout(stopRecording, maxDuration * 1000);
        }

        function stopRecording() {
            isRecording = false;
            recButton.classList.remove("recording");
            clearTimeout(timer);
            if (mediaRecorder?.state !== "inactive") mediaRecorder.stop();
        }

        // ── Preview ─────────────────────────────────────────
        function previewRecording() {
            if (recordedChunks.length === 0) return;

            if (video.src) URL.revokeObjectURL(video.src);

            recordedBlob = new Blob(recordedChunks, { type: "video/webm" });
            const url = URL.createObjectURL(recordedBlob);

            video.srcObject = null;
            video.src = url;
            video.muted = false;
            video.loop = true;
            video.controls = false;
            video.play().catch(() => {});

            video.onloadedmetadata = () => {
                const dur = video.duration || maxDuration;
                trimStart.max = dur;
                trimEnd.max   = dur;
                trimStart.value = 0;
                trimEnd.value   = dur;
                document.getElementById("startVal").textContent = "0";
                document.getElementById("endVal").textContent = dur.toFixed(1);
            };
        }

        // ── Volume Sliders ──────────────────────────────────
        videoVolume.oninput = e => video.volume = +e.target.value;
        musicVolume.oninput = e => audio.volume = +e.target.value;

        // ── Trim Display Update ─────────────────────────────
        trimStart.oninput = () => document.getElementById("startVal").textContent = (+trimStart.value).toFixed(1);
        trimEnd.oninput   = () => document.getElementById("endVal").textContent   = (+trimEnd.value).toFixed(1);

        // ── Upload Video ────────────────────────────────────
        document.getElementById("reelUpload").onchange = e => {
            const file = e.target.files[0];
            if (!file?.type.startsWith("video/")) return;

            if (video.src) URL.revokeObjectURL(video.src);

            const url = URL.createObjectURL(file);
            video.srcObject = null;
            video.src = url;
            video.loop = true;
            video.controls = false;
            video.play();

            video.onloadedmetadata = () => {
                const dur = video.duration;
                trimStart.max = dur;
                trimEnd.max   = dur;
                trimStart.value = 0;
                trimEnd.value   = dur;
                document.getElementById("startVal").textContent = "0";
                document.getElementById("endVal").textContent = dur.toFixed(1);
            };

            showEditor();
        };

        // ── Editor Show/Hide ────────────────────────────────
        function showEditor() { editor.style.display = "flex"; }
        function hideEditor() { editor.style.display = "none"; }

        // ── FFmpeg (load once) ──────────────────────────────
        const { FFmpeg } = FFmpegWASM;
        let ffmpeg = null;
        let ffmpegReady = false;

        async function loadFFmpeg() {
            if (ffmpegReady) return;
            try {
                ffmpeg = new FFmpeg();
                await ffmpeg.load({
                    coreURL: "https://cdn.jsdelivr.net/npm/@ffmpeg/core@0.12.10/dist/umd/ffmpeg-core.js",
                });
                ffmpegReady = true;
            } catch (err) {
                console.error("FFmpeg failed to load", err);
                alert("Video processor failed to load.\nCheck internet or try again later.");
            }
        }

        // Preload on first interaction
        recordTrigger.addEventListener("click", loadFFmpeg, { once: true });

        // ── Export Final Video ──────────────────────────────
        async function exportFinalVideo() {
            if (!recordedBlob) return alert("No video to export");

            const start = +trimStart.value;
            const end   = +trimEnd.value;
            const dur   = end - start;

            if (dur <= 0.5) return alert("Trim too short");

            const vVol = +videoVolume.value;
            const mVol = +musicVolume.value;

            if (!ffmpegReady) {
                alert("Preparing video tools... please wait 10–30 seconds");
                await loadFFmpeg();
                if (!ffmpegReady) return;
            }

            try {
                await ffmpeg.writeFile("input.webm", await fetchFile(recordedBlob));

                let cmd = [
                    "-ss", start.toFixed(2),
                    "-t", dur.toFixed(2),
                    "-i", "input.webm",
                ];

                let filter = `volume=${vVol},loudnorm=I=-16:TP=-1.5:LRA=11[audio]`;

                if (musicFile) {
                    await ffmpeg.writeFile("music.mp3", await fetchFile(musicFile));
                    cmd.push("-i", "music.mp3");
                    filter = `[0:a]volume=${vVol},loudnorm=I=-16[a0];[1:a]volume=${mVol},loudnorm=I=-16[a1];[a0][a1]amix=inputs=2:duration=shortest[audio]`;
                }

                cmd = cmd.concat([
                    "-filter_complex", filter,
                    "-map", "0:v?",
                    "-map", "[audio]",
                    "-c:v", "libx264", "-preset", "veryfast", "-crf", "24",
                    "-c:a", "aac", "-b:a", "128k",
                    "-movflags", "+faststart",
                    "output.mp4"
                ]);

                await ffmpeg.exec(cmd);

                const data = await ffmpeg.readFile("output.mp4");
                const finalBlob = new Blob([data.buffer], { type: "video/mp4" });

                // Preview result
                if (video.src) URL.revokeObjectURL(video.src);
                video.src = URL.createObjectURL(finalBlob);
                video.loop = false;
                video.controls = true;
                video.play();

                // Upload
                uploadToServer(finalBlob);

            } catch (err) {
                console.error(err);
                alert("Processing failed – file may be too large or device too slow.");
            }
        }

        // ── Upload to PHP ───────────────────────────────────
        async function uploadToServer(blob) {
            try {
                const formData = new FormData();
                formData.append("video", blob, "monieflow-reel.mp4");

                const res = await fetch("upload.php", { method: "POST", body: formData });
                const text = await res.text();

                alert(res.ok ? `Uploaded! Server says: ${text}` : "Upload failed – check server");
            } catch (err) {
                alert("Upload error – check internet connection");
                console.error(err);
            }
        }

        // ── Done Button ─────────────────────────────────────
        doneBtn.onclick = exportFinalVideo;

        // Optional: Add music upload later (e.g. via hidden input)
        // document.querySelector(".music-picker").onclick = () => { ... }
    </script>
</body>
</html>