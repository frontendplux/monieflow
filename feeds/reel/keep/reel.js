/* ===============================
   MONIEFLOW REEL ENGINE
   (Improved & Bug-Fixed Version)
================================ */

let stream;
let mediaRecorder;
let recordedChunks = [];
let isRecording = false;
let facingMode = "user";
let maxDuration = 10; // seconds
let timer;
let audio = new Audio();
let musicFile = null;
let recordedBlob = null;
let finalBlob = null;

// DOM Elements
const video = document.getElementById("cameraFeed");
const recButton = document.getElementById("recButton");
// Assume these exist in HTML:
const trimStart = document.getElementById("trimStart");
const trimEnd   = document.getElementById("trimEnd");
const videoVolume = document.getElementById("videoVolume");
const musicVolume = document.getElementById("musicVolume");
const reelUpload = document.getElementById("reelUpload");

/* ===============================
   INIT CAMERA
================================ */
async function startCamera() {
    try {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: facingMode },
            audio: true
        });

        video.srcObject = stream;
        video.muted = true;       // prevent feedback
        video.controls = false;
        video.playsInline = true; // important for mobile

    } catch (err) {
        alert("Camera access denied or not available.");
        console.error("Camera error:", err);
    }
}

startCamera();

/* ===============================
   FLIP CAMERA
================================ */
document.querySelector(".ri-repeat-line")?.parentElement?.addEventListener("click", () => {
    facingMode = facingMode === "user" ? "environment" : "user";
    startCamera();
});

/* ===============================
   RECORD CONTROLS
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
    recordedBlob = null;

    mediaRecorder = new MediaRecorder(stream, { mimeType: "video/webm;codecs=vp9,opus" });

    mediaRecorder.ondataavailable = e => {
        if (e.data.size > 0) recordedChunks.push(e.data);
    };

    mediaRecorder.onstop = previewRecording;

    mediaRecorder.start();

    // Auto-stop after maxDuration
    timer = setTimeout(stopRecording, maxDuration * 1000);
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
   PREVIEW RECORDED VIDEO
================================ */
function previewRecording() {
    if (recordedChunks.length === 0) return;

    // Clean up previous source
    if (video.src) {
        URL.revokeObjectURL(video.src);
        video.src = "";
    }

    recordedBlob = new Blob(recordedChunks, { type: "video/webm" });
    const url = URL.createObjectURL(recordedBlob);

    video.srcObject = null;
    video.src = url;
    video.controls = false;
    video.loop = true;
    video.muted = false;
    video.play().catch(e => console.warn("Playback failed:", e));

    // Enable trim sliders once duration is known
    video.onloadedmetadata = () => {
        if (trimStart && trimEnd && !isNaN(video.duration)) {
            trimStart.max = video.duration;
            trimEnd.max   = video.duration;
            trimStart.value = 0;
            trimEnd.value   = Math.min(maxDuration, video.duration);
        }
    };

    if (musicFile) {
        audio.currentTime = 0;
        audio.play().catch(e => console.warn("Music auto-play blocked:", e));
    }
}

/* ===============================
   LIVE VOLUME CONTROLS
================================ */
if (videoVolume) {
    videoVolume.oninput = e => {
        video.volume = parseFloat(e.target.value);
    };
}

if (musicVolume) {
    musicVolume.oninput = e => {
        audio.volume = parseFloat(e.target.value);
    };
}

/* ===============================
   ADD / REMOVE MUSIC
================================ */
function addMusic(file) {
    if (!file) return;
    musicFile = file;
    const url = URL.createObjectURL(file);
    audio.src = url;
    audio.loop = true;
    // If already previewing, start music
    if (!video.paused) {
        audio.currentTime = video.currentTime;
        audio.play().catch(() => {});
    }
}

function removeMusic() {
    audio.pause();
    audio.src = "";
    musicFile = null;
}

/* ===============================
   UPLOAD FROM DEVICE
================================ */
if (reelUpload) {
    reelUpload.addEventListener("change", e => {
        const file = e.target.files?.[0];
        if (!file || !file.type.startsWith("video/")) return;

        // Clean up previous
        if (video.src) URL.revokeObjectURL(video.src);

        const url = URL.createObjectURL(file);
        video.srcObject = null;
        video.src = url;
        video.controls = false;
        video.loop = true;
        video.play();

        video.onloadedmetadata = () => {
            if (trimStart && trimEnd) {
                trimStart.max = video.duration;
                trimEnd.max   = video.duration;
                trimStart.value = 0;
                trimEnd.value   = video.duration;
            }
        };
    });
}

