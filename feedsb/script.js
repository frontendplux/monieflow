// ────────────────────────────────────────────────
// POSTING + MEDIA
// ────────────────────────────────────────────────

let selectedImages = [];
const imageInput = document.getElementById('image-open');
const musicInput = document.getElementById('music-open');
const player = document.getElementById('hiddenPlayer');

imageInput.addEventListener('change', function() {
    const files = Array.from(this.files);
    files.forEach(file => {
        selectedImages.push(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = `<img src="${e.target.result}" class="preview-img">`;
            document.getElementById('imagePreviewContainer').insertAdjacentHTML('beforeend', img);
        };
        reader.readAsDataURL(file);
    });
});

function checkCoverBeforeAudio() {
    if (selectedImages.length === 0) {
        droppySammy('info', 'Auth Error', "Please upload at least one image to serve as a cover for your audio.");
        return;
    }
    musicInput.click();
}

musicInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        document.getElementById('audioPreviewContainer').classList.remove('d-none');
        document.getElementById('audioFileName').innerText = file.name;
        const url = URL.createObjectURL(file);
        player.src = url;
    }
});

const playToggle = document.getElementById('playToggle');
const playIcon = document.getElementById('playIcon');

playToggle.addEventListener('click', () => {
    if (player.paused) {
        player.play();
        playIcon.className = 'ri-pause-fill';
    } else {
        player.pause();
        playIcon.className = 'ri-play-fill';
    }
});

player.ontimeupdate = () => {
    const prog = (player.currentTime / player.duration) * 100;
    document.getElementById('audioProgress').style.width = prog + '%';
};

function removeAudio() {
    player.pause();
    document.getElementById('audioPreviewContainer').classList.add('d-none');
    musicInput.value = "";
}

// ────────────────────────────────────────────────
// EMOJI PICKER – FIXED (inserts into comment drawer input)
// ────────────────────────────────────────────────

document.getElementById('emojiBtn')?.addEventListener('click', function(e) {
    e.stopPropagation();
    const picker = document.getElementById('customEmojiPicker');
    picker.classList.toggle('d-none');

    const closeListener = function(evt) {
        if (!picker.contains(evt.target) && evt.target !== this) {
            picker.classList.add('d-none');
            document.removeEventListener('click', closeListener);
        }
    };
    document.addEventListener('click', closeListener.bind(this));
});

document.querySelectorAll('#customEmojiPicker .emoji-grid span').forEach(span => {
    span.addEventListener('click', () => {
        const input = document.querySelector('#commentDrawer input[type="text"]');
        if (input) {
            const start = input.selectionStart || input.value.length;
            const end = input.selectionEnd || input.value.length;
            input.value = input.value.substring(0, start) + span.innerText + input.value.substring(end);
            input.selectionStart = input.selectionEnd = start + span.innerText.length;
            input.focus();
            document.getElementById('customEmojiPicker').classList.add('d-none');
        }
    });
});

// ────────────────────────────────────────────────
// POST UPLOAD
// ────────────────────────────────────────────────

function uploadPosttodb(e) {
    e.innerHTML = "posting...";
    e.disabled = true;

    const formData = new FormData();
    formData.append("action", "upload");
    const postText = document.getElementById('postText').value.trim();
    formData.append("text", postText);

    selectedImages.forEach(file => formData.append("images[]", file));

    if (musicInput.files[0]) formData.append("audio", musicInput.files[0]);

    fetch("/feeds/req.php", { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            droppySammy('success', 'Success', "Post uploaded successfully!");
            document.getElementById('postForm').reset();
            document.getElementById('imagePreviewContainer').innerHTML = "";
            document.getElementById('audioPreviewContainer').classList.add('d-none');
            selectedImages = [];
            e.innerHTML = "post";
            e.disabled = false;
            window.location.reload();
        } else {
            droppySammy('danger', 'Error', "Upload failed: " + data.message);
            e.innerHTML = "post";
            e.disabled = false;
        }
    })
    .catch(error => {
        console.error("Error uploading:", error);
        droppySammy('danger', 'Error', "An error occurred while uploading.");
        e.innerHTML = "post";
        e.disabled = false;
    });
}

// ────────────────────────────────────────────────
// FEED LIKE
// ────────────────────────────────────────────────

document.addEventListener("click", function(e) {
  const btn = e.target.closest(".like-btn");
  if (!btn) return;

  const feedId = btn.dataset.feedId;

  fetch("/feeds/req.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "like", feed_id: feedId })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "liked") {
      btn.innerHTML = `<i class="ri-heart-fill me-1 text-danger"></i> ${data.count}`;
      triggerBubble(btn);
    } else if (data.status === "unliked") {
      btn.innerHTML = `<i class="ri-heart-3-line me-1"></i> ${data.count}`;
    }
  });
});

