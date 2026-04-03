import { Feed } from "../helperJs/helperfeeds.js";
import router from "../helperJs/helperRouter.js";
import { toggleNav } from "../helperJs/mainhelper.js";
import { Footer } from "./feeds.js";

export function CreateFeedPageTemplateFunction() {

  const state = {
  media: []
};

window.updatePostButtonState = function() {
  const btn = document.getElementById("postFeedBtn");
  const textarea = document.getElementById("feedContent");
  const hasText = textarea.value.trim().length > 0;
  const hasMedia = state.media.length > 0;

  if (hasText || hasMedia) {
    btn.style.backgroundColor = "#f0c14b"; // active bg
    btn.style.borderColor = "#a88734";     // active border
    btn.style.color = "#111";               // active text
  } else {
    btn.style.backgroundColor = "#3a4553"; // inactive bg
    btn.style.borderColor = "#4a5568";     // inactive border
    btn.style.color = "#888";               // inactive text
  }
}
    window.removeMedia = function(index) {
    state.media.splice(index, 1);
     updatePostButtonState();
    document.getElementById("post-media-preview").innerHTML = state.media.map((file, index) =>  `
      <div class="position-relative">
        <img src="${URL.createObjectURL(file)}" alt="preview" style="width:80px;height:80px;object-fit:cover;border-radius:6px" />
        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle" onclick="removeMedia(${index})">
          <i class="bi bi-x"></i>
        </button>
      </div>
    `).join('');
    } 
document.getElementById("feedContent").addEventListener("input", updatePostButtonState);

   window.uploadmultipleFiles = function(event) {
    const files = Array.from(event.target.files);
    state.media = files;
      updatePostButtonState(); // update button state
    document.getElementById("post-media-preview").innerHTML = files.map(file => `
     <div class="position-relative">
        <img src="${URL.createObjectURL(file)}" alt="preview" style="width:80px;height:80px;object-fit:cover;border-radius:6px" />
        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle" onclick="removeMedia(${state.media.indexOf(file)})">
          <i class="bi bi-x"></i>
        </button>
      </div>
    `).join('');
  }

  window.createFeeds = async function() {
    const isloding=document.getElementById("isloading");
     isloding.classList.remove("d-none");
     isloding.classList.add("d-flex");
    const content = document.getElementById("feedContent").value.trim();
    if (!content && state.media.length === 0) {
      toggleNav("red", "UPLOADING ERROR", "Please add some content or media to your post.");
       isloding.classList.remove("d-flex");
       isloding.classList.add("d-none");
      return;
    }
    const formData = new FormData();
    formData.append("text", content);
    state.media.forEach((file, index) => {
      formData.append(`images[]`, file);
    });
    const feeds=new Feed();
    const response = await feeds.createFeedsx(formData);
    if (response.success) {
      toggleNav("green", "POST CREATED", "Your feed has been successfully posted.");
      router("/home");
      // Optionally, redirect to the feed page or clear the form
    } else {
      toggleNav("red", "POSTING ERROR", "There was an issue creating your post. Please try again.");
    }
     isloding.classList.remove("d-flex");
       isloding.classList.add("d-none");
  }
}
export function CreateFeedPageTemplate() {
  return /* html */ `
     <div id="isloading" style="background-color:#f0f2f5;font-family:Arial,sans-serif" class="d-none">
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white" style="z-index:9999">
           <div class="spinner-border text-dark"></div>
            <span class="ms-3 fw-bold" style="font-size:1.2rem;color:#333">Posting to feed...</span>
           </div>
      </div>

      <!-- NAV -->
      <nav class="bg-white px-2 sticky-top py-3 shadow-sm" style="background-color:#232f3e">
        <div class="container d-flex justify-content-between mx-auto">
          <div class="d-flex align-items-center gap-2">
            <span class="btn btn-light cursor-pointer" onclick="window.history.back()">
              <i class="bi bi-arrow-left"></i>
            </span>
            <h2 class="fw-light mb-0 ms-2" style="color:#fff;font-size:1.2rem">
              <span class="text-dark text-capitalize fw-bold">feeds</span>
            </h2>
          </div>
          <button id="postFeedBtn" onclick="createFeeds()" class="btn btn-sm fw-bold px-4 shadow-sm" style="background-color:#3a4553;border-color:#4a5568;color: #888}">
            Post to Feed <!-- #a88734 #111 -->
          </button>
        </div>
      </nav>

      <div class="container py-3 pb-6 mb-6">
        <div class="row justify-content-center">
          <input id="fileInput" onchange="uploadmultipleFiles(event)" type="file" accept="image/*" multiple class="d-none" />
          <!-- LEFT -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius:8px">
              <h5 class="fw-bold mb-4">Craft your update</h5>

              <div class="mb-4">
                <textarea id="feedContent" class="form-control border-0 bg-light p-3 fs-5"rows="6"placeholder="What's happening in your professional world?" style="border-radius:8px;resize:none"></textarea>
              </div>

              <div class="d-flex flex-wrap gap-2 mb-3" id="post-media-preview" style="min-height:80px">
                
              </div>

              <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center mb-3"
                   onclick="document.getElementById('fileInput').click()">
                <span class="small fw-bold text-muted">Click Here to Add image on your post</span>
              </div>

              <div class="p-3 rounded small" style="background-color:#fcf5e5;border:1px solid #f5d7bb">
                <span class="fw-bold">Pro Tip:</span> Posts with images get 3x more engagement from the monieFlow community.
              </div>
            </div>
          </div>

        </div>
      </div>

      <style>
        .hover-scale:hover { transform: scale(1.2); transition:0.2s; }
        .no-scrollbar::-webkit-scrollbar { display:none; }
      </style>
    </div>
    ${Footer({page: 'create-post'})}
  `;
}
