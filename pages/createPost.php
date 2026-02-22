   <header class="bg-white py-3">
        <div class="d-flex justify-content-between align-items-center container">
            <span data-href='home' class="ri-arrow-left-s-fill"> Return</span>
            <span data-post='create-post' class="btn btn-light text-capitalize fw-medium rounded-pill">create post</span>
        </div>
    </header>
    <div class="container my-2">
        <div  class="w-100 bg-white m-auto p-3 rounded-3">
           <textarea placeholder="What is on your mind ?" class="form-control mb-3"></textarea>
           <div id="image_preview" class="pb-4 d-flex gap-2 overflow-auto"></div>
           <div class="d-flex gap-3">
                <span class="d-block fw-bold text-warning ri-video-fill btn btn-light rounded-pill" onclick="droppySammy('info', 'Auth Error','these is not available yet but you can upload picture and text, to keep earning more!')"> video</span>
                <span class="d-block fw-bold text-info ri-music-fill btn btn-light rounded-pill" onclick="droppySammy('info', 'Auth Error','these is not available yet but you can upload picture and text, to keep earning more!')"> audio</span>
                <span class="d-block fw-bold ri-image-fill btn btn-light rounded-pill" onclick="document.getElementById('upload-post-video-input').click()"> image</span>
                <input type="file" accept="image/*" multiple onchange="upload_post_video_input(this)" id="upload-post-video-input" class="d-none">
           </div>
        </div>
    </div>

<button id="uploadBtn" class="btn btn-primary">Upload</button>

<script>
function upload_post_video_input(input) {
    const previewContainer = document.getElementById('image_preview');
    previewContainer.innerHTML = ''; // clear old previews

    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = "position-relative bg-light m-2";
            div.style.width = "100px";
            div.style.height = "100px";
            div.style.overflow = "hidden";

            const closeBtn = document.createElement('span');
            closeBtn.className = "ri-close-circle-fill position-absolute end-0 m-2 z-3";
            closeBtn.style.cursor = "pointer";
            closeBtn.onclick = () => div.remove();

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = "100%";
            img.style.height = "100%";
            img.style.objectFit = "cover";

            div.appendChild(closeBtn);
            div.appendChild(img);
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// Handle upload button click
 const () => {
    const input = document.getElementById('upload-post-video-input');
    if (!input.files.length) {
        alert("Please select at least one image before uploading.");
        return;
    }

    const formData = new FormData();
    Array.from(input.files).forEach(file => {
        formData.append('images[]', file);
    });

    const textContent = document.querySelector('textarea').value;
    formData.append('text', textContent);

    fetch('req.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert("Successfully uploaded!");
        console.log('Server response:', data);
    })
    .catch(err => {
        alert("Upload failed. Please try again.");
        console.error('Upload error:', err);
    });
});
</script>