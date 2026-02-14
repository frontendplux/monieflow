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
</head>
<body>

    <div style="background: #1a1a1a; height: 100%; width: 100%;" id="cameraFeed"></div>

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
                <div class="tool-btn">
                    <div class="upload-preview"></div>
                    <span class="mt-1">Upload</span>
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

    <script>
        let isRecording = false;
        function toggleRecord() {
            const btn = document.getElementById('recButton');
            isRecording = !isRecording;
            
            if(isRecording) {
                btn.classList.add('recording');
                // Start a timer logic here
            } else {
                btn.classList.remove('recording');
            }
        }
    </script>
</body>
</html>