import { loader_board } from "../pages.js";

export default function createPost(userinfo){
    // 1. Store files in a variable so we can modify it (unlike the input.files list)
let selectedFiles = [];

window.previewImages = () => {
    const fileInput = document.getElementById('postImages');
    const container = document.getElementById('imagePreviewGrid');
    
    // Convert FileList to Array and add to our storage
    const newFiles = Array.from(fileInput.files);
    selectedFiles = [...selectedFiles, ...newFiles];

    renderPreview();
    fileInput.value = ""; // Clear input so same file can be re-selected if needed
};

// 2. Render the Masonry-style preview
const renderPreview = () => {
    const container = document.getElementById('imagePreviewGrid');
    container.innerHTML = ''; 

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            // First image full width if multiple exist, else half-width
            const colSize = (index === 0 && selectedFiles.length > 1) ? 'col-12' : 'col-6';
            
            div.className = `${colSize} position-relative mb-2`;
            div.innerHTML = `
                <div class="rounded-3 overflow-hidden shadow-sm" style="height: 200px; border: 1px solid #eee;">
                    <img src="${e.target.result}" class="w-100 h-100 object-fit-cover">
                    <button type="button" onclick="removePicture(${index})" 
                            class="btn btn-dark btn-sm position-absolute top-0 end-0 m-2 rounded-circle opacity-75 d-flex align-items-center justify-content-center" 
                            style="width:28px; height:28px; z-index: 10;">
                        <i class="bi bi-x-lg"></i> ×
                    </button>
                </div>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
};

// 3. The "Remove Picture" Function
window.removePicture = (index) => {
    selectedFiles.splice(index, 1); // Remove from our array
    renderPreview(); // Re-draw the grid
};

// 4. Send as FormData to req.php
window.handleCreatePost = async () => {
    const content = document.getElementById('postContent').value.trim();
    const sourceId = document.getElementById('postSource').value; // e.g., user_id or group_id
    
    if (!content && selectedFiles.length === 0) {
         droppySammy('info', 'Auth Error',"Please add some text or an image.");
        return;
    }

    const formData = new FormData();
    formData.append('action', 'createPost');
    formData.append('content', content);
    formData.append('source_id', sourceId); 
    formData.append('source_type', 'user'); // Or dynamic based on your UI

    // Append our custom selectedFiles array
    selectedFiles.forEach((file) => {
        formData.append('media[]', file);
    });

    try {
        droppySammy('warning', 'uploading processesing...',"please do not reload page while uploading is processing...")
        const response = await fetch(loader_board, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            alert("Post created!");
            window.location.href = "feeds.php"; 
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) {
        console.error("Submission Error:", error);
    }
};
    return`
<header class="sticky-top bg-white p-2">
  <div class="container d-flex justify-content-between align-items-center">
    <span data-href="member" class="fs-6"><i style="width:40px; height:40px" class="ri-arrow-left-s-fill p-2 border border-5 rounded-circle"></i></span>
    <h1 class="fs-4 fw-bold d-none d-lg-block">Create Post</h1>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="handleCreatePost()">
            Publish Post
        </button>
    </div>
  </div>
</header>

<div class="container p-2 h-100" style="margin:10px auto 200px auto">
    <div class="d-flex p-0 justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://i.pravatar.cc/150?u=usr_01" class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <h6 class="mb-0 fw-bold">Alex Reed</h6>
                            <span class="badge bg-light text-muted border fw-normal">Posting to Personal Feed</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <textarea class="form-control border-0 fs-4 p-0" id="postContent" rows="4" 
                            placeholder="What are you thinking about?" style="resize: none; box-shadow: none;"></textarea>
                    </div>

                    <div id="dropzone" class="border-2 border-dashed rounded-4 p-5 text-center mb-4 bg-light position-relative" 
                         onclick="document.getElementById('postImages').click()" style="cursor: pointer;">
                        <div class="py-3">
                            <i class="bi bi-images fs-1 text-primary mb-3"></i>
                            <h5 class="fw-bold">Add Photos or Video</h5>
                            <p class="text-muted small">Drag and drop or click to upload</p>
                        </div>
                        <input type="file" id="postImages" multiple hidden accept="image/*" onchange="previewImages()">
                        
                        <div id="imagePreviewGrid" class="row g-2 mt-2"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Post to</label>
                            <select class="form-select border-light bg-light rounded-3" id="postSource">
                                <option value="user">My Feed</option>
                                <option value="group">A Group</option>
                                <option value="market">Marketplace</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Privacy</label>
                            <select class="form-select border-light bg-light rounded-3">
                                <option>Public</option>
                                <option>Friends Only</option>
                                <option>Only Me</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <a href="feed.php" class="text-decoration-none text-muted small fw-bold">
                    <i class="bi bi-arrow-left"></i> Back to Feed
                </a>
            </div>
        </div>
    </div>
</div>`;
}