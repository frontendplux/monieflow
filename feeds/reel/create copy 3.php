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
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
    background: #000;
    color: white;
    font-family: 'Segoe UI', sans-serif;
    overflow: hidden;
}

#cameraFeed {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.viewfinder {
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(rgba(0,0,0,0.3), transparent 20%, transparent 80%, rgba(0,0,0,0.6));
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    z-index: 2;
}

/* Top */
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
    gap: 8px;
}

/* Side tools */
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
}

.tool-btn i {
    font-size: 24px;
    text-shadow: 0 2px 5px rgba(0,0,0,0.5);
}

.tool-btn span {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}

/* Bottom */
.footer-controls {
    padding: 30px;
    display: flex;
    align-items: center;
    justify-content: space-around;
}

.upload-preview {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 2px solid #fff;
    background: #222;
}

.record-btn-container {
    width: 85px;
    height: 85px;
    border-radius: 50%;
    border: 4px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
}

.record-btn {
    width: 100%;
    height: 100%;
    background: #ff4d6d;
    border-radius: 50%;
    transition: 0.3s;
}

.record-btn.recording {
    transform: scale(0.6);
    border-radius: 12px;
}

/* Monetization */
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

/* Timer bar */
.progress-bar-record {
    position: absolute;
    top: 0;
    left: 0;
    height: 5px;
    width: 0%;
    background: #ff4d6d;
    z-index: 5;
}
</style>
</head>

<body>

<video id="cameraFeed" autoplay playsinline></video>
<div class="progress-bar-record" id="recordProgress"></div>

<div class="viewfinder">

    <!-- TOP -->
    <div class="top-nav">
        <i class="ri-close-line fs-2" onclick="history.back()"></i>
        <div class="music-picker">
            <i class="ri-music-fill"></i> Add Sound
        </div>
        <i class="ri-settings-4-line fs-3"></i>
    </div>

    <!-- SIDE -->
    <div class="side-tools">
        <div class="tool-btn" onclick="flipCamera()">
            <i class="ri-repeat-line"></i>
            <span>Flip</span>
        </div>
    </div>

    <!-- BOTTOM -->
    <div>

        <div class="monie-settings">
            <div class="d-flex align-items-center">
                <i class="ri-lock-password-line text-warning me-2"></i>
                <div>
                    <div class="small fw-bold">Paid Access</div>
                    <div style="font-size: 10px;" class="text-white-50">
                        Set MC price to watch
                    </div>
                </div>
            </div>
            <input type="checkbox" id="payToView">
        </div>

        <div class="footer-controls">

            <div class="tool-btn" onclick="document.getElementById('reelUpload').click()">
                <div class="upload-preview"></div>
                <span>Upload</span>
                <input type="file" id="reelUpload" hidden accept="video/*" />
            </div>

            <div class="record-btn-container" onclick="toggleRecord()">
                <div class="record-btn" id="recButton"></div>
            </div>

            <div class="tool-btn" onclick="finishRecording()">
                <i class="ri-checkbox-circle-fill text-success" style="font-size: 40px;"></i>
                <span>Done</span>
            </div>

        </div>
    </div>

</div>

<script>
let stream;
let mediaRecorder;
let recordedChunks = [];
let isRecording = false;
let facingMode = "user";
let timerInterval;
let maxDuration = 30; // seconds

const video = document.getElementById("cameraFeed");
const recButton = document.getElementById("recButton");
const progressBar = document.getElementById("recordProgress");

async function startCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }

    stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: facingMode },
        audio: true
    });

    video.srcObject = stream;
}

startCamera();

function flipCamera() {
    facingMode = facingMode === "user" ? "environment" : "user";
    startCamera();
}

function toggleRecord() {
    if (!isRecording) {
        startRecording();
    } else {
        stopRecording();
    }
}

function startRecording() {
    isRecording = true;
    recButton.classList.add("recording");
    recordedChunks = [];

    mediaRecorder = new MediaRecorder(stream);

    mediaRecorder.ondataavailable = e => {
        if (e.data.size > 0) recordedChunks.push(e.data);
    };

    mediaRecorder.onstop = previewRecording;

    mediaRecorder.start();

    let time = 0;
    timerInterval = setInterval(() => {
        time++;
        progressBar.style.width = (time / maxDuration) * 100 + "%";
        if (time >= maxDuration) stopRecording();
    }, 1000);
}

function stopRecording() {
    isRecording = false;
    recButton.classList.remove("recording");
    clearInterval(timerInterval);
    progressBar.style.width = "0%";
    mediaRecorder.stop();
}

function previewRecording() {
    const blob = new Blob(recordedChunks, { type: "video/webm" });
    const url = URL.createObjectURL(blob);
    video.srcObject = null;
    video.src = url;
    video.controls = true;
}

function finishRecording() {
    alert("Next step: Upload to server");
}

document.getElementById("reelUpload").addEventListener("change", function(e){
    const file = e.target.files[0];
    if(file){
        const url = URL.createObjectURL(file);
        video.srcObject = null;
        video.src = url;
        video.controls = true;
    }
});
</script>

</body>
</html>
