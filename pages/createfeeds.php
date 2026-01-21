<!-- ================= CREATE POST MODAL ================= -->
<div class="modal fade" id="feedPostModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5>Create Post</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body position-relative">

        <textarea id="postText" class="form-control mb-2" rows="3"
          placeholder="What's on your mind? 😊"></textarea>

        <!-- Emoji Picker Container -->
        <div id="emojiPickerWrapper"
             class="position-absolute d-none"
             style="z-index:1056;"></div>

        <div class="d-flex gap-2 mb-3 flex-wrap">
          <button class="btn btn-light btn-sm" onclick="toggleEmojiPicker()">😀 Emoji</button>

          <button class="btn btn-light btn-sm" onclick="postImages.click()">📷 Images</button>
          <input id="postImages" type="file" multiple accept="image/*" hidden>

          <button class="btn btn-light btn-sm" onclick="postVideo.click()">🎥 Video</button>
          <input id="postVideo" type="file" accept="video/*" hidden>

          <button class="btn btn-light btn-sm" onclick="postAudio.click()">🎵 Audio</button>
          <input id="postAudio" type="file" accept="audio/*" hidden>

          <button class="btn btn-light btn-sm" onclick="audioCover.click()">🖼 Audio Cover</button>
          <input id="audioCover" type="file" accept="image/*" hidden>
        </div>

        <div id="previewArea"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="submitPost()">Post</button>
      </div>

    </div>
  </div>
</div>
