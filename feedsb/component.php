


<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Share to...</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center bg-light p-2 rounded-3 mb-4">
                    <i class="ri-link m-2 text-muted"></i>
                    <input type="text" class="form-control form-control-sm border-0 bg-transparent" id="shareLinkInput" value="https://socialflow.me/p/12345" readonly>
                    <button class="btn btn-primary btn-sm rounded-2 px-3" onclick="copyToClipboard()">Copy</button>
                </div>

                <div class="row g-3 text-center">
                    <div class="col-4">
                        <a href="#" class="share-icon-link text-decoration-none">
                            <div class="share-icon bg-whatsapp"><i class="ri-whatsapp-line"></i></div>
                            <span class="small text-dark">WhatsApp</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="share-icon-link text-decoration-none">
                            <div class="share-icon bg-facebook"><i class="ri-facebook-fill"></i></div>
                            <span class="small text-dark">Facebook</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="share-icon-link text-decoration-none">
                            <div class="share-icon bg-instagram"><i class="ri-instagram-line"></i></div>
                            <span class="small text-dark">Instagram</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="share-icon-link text-decoration-none">
                            <div class="share-icon bg-twitter-x"><i class="ri-twitter-x-fill"></i></div>
                            <span class="small text-dark">Twitter</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="share-icon-link text-decoration-none">
                            <div class="share-icon bg-messenger"><i class="ri-messenger-line"></i></div>
                            <span class="small text-dark">Chat</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="share-icon-link text-decoration-none">
                            <div class="share-icon bg-secondary"><i class="ri-more-fill"></i></div>
                            <span class="small text-dark">More</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
  /* Share Grid Styles */
.share-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin: 0 auto 5px;
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.share-icon-link:hover .share-icon {
    transform: scale(1.15);
}