// ────────────────────────────────────────────────
// COMMENT SYSTEM – ONE INPUT FOR COMMENT & REPLY
// ────────────────────────────────────────────────

let currentFeedId = null;
let replyingTo = null;          // parent comment ID
let replyingUsername = '';      // for placeholder

// Open comment drawer
document.addEventListener("click", function(e) {
  const btn = e.target.closest(".comment-btn");
  if (!btn) return;

  currentFeedId = btn.dataset.feedId;

  const form = document.querySelector("#commentDrawer form");
  if (form) form.dataset.feedId = currentFeedId;

  // Reset to comment mode
  replyingTo = null;
  replyingUsername = '';
  const input = form.querySelector("input");
  input.placeholder = "Write a comment...";

  loadComments(currentFeedId);

  const drawer = new bootstrap.Offcanvas(document.getElementById("commentDrawer"));
  drawer.show();
});

// Load & render comments (with Like/Reply counts)
function loadComments(feedId) {
  const list = document.getElementById("drawerCommentList");
  list.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading...</p></div>';

  fetch("/feeds/req.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "comments_list", feed_id: feedId })
  })
  .then(res => res.json())
  .then(comments => {
    list.innerHTML = "";

    if (!comments.length) {
      list.innerHTML = `
        <div class="text-center text-muted py-5">
          <i class="ri-chat-smile-2-line fs-1 opacity-25"></i>
          <p>Be the first to say something!</p>
        </div>`;
      return;
    }

    const map = {};
    const top = [];

    comments.forEach(c => {
      c.replies = [];
      map[c.id] = c;
      if (c.parent_id == 0) top.push(c);
      else if (map[c.parent_id]) map[c.parent_id].replies.push(c);
    });

    function render(c, level = 0) {
      const d = document.createElement("div");
      d.className = level ? "ms-5 ps-3 border-start mb-3" : "mb-3";
      d.innerHTML = `
        <div class="d-flex align-items-start">
          <a href="/profile/?user=${c.user_id}">
            <img src="/uploads/${c.profile_pic || 'default.jpg'}" class="rounded-circle me-2" width="35">
          </a>
          <div class="flex-grow-1">
            <div class="fw-bold">${c.username}</div>
            <div>${c.text}</div>
            <small class="text-muted">${timeAgo(c.created_at)}</small>
            <button class="btn btn-sm text-muted comment-like-btn" data-comment-id="${c.id}">
              <i class="${c.user_liked_comment ? 'ri-heart-fill text-danger' : 'ri-heart-3-line'} me-1"></i> Like (${c.like_count || 0})
            </button>

            <button class="btn btn-sm text-muted reply-btn ms-2" data-parent-id="${c.id}" data-username="${c.username}">
              Reply (${c.replies.length})
            </button>
            <br />
            ${c.replies.length ? `
              <button class="btn btn-link btn-sm text-muted mt-1 view-replies" data-count="${c.replies.length}">
                View ${c.replies.length} ${c.replies.length === 1 ? 'reply' : 'replies'}
              </button>
              <div class="replies-container d-none mt-2">
                ${c.replies.map(r => render(r, 1).outerHTML).join('')}
              </div>
            ` : ''}
          </div>
        </div>`;

      // Toggle replies
      d.querySelector('.view-replies')?.addEventListener('click', function() {
        const cont = this.nextElementSibling;
        cont.classList.toggle('d-none');
        this.textContent = cont.classList.contains('d-none') 
          ? `View ${this.dataset.count} ${this.dataset.count == 1 ? 'reply' : 'replies'}`
          : 'Hide replies';
      });

      return d;
    }

    top.forEach(c => list.appendChild(render(c)));
  })
  .catch(() => {
    list.innerHTML = '<p class="text-danger text-center py-5">Could not load comments</p>';
  });
}

// Set reply mode (same input)
document.addEventListener("click", e => {
  const btn = e.target.closest(".reply-btn");
  if (!btn) return;

  replyingTo = btn.dataset.parentId;
  replyingUsername = btn.dataset.username;

  const input = document.querySelector("#commentDrawer input");
  input.placeholder = `Reply to @${replyingUsername}...`;
  input.focus();
});

