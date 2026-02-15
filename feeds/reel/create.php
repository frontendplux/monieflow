<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include __DIR__."/req.php"; 
        $new=new reels($conn);
        if(!$new->isLoggedIn()) header('location:/');
        $music=$new->getMusic();
        print_r($music)
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
                    <li class="d-flex align-items-center gap-3 p-3 rounded-4 hover-bg-info-10 transition mb-3 border border-white border-opacity-10" onclick="selectTrack('Turbulence')">
                        <div class="bg-info rounded-circle text-black fw-bold d-flex align-items-center justify-content-center" style="min-width: 35px; height: 35px; font-size: 12px;">01</div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-white text-truncate fw-semibold">Turbulence</div>
                            <div class="text-info opacity-75 text-truncate" style="font-size: 11px;">feat. Wizkid</div>
                        </div>
                    </li>
                    <li class="d-flex align-items-center gap-3 p-3 rounded-4 hover-bg-info-10 transition mb-3 border border-white border-opacity-10" onclick="selectTrack('Jogodo')">
                        <div class="bg-white bg-opacity-10 rounded-circle text-white d-flex align-items-center justify-content-center" style="min-width: 35px; height: 35px; font-size: 12px;">02</div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-white text-truncate fw-semibold">Jogodo</div>
                            <div class="text-info opacity-75 text-truncate" style="font-size: 11px;">Asake</div>
                        </div>
                    </li>
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
    <script>
        let isRecording = false;
        let audioCtx, mp3Source, mixedStream;
        const bgMusic = new Audio('mp3.mp3'); // Ensure this file is in your folder
        bgMusic.crossOrigin = "anonymous";
        // DOM Elements
        const sidebar = document.getElementById('musicSidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggleIcon');
        const innerCircle = document.getElementById('innerCircle');
        const recStatus = document.getElementById('recStatus');

        // Initialize Camera and Mixer
        async function initStudio() {
            try {
                const userStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                document.getElementById('preview').srcObject = userStream;

                // Set up the Audio Context for Mixing
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const micSource = audioCtx.createMediaStreamSource(userStream);
                mp3Source = audioCtx.createMediaElementSource(bgMusic);
                const destination = audioCtx.createMediaStreamDestination();

                // Connect both to the final destination
                micSource.connect(destination);
                mp3Source.connect(destination);
                mp3Source.connect(audioCtx.destination); // For you to hear it

                mixedStream = new MediaStream([
                    userStream.getVideoTracks()[0],
                    destination.stream.getAudioTracks()[0]
                ]);
            } catch (err) {
                console.error("Studio Init Failed:", err);
            }
        }

        // Sidebar Toggle Logic
        toggleBtn.onclick = () => {
            sidebar.classList.toggle('active');
            toggleIcon.className = sidebar.classList.contains('active') ? 'ri-close-line' : 'ri-music-2-fill';
        };

        // Track Selection Logic
        function selectTrack(name) {
            document.getElementById('currentTrack').innerText = name;
            if (window.innerWidth < 768) sidebar.classList.remove('active');
        }

        // Record Toggle Logic
        document.getElementById('recordBtn').onclick = () => {
            if (audioCtx.state === 'suspended') audioCtx.resume();
            
            isRecording = !isRecording;
            if (isRecording) {
                bgMusic.play();
                innerCircle.style.transform = "scale(0.7)";
                innerCircle.style.borderRadius = "12px";
                recStatus.classList.remove('d-none');
                recStatus.classList.add('rec-active');
            } else {
                bgMusic.pause();
                bgMusic.currentTime = 0;
                innerCircle.style.transform = "scale(1)";
                innerCircle.style.borderRadius = "50%";
                recStatus.classList.add('d-none');
                recStatus.classList.remove('rec-active');
            }
        };

        window.onload = initStudio;
    </script>
</body>
</html>