/* Brand Colors */
.bg-whatsapp { background: #25D366; }
.bg-facebook { background: #1877F2; }
.bg-instagram { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
.bg-twitter-x { background: #000000; }
.bg-messenger { background: #0084FF; }

#shareLinkInput:focus {
    box-shadow: none;
}
</style>




<div class="offcanvas offcanvas-bottom shadow-lg" tabindex="-1" id="commentDrawer" style="height: 80vh; border-radius: 25px 25px 0 0;">
    <div class="offcanvas-header justify-content-center">
        <div style="width: 40px; height: 5px; background: #e0e0e0; border-radius: 10px;"></div>
    </div>
    <div class="offcanvas-body pt-0">
        <div class="container" style="max-width: 600px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Comments</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div id="drawerCommentList" class="mb-5 pb-5">
                <div class="text-center text-muted py-5">
                    <i class="ri-chat-smile-2-line fs-1 opacity-25"></i>
                    <p>Be the first to say something!</p>
                </div>
            </div>

            <div class="position-absolute bottom-0 start-0 w-100 bg-white p-3 border-top">
                <div class="container" style="max-width: 600px;">
                    <form class="d-flex align-items-center gap-2">
                        <img src="https://i.pravatar.cc/150?u=me" class="rounded-circle" width="35" height="35">
                        <div class="input-group">
                            <input type="text" class="form-control border-0 bg-light" placeholder="Write a comment..." style="border-radius: 20px;">
                            <button class="btn text-primary fw-bold" type="submit">Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>





<style>
/* Music Tag Style */
.music-tag {
    display: inline-flex;
    align-items: center;
    background: #fff5f5;
    border: 1px solid #feb2b2;
    color: #c53030;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
}

/* Emoji Picker Container */
.custom-emoji-card {
    position: absolute;
    bottom: 50px;
    right: 0;
    background: white;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-radius: 15px;
    padding: 10px;
    z-index: 1000;
    width: 200px;
    border: 1px solid #eee;
    transform-origin: bottom right;
    animation: emojiPop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes emojiPop {
    from { transform: scale(0.5); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    text-align: center;
}

.emoji-grid span {
    cursor: pointer;
    font-size: 1.4rem;
    transition: transform 0.2s;
    display: block;
}

.emoji-grid span:hover {
    transform: scale(1.3);
}
</style>

<style>
.preview-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #eee;
}

.audio-card {
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.audio-wave-icon {
    width: 40px;
    height: 40px;
    background: #6366f1;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.2s;
}

.audio-wave-icon:hover { transform: scale(1.1); }

.custom-emoji-card {
    position: absolute; bottom: 50px; right: 0; background: white;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 15px; padding: 10px;
    z-index: 1000; width: 180px; animation: emojiPop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes emojiPop { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.emoji-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; cursor: pointer; }
</style>

<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
      <div class="modal-header border-0 pt-4 px-4">
        <h5 class="modal-title fw-bold" id="createPostModalLabel">Create Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        
      </div>

      <div class="modal-body px-4">
        <div class="d-flex align-items-center mb-3">
          <img src="https://i.pravatar.cc/150?u=me" class="rounded-circle me-2" width="40" height="40">
          <span class="fw-bold"><?= json_decode($main->getUserData()['data']['profile'], true)['username'] ?></span>
        </div>

        <form id="postForm">
          <textarea class="form-control border-0 fs-5 ps-0" id="postText" rows="3" placeholder="What's on your mind?" style="resize: none; box-shadow: none;"></textarea>
          
          <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2 mb-3"></div>
          
          <div id="audioPreviewContainer" class="mb-3 d-none">
            <div class="audio-card p-2 d-flex align-items-center gap-3">
              <div class="audio-wave-icon" id="playToggle">
                <i class="ri-play-fill" id="playIcon"></i>
              </div>
              <div class="flex-grow-1">
                <small class="fw-bold d-block text-truncate" id="audioFileName"></small>
                <div class="progress" style="height: 4px;">
                  <div id="audioProgress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
              <button type="button" class="btn-sm btn-close" onclick="removeAudio()"></button>
            </div>
          </div>

          <audio id="hiddenPlayer"></audio>

          <div class="d-flex align-items-center justify-content-between border rounded-3 p-2">
            <span class="small fw-bold text-muted ms-2">Add to your post</span>
            <div class="d-flex gap-2">
              <button type="button" onclick="document.getElementById('image-open').click()" class="btn btn-light text-success rounded-circle">
                <i class="ri-image-add-line"></i>
                <input type="file" id="image-open" class="d-none" accept="image/*" multiple>
              </button>

              <button type="button" id="musicBtn" onclick="checkCoverBeforeAudio()" class="btn btn-light text-danger rounded-circle">
                <i class="ri-music-2-line"></i>
                <input type="file" id="music-open" class="d-none" accept="audio/*">
              </button>

              <div class="position-relative">
                <button type="button" id="emojiBtn" class="btn btn-light text-warning rounded-circle"><i class="ri-emotion-line"></i></button>
                <div id="customEmojiPicker" class="custom-emoji-card d-none">
                    <div class="emoji-grid">
                        <span>😊</span><span>😂</span><span>🥰</span><span>😎</span>
                        <span>😍</span><span>🔥</span><span>✨</span><span>❤️</span><span>🙌</span>
                        <span>🤔</span><span>🚀</span><span>🎉</span>
                        <span>🎵</span><span>🎸</span><span>🌈</span><span>⭐</span>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer border-0 p-4">
        <button type="button" onclick="uploadPosttodb(this)" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 12px; background: linear-gradient(45deg, #6366f1, #a855f7); border: none;">Post</button>
      </div>
    </div>
  </div>
</div>



<style>
/* Container for image + audio icon */
.audio-overlay {
  position: relative;
  display: inline-block;
  width: 100%;
}

.audio-overlay img {
  display: block;
  width: 100%;
  height: auto;
  border-radius: 8px;
}

/* Audio button overlay */
.audio-overlay .btn-audio {
  position: absolute;
  bottom: 12px;
  right: 12px;
  background: rgba(0,0,0,0.65);
  color: #fff;
  border: none;
  border-radius: 50%;
  padding: 14px;
  font-size: 22px;
  line-height: 1;
  cursor: pointer;
  transition: transform 0.3s ease, background 0.3s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

/* Hover effect */
.audio-overlay .btn-audio:hover {
  background: rgba(255,255,255,0.9);
  color: #000;
  transform: scale(1.15);
}

/* Booming animation when audio is playing */
.audio-overlay .btn-audio.playing {
  animation: boomPulse 1s infinite;
}

@keyframes boomPulse {
  0% {
    transform: scale(1);
    box-shadow: 0 0 0 rgba(255,255,255,0.7);
  }
  50% {
    transform: scale(1.2);
    box-shadow: 0 0 20px rgba(255,255,255,0.9);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 0 0 rgba(255,255,255,0.7);
  }
}

.hidden-audio {
  display: none;
}

</style>