// Submit (comment or reply – same input)
document.querySelector("#commentDrawer form")?.addEventListener("submit", function(e) {
  e.preventDefault();

  const input = this.querySelector("input");
  const text = input.value.trim();
  if (!text) return;

  const payload = {
    action: replyingTo ? "reply" : "comment",
    feed_id: currentFeedId,
    comment_text: text
  };
  if (replyingTo) payload.parent_id = replyingTo;

  fetch("/feeds/req.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "commented" || data.status === "replied") {
      input.value = "";
      input.placeholder = "Write a comment...";
      replyingTo = null;
      replyingUsername = '';
      loadComments(currentFeedId);

      const btn = document.querySelector(`.comment-btn[data-feed-id="${currentFeedId}"]`);
      if (btn && data.comment_count != null) {
        btn.innerHTML = `<i class="ri-chat-1-line me-1"></i> ${data.comment_count}`;
        triggerPulse(btn);
      }
    } else {
      alert(data.message || "Failed to post");
    }
  })
  .catch(err => {
    console.error(err);
    alert("Error – try again");
  });
});

// Comment Like
document.addEventListener("click", function(e) {
  const btn = e.target.closest(".comment-like-btn");
  if (!btn) return;

  const commentId = btn.dataset.commentId;

  fetch("/feeds/req.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "comment_like",
      comment_id: commentId
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "liked") {
      btn.innerHTML=`<i class="ri-heart-fill me-1 text-danger"></i> Like (${data.like_count})`
      triggerBubble(btn);
    } else if (data.status === "unliked") {
      btn.innerHTML = `<i class="ri-heart-line me-1 text-danger"></i> Like (${data.like_count})`;
    }
  })
  .catch(err => console.error("Comment like error:", err));
});

// ────────────────────────────────────────────────
// POLLING + ANIMATIONS
// ────────────────────────────────────────────────

function pollEvents() {
  fetch("/feeds/req.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "events" })
  })
  .then(res => res.json())
  .then(events => {
    events.forEach(evt => {
      switch (evt.event_type) {
        case "like":
          const likeBtn = document.querySelector(`.like-btn[data-feed-id="${evt.data.feed_id}"]`);
          if (likeBtn) {
            likeBtn.innerHTML = `<i class="ri-heart-fill me-1 text-danger"></i> ${evt.data.count}`;
            triggerBubble(likeBtn);
          }
          break;

        case "comment":
        case "reply":
          const commentBtn = document.querySelector(`.comment-btn[data-feed-id="${evt.data.feed_id}"]`);
          if (commentBtn) {
            commentBtn.innerHTML = `<i class="ri-chat-1-line me-1"></i> ${evt.data.comment_count}`;
            triggerPulse(commentBtn);
          }
          break;

        case "comment_like":
          const clBtn = document.querySelector(`.comment-like-btn[data-comment-id="${evt.data.comment_id}"]`);
          if (clBtn) {
            clBtn.innerHTML = `<i class="ri-heart-fill me-1 text-danger"></i> ${evt.data.count}`;
            triggerBubble(clBtn);
          }
          break;

        case "share":
          const shareBtn = document.querySelector(`.share-btn[data-feed-id="${evt.data.feed_id}"]`);
          if (shareBtn) triggerShake(shareBtn);
          break;

        case "follow":
          showToast(`${evt.data.username} followed you`);
          break;

        case "message":
          showToast(`New message from ${evt.data.username}`);
          break;

        case "notification":
          showToast(evt.data.text);
          break;
      }
    });
  });
}

setInterval(pollEvents, 5000);

function triggerBubble(btn) {
  const bubble = document.createElement("span");
  bubble.className = "bubble";
  bubble.style.width = "12px";
  bubble.style.height = "12px";
  bubble.style.left = `${btn.offsetLeft + btn.offsetWidth/2}px`;
  bubble.style.top = `${btn.offsetTop}px`;
  bubble.style.position = "absolute";
  btn.parentElement.appendChild(bubble);
  setTimeout(() => bubble.remove(), 1000);
}

function triggerPulse(btn) {
  btn.classList.add("pulse");
  setTimeout(() => btn.classList.remove("pulse"), 600);
}

function triggerShake(btn) {
  btn.classList.add("shake");
  setTimeout(() => btn.classList.remove("shake"), 500);
}