/* ===============================
   FFMPEG SETUP (Load once)
================================ */

let ffmpeg = null;
let isFFmpegReady = false;

async function initFFmpeg() {
    if (isFFmpegReady) return true;

    try {
        const { FFmpeg } = FFmpegWASM; // make sure library is imported
        ffmpeg = new FFmpeg();

        // Optional: show loading UI here
        console.log("Loading FFmpeg...");
        await ffmpeg.load({
            coreURL: "https://unpkg.com/@ffmpeg/core@0.12.10/dist/umd/ffmpeg-core.js",
        });
        isFFmpegReady = true;
        console.log("FFmpeg loaded");
        return true;
    } catch (err) {
        console.error("FFmpeg load failed:", err);
        alert("Failed to load video processor. Check internet and try again.");
        return false;
    }
}

// Optional: pre-load on page load or first interaction
// window.addEventListener("load", initFFmpeg);

/* ===============================
   EXPORT FINAL VIDEO
================================ */

async function exportFinalVideo() {
    if (!recordedBlob) {
        alert("No video available to export.");
        return;
    }

    const start = parseFloat(trimStart?.value || 0);
    const end   = parseFloat(trimEnd?.value   || (recordedBlob.duration || maxDuration));
    const duration = end - start;

    if (duration <= 0 || isNaN(duration)) {
        alert("Invalid trim range.");
        return;
    }

    const vidVol = parseFloat(videoVolume?.value || 1);
    const musVol = parseFloat(musicVolume?.value || 0.7);

    if (!await initFFmpeg()) return;

    alert("Processing video... (may take a few seconds)");

    try {
        await ffmpeg.writeFile("input.webm", await fetchFile(recordedBlob));

        let command = [
            "-ss", start.toFixed(2),
            "-t",  duration.toFixed(2),
            "-i", "input.webm",
        ];

        let audioFilter;

        if (musicFile) {
            await ffmpeg.writeFile("music.mp3", await fetchFile(musicFile));
            command.push("-i", "music.mp3");

            audioFilter = [
                `[0:a]volume=${vidVol},loudnorm=I=-16:TP=-1.5:LRA=11[a0];`,
                `[1:a]volume=${musVol},loudnorm=I=-16:TP=-1.5:LRA=11[a1];`,
                `[a0][a1]amix=inputs=2:duration=shortest:weights=1 0.6[audio]`
            ].join("");
        } else {
            audioFilter = `volume=${vidVol},loudnorm=I=-16:TP=-1.5:LRA=11[audio]`;
        }

        command = command.concat([
            "-filter_complex", audioFilter,
            "-map", "0:v",
            "-map", "[audio]",
            "-c:v", "libx264",
            "-preset", "veryfast",
            "-crf", "23",
            "-c:a", "aac",
            "-b:a", "128k",
            "-movflags", "+faststart",
            "output.mp4"
        ]);

        await ffmpeg.exec(command);

        const data = await ffmpeg.readFile("output.mp4");
        finalBlob = new Blob([data.buffer], { type: "video/mp4" });

        // Clean up previous preview
        if (video.src) URL.revokeObjectURL(video.src);

        const finalURL = URL.createObjectURL(finalBlob);
        video.src = finalURL;
        video.loop = false;
        video.controls = true;
        video.play();

        // Optional: auto-upload
        // uploadToServer(finalBlob);

    } catch (err) {
        console.error("Export failed:", err);
        alert("Video processing failed. Try a shorter clip or check console.");
    }
}

/* ===============================
   UPLOAD TO SERVER
================================ */

async function uploadToServer(blob) {
    try {
        const formData = new FormData();
        formData.append("video", blob, "monieflow-reel.mp4");

        const response = await fetch("upload.php", {
            method: "POST",
            body: formData
        });

        if (!response.ok) throw new Error(`Upload failed: ${response.status}`);

        const result = await response.text();
        alert("Upload successful: " + result);
    } catch (err) {
        console.error("Upload error:", err);
        alert("Upload failed. Check connection or server.");
    }
}