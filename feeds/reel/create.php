<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include __DIR__."/req.php"; 
        $new=new reels($conn);
        if(!$new->isLoggedIn()) header('location:/');
        $music=$new->getMusic();
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <title>Asake Studio - The Boss Edition</title>
    <style>
        body { background-color: black; overflow: hidden; font-family: sans-serif; }
        #preview { object-fit: cover; transform: scaleX(-1); position: fixed; top: 0; left: 0; width: 100%; height: 100%; }
        
        /* Sidebar Styling */
        .glass-sidebar {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
        }

        /* Hover Effects */
        .hover-bg-info-10:hover { background: rgba(13, 202, 240, 0.15); cursor: pointer; }
        .transition { transition: all 0.3s ease; }
        
        /* Recording State */
        .rec-active { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

        /* Responsive Mobile Sidebar */
        @media (max-width: 768px) {
            #musicSidebar { transform: translateX(100%); width: 85% !important; }
            #musicSidebar.active { transform: translateX(0); }
        }
    </style>
</head>
<body class="text-white">

    <video id="preview" autoplay playsinline muted></video>

    <div class="position-relative vh-100 w-100">
        
        <button id="sidebarToggle" class="btn btn-info position-absolute end-0 top-0 m-4 d-md-none" style="z-index: 2000; border-radius: 12px;">
            <i id="toggleIcon" class="ri-music-2-fill"></i>
        </button>

        <div class="position-absolute start-0 top-0 m-4">
            <span id="recStatus" class="bg-danger px-3 py-1 rounded-pill text-uppercase fw-bolder d-none">rec</span>
        </div>

        <div id="musicSidebar" class="col-md-3 glass-sidebar position-fixed end-0 top-0 bottom-0 shadow-lg">
            <div class="p-4 overflow-y-auto h-100">
                <div class="mb-5 pt-4 pt-md-0">
                    <h5 class="text-info fw-bold mb-1">ASAKE <span class="text-white opacity-50">/ BOSS</span></h5>
                    <p class="text-secondary text-uppercase mb-0" style="font-size: 10px; letter-spacing: 2px;">Vibe Selection (2026)</p>
                </div>
                
                <ul class="list-unstyled">
                <?php foreach ($music as $key => $value):
                    $data=json_decode($value['data'],true);
                    $key += 1;
                 ?>
                    <li class="d-flex align-items-center gap-3 p-3 rounded-4 hover-bg-info-10 transition mb-3 border border-white border-opacity-10" onclick="selectTrack('<?= $value['txt'] ?? $data['title'] ?>','<?= '/uploads/audio/'.$data['audio'] ?>')">
                        <div class="bg-<?= $key == 1 ? 'info' : 'white' ?> rounded-circle text-black fw-bold d-flex align-items-center justify-content-center" style="min-width: 35px; height: 35px; font-size: 12px;"><?= $key ?> </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <img src="/uploads/covers/<?= $data['cover'] ?>" style="width:100px" alt="">
                            <div class="text-white text-truncate fw-semibold"><?= $value['txt'] ?? $data['title'] ?></div>
                            <div class="text-info opacity-75 text-truncate" style="font-size: 11px;"><?= $value['description'] ?? $data['title'] ?></div>
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="p-5 justify-content-center align-items-center position-fixed bottom-0 start-0 end-0 d-flex" style="background: linear-gradient(transparent, rgba(0,0,0,0.9));">
            <div id="recordBtn" class="p-1 rounded-circle bg-white border-0 transition shadow-lg" style="cursor: pointer;">
                <div id="innerCircle" class="rounded-circle transition" style="width: 65px; height: 65px; background: red;"></div>
            </div>
        </div>
    </div>


               <div class="position-fixed  start-0 end-0 d-flex justify-content-center  " style="bottom: 150px;">
                 <div class="mt-5 p-3 col-11 col-md-3  right-0 rounded-4 bg-info bg-opacity-10 border border-info border-opacity-25">
                    <p class="text-info mb-1" style="font-size: 12px;">Now Mixing:</p>
                    <p id="currentTrack" class="text-white fw-bold mb-0">None Selected</p>
                </div>
               </div>

               <audio src="" id="audio-lite" class="d-none"></audio>

               <script>
let isRecording = false;
let mediaRecorder;
let recordedChunks = [];
let audioCtx, micSource, mp3Source, mixedStream;

const bgMusic = document.getElementById('audio-lite');
bgMusic.crossOrigin = "anonymous";

const innerCircle = document.getElementById('innerCircle');
const recStatus = document.getElementById('recStatus');

async function initStudio() {
    try {

        const userStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        });

        document.getElementById('preview').srcObject = userStream;

        audioCtx = new (window.AudioContext || window.webkitAudioContext)();

        micSource = audioCtx.createMediaStreamSource(userStream);
        mp3Source = audioCtx.createMediaElementSource(bgMusic);

        const destination = audioCtx.createMediaStreamDestination();

        micSource.connect(destination);
        mp3Source.connect(destination);
        mp3Source.connect(audioCtx.destination);

        mixedStream = new MediaStream([
            userStream.getVideoTracks()[0],
            destination.stream.getAudioTracks()[0]
        ]);

    } catch (err) {
        console.error(err);
    }
}

function selectTrack(name, audio) {
    bgMusic.src = audio;
    bgMusic.load();
    document.getElementById('currentTrack').innerText = name;
}

document.getElementById('recordBtn').onclick = async () => {

    if (!mixedStream) return;

    if (!isRecording) {

        if (audioCtx.state === 'suspended') await audioCtx.resume();

        recordedChunks = [];

        mediaRecorder = new MediaRecorder(mixedStream, {
            mimeType: 'video/webm;codecs=vp8,opus'
        });

        mediaRecorder.ondataavailable = e => {
            if (e.data.size > 0) recordedChunks.push(e.data);
        };

        mediaRecorder.onstop = uploadVideo;

        mediaRecorder.start();
        bgMusic.play();

        innerCircle.style.transform = "scale(0.7)";
        innerCircle.style.borderRadius = "12px";
        recStatus.classList.remove('d-none');
        recStatus.classList.add('rec-active');

        isRecording = true;

        // AUTO STOP AFTER 5 SECONDS
        setTimeout(() => {
            if (mediaRecorder.state !== "inactive") {

                mediaRecorder.stop();

                bgMusic.pause();
                bgMusic.currentTime = 0;

                innerCircle.style.transform = "scale(1)";
                innerCircle.style.borderRadius = "50%";
                recStatus.classList.add('d-none');
                recStatus.classList.remove('rec-active');

                isRecording = false;
            }
        }, 5000);

    }
};

async function uploadVideo() {

    const blob = new Blob(recordedChunks, { type: 'video/webm' });

    const formData = new FormData();
    formData.append("video", blob);
    formData.append("title", document.getElementById("currentTrack").innerText);
    formData.append("description", "5 seconds reel");
    formData.append("category", "reel");

    try {

        const response = await fetch("upload.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert("Reel uploaded successfully 🔥");
        } else {
            alert(result.message);
        }

    } catch (err) {
        console.error(err);
        alert("Upload failed");
    }
}

window.onload = initStudio;
</script>

</body>
</html>