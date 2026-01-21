<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="/asset/bootstrap-5.3.2-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/asset/RemixIcon_Fonts_v4.1.0/rimix-icon/remixicon.css">
<link rel="stylesheet" href="/style.css">

<!-- Emoji Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emoji-mart@5.6.0/dist/browser.css">
<script src="https://cdn.jsdelivr.net/npm/emoji-mart@5.6.0/dist/browser.js"></script>

<!-- FFmpeg -->
<script src="https://unpkg.com/@ffmpeg/ffmpeg@0.12.6/dist/ffmpeg.min.js"></script>
<script src="https://unpkg.com/@ffmpeg/util@0.12.6/dist/util.min.js"></script>

<script src="/asset/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>

<title>Monieflow</title>
</head>

<body>

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

<!-- ================= SCRIPT ================= -->
<script>
/* -------- STATE -------- */
const state = {
  images: [],
  video: null,
  trimmedVideo: null,
  audio: null,
  audioCover: null,
  videoStart: 0,
  videoEnd: 0
};

/* -------- HELPERS -------- */
function clearAll() {
  state.images=[];
  state.video=null;
  state.trimmedVideo=null;
  state.audio=null;
  state.audioCover=null;
}

/* -------- IMAGE -------- */
postImages.onchange = e => {
  clearAll();
  state.images = [...e.target.files];
  renderPreview();
};

/* -------- AUDIO -------- */
postAudio.onchange = e => {
  clearAll();
  state.audio = e.target.files[0];
  renderPreview();
};

audioCover.onchange = e => {
  state.audioCover = e.target.files[0];
  renderPreview();
};

/* -------- VIDEO TRIM -------- */
const { FFmpeg } = FFmpegWASM;
const { fetchFile } = FFmpegUtil;
const ffmpeg = new FFmpeg();

postVideo.onchange = e => {
  clearAll();
  const file = e.target.files[0];
  if (!file) return;
  if (file.size > 15 * 1024 * 1024) {
    alert("Video must be under 15MB");
    return;
  }
  state.video = file;
  renderVideoEditor(file);
};

function renderVideoEditor(file) {
  previewArea.innerHTML = `
    <video id="vid" controls class="w-100 mb-2"></video>
    <input id="start" type="range" min="0" step="0.1">
    <input id="end" type="range" min="0" step="0.1">
    <button class="btn btn-sm btn-primary mt-2" onclick="trimVideo()">Trim</button>
  `;

  const v = document.getElementById("vid");
  v.src = URL.createObjectURL(file);

  v.onloadedmetadata = () => {
    start.max = end.max = v.duration;
    end.value = v.duration;
    start.oninput = e => state.videoStart = e.target.value;
    end.oninput = e => state.videoEnd = e.target.value;
  };
}

async function trimVideo() {
  if (state.videoEnd <= state.videoStart) return alert("Invalid trim");

  await ffmpeg.load();
  await ffmpeg.writeFile("in.mp4", await fetchFile(state.video));
  await ffmpeg.exec([
    "-ss", state.videoStart,
    "-to", state.videoEnd,
    "-i", "in.mp4",
    "-c:v", "libx264",
    "-crf", "28",
    "out.mp4"
  ]);
  const data = await ffmpeg.readFile("out.mp4");
  state.trimmedVideo = new File([data.buffer], "video.mp4",{type:"video/mp4"});
  renderPreview();
}

/* -------- PREVIEW -------- */
function renderPreview() {
  previewArea.innerHTML = "";

  state.images.forEach(i=>{
    previewArea.innerHTML += `<img src="${URL.createObjectURL(i)}" width="80" class="me-2 mb-2">`;
  });

  if (state.trimmedVideo)
    previewArea.innerHTML += `<video src="${URL.createObjectURL(state.trimmedVideo)}" controls class="w-100"></video>`;

  if (state.audio)
    previewArea.innerHTML += `
      ${state.audioCover?`<img src="${URL.createObjectURL(state.audioCover)}" width="120">`:""}
      <audio controls src="${URL.createObjectURL(state.audio)}"></audio>`;
}

/* -------- EMOJI PICKER -------- */
const textarea = document.getElementById("postText");
const pickerWrap = document.getElementById("emojiPickerWrapper");
let emojiOpen=false;

const picker = new EmojiMart.Picker({
  previewPosition:"none",
  onEmojiSelect:e=>insertEmoji(e.native)
});
pickerWrap.appendChild(picker);

function toggleEmojiPicker(){
  emojiOpen=!emojiOpen;
  pickerWrap.classList.toggle("d-none",!emojiOpen);
}

function insertEmoji(emoji){
  const s=textarea.selectionStart;
  textarea.value = textarea.value.slice(0,s)+emoji+textarea.value.slice(s);
  textarea.selectionStart = textarea.selectionEnd = s+emoji.length;
  textarea.focus();
}

/* -------- SUBMIT -------- */
async function submitPost(){
  if(state.audio && !state.audioCover)
    return alert("Audio needs cover");

  const fd=new FormData();
  fd.append("text",postText.value);
  state.images.forEach(i=>fd.append("images[]",i));
  if(state.trimmedVideo) fd.append("video",state.trimmedVideo);
  if(state.audio) fd.append("audio",state.audio);
  if(state.audioCover) fd.append("cover",state.audioCover);

  console.log("READY TO SEND", [...fd.entries()]);
  alert("Feed created successfully");
}
</script>

</body>
</html>