function showToast(msg) {
  const toast = document.createElement("div");
  toast.className = "toast";
  toast.innerText = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// ────────────────────────────────────────────────
// FEED LOADING
// ────────────────────────────────────────────────

let offset = 0;
let loading = false;

function fetchFeeds(initial = false) {
  if (loading) return;
  loading = true;

  document.getElementById("roots").insertAdjacentHTML("beforeend",
    document.getElementById("preloader-loding").outerHTML
  );

  fetch("/feeds/req.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "feeds", limit: offset })
  })
  .then(res => res.json())
  .then(rows => {
    const preloader = document.querySelector("#roots #preloader-loding");
    if (preloader) preloader.remove();

    rows.forEach(post => {
      let images = [];
      try { images = JSON.parse(post.images || "[]"); } catch (e) {}

      let imageHtml = images.length
        ? images.map(img => `<img src="/uploads/images/${img}" class="w-100" alt="">`).join("")
        : "";

      let audioHtml = "";
      if (post.audio) {
        audioHtml = `
          <div class="audio-overlay">
            ${imageHtml}
            <button class="btn btn-audio" data-audio="/uploads/audio/${post.audio}">
              <i class="ri-volume-up-fill"></i>
            </button>
            <audio class="hidden-audio" src="/uploads/audio/${post.audio}"></audio>
          </div>`;
        imageHtml = "";
      }

      const card = `
        <div class="card mb-3">
          <div class="card-header d-flex align-items-center">
            <a href="/profile/?user=${post.user_id}">
              <img src="/uploads/${post.profile_pic}" class="rounded-circle me-3" width="48" height="48">
            </a>
            <a href="/profile/?user=${post.user_id}" class="d-block text-dark text-decoration-none">
              <div class="fw-bold">
                ${post.first_name} ${post.last_name}
                ${post.verified ? `<i class="ri-verified-badge-fill text-primary"></i>` : ""}
              </div>
              <small class="text-muted">${post.country ?? `outlaw`} • ${timeAgo(post.created_at)}</small>
            </a>
            <button class="btn ms-auto text-muted"><i class="ri-more-fill"></i></button>
          </div>
          <div class="card-body pt-0">
            <p class="mb-3 px-2">${post.text}</p>
          </div>
          <div class="post-images mb-3">
            ${imageHtml}
            ${audioHtml}
          </div>
          <div class="action-bar d-flex gap-3">
            <button class="btn btn-action like-btn" data-feed-id="${post.id}">
              ${post.user_liked == 1 
                ? `<i class="ri-heart-fill me-1 text-danger"></i>` 
                : `<i class="ri-heart-3-line me-1"></i>`} ${post.like_count}
            </button>
            <button class="btn btn-action comment-btn" data-feed-id="${post.id}">
              <i class="ri-chat-1-line me-1"></i> ${post.comment_count}
            </button>
            <button class="btn btn-action" data-bs-toggle="modal" data-bs-target="#shareModal">
              <i class="ri-send-plane-2-line me-1"></i> Share
            </button>
          </div>
        </div>
      `;

      document.getElementById("roots").insertAdjacentHTML("beforeend", card);
    });

    offset += rows.length;
    loading = false;
  })
  .catch(err => {
    console.error("Error loading feeds:", err);
    loading = false;
  });
}

// ────────────────────────────────────────────────
// STARTUP + SCROLL + AUDIO
// ────────────────────────────────────────────────

document.addEventListener("DOMContentLoaded", () => fetchFeeds(true));

window.addEventListener("scroll", () => {
  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
    fetchFeeds();
  }
});

document.addEventListener("click", function(e) {
  if (e.target.closest(".btn-audio")) {
    const btn = e.target.closest(".btn-audio");
    const audio = btn.nextElementSibling;
    document.querySelectorAll(".hidden-audio").forEach(a => {
      if (a !== audio) {
        a.pause();
        a.previousElementSibling.innerHTML = '<i class="ri-volume-up-fill"></i>';
      }
    });
    if (audio.paused) {
      audio.play();
      btn.innerHTML = '<i class="ri-pause-fill"></i>';
    } else {
      audio.pause();
      btn.innerHTML = '<i class="ri-volume-up-fill"></i>';
    }
  }
}); 


function timeAgo(dateString) {
  const s = Math.floor((Date.now() - new Date(dateString)) / 1000);

  if (s < 60) return "just now";
  if (s < 3600) return Math.floor(s / 60) + " min" + (s >= 120 ? "s" : "") + " ago";
  if (s < 86400) return Math.floor(s / 3600) + " hour" + (s >= 7200 ? "s" : "") + " ago";
  if (s < 604800) return Math.floor(s / 86400) + " day" + (s >= 172800 ? "s" : "") + " ago";
  if (s < 2592000) return Math.floor(s / 604800) + " week" + (s >= 1209600 ? "s" : "") + " ago";
  if (s < 31536000) return Math.floor(s / 2592000) + " month" + (s >= 5184000 ? "s" : "") + " ago";
  return Math.floor(s / 31536000) + " year" + (s >= 63072000 ? "s" : "") + " ago";
}