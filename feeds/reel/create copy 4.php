<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Reel | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

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

        /* Mock Camera Viewfinder */
        .viewfinder {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(rgba(0,0,0,0.3), transparent 20%, transparent 80%, rgba(0,0,0,0.6));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1;
        }

        /* Top Controls */
        .top-nav {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .music-picker {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Right Sidebar Tools */
        .side-tools {
            position: absolute;
            right: 15px;
            top: 100px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            z-index: 10;
        }

        .tool-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .tool-btn i { font-size: 24px; text-shadow: 0 2px 5px rgba(0,0,0,0.5); }
        .tool-btn span { font-size: 10px; font-weight: 700; text-transform: uppercase; }

        /* Bottom Controls */
        .footer-controls {
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 10;
        }

        .upload-preview {
            width: 40px; height: 40px;
            border-radius: 8px;
            border: 2px solid #fff;
            background: url('https://picsum.photos/100') center/cover;
        }

        .record-btn-container {
            width: 80px; height: 80px;
            border-radius: 50%;
            border: 4px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }

        .record-btn {
            width: 100%; height: 100%;
            background: #ff4d6d;
            border-radius: 50%;
            transition: 0.3s;
        }

        .record-btn.recording {
            transform: scale(0.6);
            border-radius: 8px;
        }

        /* Monetization Toggle */
        .monie-settings {
            background: rgba(0,0,0,0.7);
            border-radius: 15px;
            padding: 10px 15px;
            margin: 0 20px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(255, 204, 0, 0.3);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.12.4/dist/ffmpeg.min.js"></script>

</head>
<body>

    <video id="cameraFeed" autoplay playsinline loop style="width:100%; position: fixed; height: 100%;"></video>


    <div class="viewfinder">
        <div class="top-nav">
            <i class="ri-close-line fs-2" onclick="history.back()"></i>
            <div class="music-picker">
                <i class="ri-music-fill"></i>
                Add Sound
            </div>
            <i class="ri-settings-4-line fs-3"></i>
        </div>

        <div class="side-tools">
            <div class="tool-btn"><i class="ri-repeat-line"></i><span>Flip</span></div>
            <div class="tool-btn"><i class="ri-speed-line"></i><span>Speed</span></div>
            <div class="tool-btn"><i class="ri-magic-line"></i><span>Filters</span></div>
            <div class="tool-btn"><i class="ri-timer-line"></i><span>Timer</span></div>
            <div class="tool-btn text-warning"><i class="ri-coin-line"></i><span>Price</span></div>
        </div>

        <div>
            <div class="monie-settings">
                <div class="d-flex align-items-center">
                    <i class="ri-lock-password-line text-warning me-2"></i>
                    <div>
                        <div class="small fw-bold">Paid Access</div>
                        <div style="font-size: 10px;" class="text-white-50">Set MC price to watch</div>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-switch" type="checkbox" id="payToView">
                </div>
            </div>

            <div class="footer-controls">
                <div class="tool-btn" onclick="document.getElementById('reelUpload').click()">
                    <div class="upload-preview"></div>
                    <span class="mt-1">Upload</span>
                    <input type="file" id="reelUpload" hidden accept="video/*" />
                </div>

                <div class="record-btn-container" onclick="toggleRecord()">
                    <div class="record-btn" id="recButton"></div>
                </div>

                <div class="tool-btn">
                    <i class="ri-checkbox-circle-fill text-success" style="font-size: 40px;"></i>
                    <span>Done</span>
                </div>
            </div>
        </div>
    </div>

<div id="editorPanel" style="display:none; padding:15px; background:#111;">

    <!-- Trim Slider -->
    <div class="mb-3">
        <label>Trim</label>
        <input type="range" id="trimStart" min="0" step="0.1">
        <input type="range" id="trimEnd" min="0" step="0.1">
    </div>

    <!-- Volume Controls -->
    <div class="mb-3">
        <label>Original Volume</label>
        <input type="range" id="videoVolume" min="0" max="1" step="0.1" value="1">
    </div>

    <div class="mb-3">
        <label>Music Volume</label>
        <input type="range" id="musicVolume" min="0" max="1" step="0.1" value="1">
    </div>

    <button class="btn btn-warning w-100" onclick="exportFinalVideo()">
        Export MP4
    </button>

</div>

    <script>
        /* ===============================
   MONIEFLOW REEL ENGINE
================================ */

let stream;
let mediaRecorder;
let recordedChunks = [];
let isRecording = false;
let facingMode = "user";
let maxDuration = 10; // 10 seconds only
let timer;
let audio = new Audio();
let musicFile = null;

const video = document.getElementById("cameraFeed");
const recButton = document.getElementById("recButton");

/* ===============================
   INIT CAMERA
================================ */
async function startCamera() {
    try {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }

        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: facingMode },
            audio: true
        });

        video.srcObject = stream;
        video.controls = false;

    } catch (err) {
        alert("Camera access denied or not supported.");
        console.error(err);
    }
}

startCamera();

/* ===============================
   FLIP CAMERA
================================ */
document.querySelector(".ri-repeat-line").parentElement.onclick = function () {
    facingMode = facingMode === "user" ? "environment" : "user";
    startCamera();
};

/* ===============================
   RECORD
================================ */
function toggleRecord() {
    if (!isRecording) {
        startRecording();
    } else {
        stopRecording();
    }
}

function startRecording() {

    if (!stream) return;

    isRecording = true;
    recButton.classList.add("recording");
    recordedChunks = [];

    mediaRecorder = new MediaRecorder(stream);

    mediaRecorder.ondataavailable = e => {
        if (e.data.size > 0) recordedChunks.push(e.data);
    };

    mediaRecorder.onstop = () => {
        previewRecording();
    };

    mediaRecorder.start();

    // AUTO STOP AT 10s
    timer = setTimeout(() => {
        stopRecording();
    }, maxDuration * 1000);
}

function stopRecording() {
    isRecording = false;
    recButton.classList.remove("recording");
    clearTimeout(timer);

    if (mediaRecorder && mediaRecorder.state !== "inactive") {
        mediaRecorder.stop();
    }
}

/* ===============================
   PREVIEW (NO CONTROLS)
================================ */
function previewRecording() {
    const blob = new Blob(recordedChunks, { type: "video/webm" });
    const url = URL.createObjectURL(blob);

    video.srcObject = null;
    video.src = url;
    video.controls = false;
    video.loop = true;
    video.play();

    if (musicFile) {
        audio.currentTime = 0;
        audio.play();
    }
}

/* ===============================
   TRIM VIDEO
================================ */
function trimVideo(start, end) {
    if (!video.duration) return;

    video.currentTime = start;

    video.ontimeupdate = function () {
        if (video.currentTime >= end) {
            video.pause();
        }
    };
}

/* Example usage:
   trimVideo(2, 7);  // trims from 2s to 7s
*/

/* ===============================
   ADD MUSIC
================================ */
function addMusic(file) {
    musicFile = file;
    const url = URL.createObjectURL(file);
    audio.src = url;
    audio.loop = true;
}

/* ===============================
   REMOVE MUSIC
================================ */
function removeMusic() {
    audio.pause();
    audio.src = "";
    musicFile = null;
}

/* ===============================
   UPLOAD VIDEO FROM DEVICE
================================ */
document.getElementById("reelUpload").addEventListener("change", function (e) {

    const file = e.target.files[0];
    if (!file) return;

    const url = URL.createObjectURL(file);

    video.srcObject = null;
    video.src = url;
    video.controls = false;
    video.loop = true;
    video.play();
});

    </script>
</body>
</html>