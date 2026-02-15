<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<title>monieflow - upload your music</title>

<style>
body { 
    background: #f8f9fa; 
    color: #111; 
    font-family: 'Inter', sans-serif; 
    overflow-x: hidden; 
}

.upload-card {
    background: #ffffff;
    border: 1px solid rgba(13, 202, 240, 0.15);
    border-radius: 2.5rem;
}

.drop-zone {
    border: 2px dashed rgba(13, 202, 240, 0.4);
    border-radius: 1.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 200px;
    background: #fdfdfd;
}

.drop-zone:hover, .drop-zone.dragover {
    border-color: #0dcaf0;
    background: rgba(13, 202, 240, 0.08);
}

.form-control, .form-select {
    background-color: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    color: #000 !important;
    padding: 12px 15px;
}

.form-control:focus {
    border-color: #0dcaf0 !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.15);
}

.btn-boss {
    background: #0dcaf0;
    color: black;
    font-weight: 800;
    border-radius: 1rem;
    padding: 15px;
    transition: all 0.3s;
    border: none;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-boss:hover { 
    transform: scale(1.02); 
    background: #0bb5d8; 
    box-shadow: 0 0 15px rgba(13, 202, 240, 0.3); 
}

input[type="file"] { display: none; }
.tracking-widest { letter-spacing: 3px; }
</style>
</head>

<body class="py-5">

<div class="container">
<div class="row justify-content-center">
<div class="col-xl-10 col-lg-11">

<div class="text-center mb-5">
<h1 class="display-5 fw-bold text-info">FlowBeat</h1>
<p class="text-secondary tracking-widest small">PUBLISH TO THE FLOW STUDIO</p>
</div>

<div class="upload-card p-4 p-md-5 shadow-lg">
<form id="uploadForm">

<div class="row g-5">

<div class="col-lg-5">
<div class="mb-4">
<label class="form-label text-info small fw-bold tracking-widest">1. AUDIO SOURCE</label>
<label for="fileInput" class="drop-zone d-flex flex-column align-items-center justify-content-center w-100" id="dropArea">
<i class="ri-music-2-fill display-4 text-info mb-3"></i>
<p id="fileName" class="text-secondary text-center small px-3 m-0">
Drag MP3 here or <span class="text-info">Browse</span>
</p>
<input type="file" id="fileInput" accept="audio/*" required>
</label>
</div>

<div class="p-3 rounded-4 bg-info bg-opacity-10 border border-info border-opacity-25">
<div class="d-flex justify-content-between align-items-center">
<div>
<p class="fw-bold mb-0" style="font-size: 14px;">Premium Distribution</p>
<p class="text-info mb-0" style="font-size: 11px;">you earn when view payments</p>
</div>
<div class="form-check form-switch">
<input class="form-check-input bg-info border-0" type="checkbox" id="subToggle">
</div>
</div>
</div>
</div>

<div class="col-lg-7">
<label class="form-label text-info small fw-bold tracking-widest mb-3">2. TRACK DETAILS</label>

<div class="d-flex flex-column flex-md-row gap-4 mb-4 align-items-start align-items-md-center">
<div class="position-relative flex-shrink-0">
<div id="imagePreview" class="rounded-circle border border-2 border-info d-flex align-items-center justify-content-center overflow-hidden bg-light" 
style="width: 110px; height: 110px;">
<i class="ri-image-add-line fs-1 text-info opacity-50"></i>
</div>

<label for="coverInput" class="position-absolute bottom-0 end-0 bg-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
style="width: 32px; height: 32px; border: 3px solid white; cursor: pointer;">
<i class="ri-camera-fill text-black" style="font-size: 14px;"></i>
</label>

<input type="file" id="coverInput" accept="image/*">
</div>

<div class="flex-grow-1 w-100">
<label class="small fw-bold mb-1">SONG TITLE</label>
<input type="text" id="trackTitle" class="form-control rounded-3" required>
</div>
</div>

<div class="mb-4">
<label class="small fw-bold mb-1">DESCRIPTION</label>
<textarea id="description" class="form-control rounded-3" rows="2"></textarea>
</div>

<div class="row g-3 mb-4">
<div class="col-6">
<label class="small fw-bold mb-1">HASHTAGS</label>
<input type="text" id="hashtags" class="form-control rounded-3">
</div>

<div class="col-6">
<label class="small fw-bold mb-1">CATEGORY</label>
<select id="category" class="form-select rounded-3">
<option selected>Afrobeats</option>
<option>Street-Pop</option>
<option>Amapiano</option>
<option>Highlife</option>
</select>
</div>
</div>

<div id="progressWrapper" class="mb-4 d-none">
<div class="d-flex justify-content-between mb-1">
<span class="small text-info">Uploading...</span>
<span id="percentText" class="small text-info">0%</span>
</div>
<div class="progress" style="height:6px;">
<div id="uploadProgress" class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width:0%"></div>
</div>
</div>

<button type="submit" class="btn btn-boss w-100">
<i class="ri-rocket-2-fill me-2"></i>GO LIVE
</button>

</div>
</div>
</form>
</div>
</div>
</div>
</div>

<script>
const fileInput = document.getElementById('fileInput');
const coverInput = document.getElementById('coverInput');
const imagePreview = document.getElementById('imagePreview');
const fileNameLabel = document.getElementById('fileName');
const form = document.getElementById('uploadForm');

// Cover preview
coverInput.onchange = e => {
    const file = e.target.files[0];
    if(file){
        imagePreview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-100 h-100" style="object-fit:cover;">`;
    }
};

// Audio preview text
fileInput.onchange = e => {
    if(e.target.files.length > 0){
        fileNameLabel.innerHTML = `<i class="ri-check-line text-success"></i><br>
        <span class="text-dark">${e.target.files[0].name}</span>`;
    }
};

// Drag & Drop
const dropArea = document.getElementById('dropArea');
['dragenter','dragover'].forEach(eventName=>{
    dropArea.addEventListener(eventName,e=>{
        e.preventDefault();
        dropArea.classList.add('dragover');
    });
});
['dragleave','drop'].forEach(eventName=>{
    dropArea.addEventListener(eventName,e=>{
        e.preventDefault();
        dropArea.classList.remove('dragover');
    });
});
dropArea.addEventListener('drop',e=>{
    fileInput.files = e.dataTransfer.files;
    fileInput.dispatchEvent(new Event('change'));
});

// Upload
form.onsubmit = function(e){
    e.preventDefault();

    const audioFile = fileInput.files[0];
    if(!audioFile){
        alert("Select audio file");
        return;
    }

    const formData = new FormData();
    formData.append("audio", audioFile);
    formData.append("cover", coverInput.files[0]);
    formData.append("title", document.getElementById("trackTitle").value);
    formData.append("description", document.getElementById("description").value);
    formData.append("hashtags", document.getElementById("hashtags").value);
    formData.append("category", document.getElementById("category").value);
    formData.append("premium", document.getElementById("subToggle").checked ? 1 : 0);

    document.getElementById('progressWrapper').classList.remove('d-none');

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "req.php", true);

    xhr.upload.onprogress = function(e){
        if(e.lengthComputable){
            let percent = Math.round((e.loaded / e.total) * 100);
            document.getElementById("uploadProgress").style.width = percent+"%";
            document.getElementById("percentText").innerText = percent+"%";
        }
    };

    xhr.onload = function(){
        if(xhr.status === 200){
            alert("Boss! Your vibe is live.");
            window.location.href = "studio.html";
        }else{
            alert("Upload failed");
        }
    };

    xhr.send(formData);
};
</script>

</body>
</html>
