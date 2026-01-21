<?php include __DIR__."/compo/header.php"; ?>

<div class="container position-relative">
  <div class="row">

    <!-- LEFT SIDEBAR -->
    <div class="col d-none d-sm-block">
      <div class="sidebar-sticky">
        <div class="sidebar-menu p-3" style="max-width: 300px; height: 100vh;">
  <div class="list-group list-group-flush bg-transparent">
    
    <a href="#" class="list-group-item list-group-item-action border-0 bg-transparent d-flex align-items-center rounded-3 mb-1">
      <img src="https://via.placeholder.com/36" class="rounded-circle me-3" alt="Profile" style="width: 36px; height: 36px; object-fit: cover;">
      <span class="fw-semibold">Samuel Sunday</span>
    </a>

    <a href="#" class="list-group-item list-group-item-action border-0 bg-transparent d-flex align-items-center rounded-3 mb-1">
      <div class="me-3 d-flex align-items-center justify-content-center" style="width: 36px;">
        <i class="ri-circle-line fs-4" style="color: #0566FF;"></i>
      </div>
      <span class="fw-semibold">Meta AI</span>
    </a>

    <a href="#" class="list-group-item list-group-item-action border-0 bg-transparent d-flex align-items-center rounded-3 mb-1">
      <div class="me-3 d-flex align-items-center justify-content-center" style="width: 36px;">
        <i class="ri-team-fill fs-4 text-primary"></i>
      </div>
      <span class="fw-semibold">Friends</span>
    </a>

    <a href="#" class="list-group-item list-group-item-action border-0 bg-transparent d-flex align-items-center rounded-3 mb-1">
      <div class="me-3 d-flex align-items-center justify-content-center" style="width: 36px;">
        <i class="ri-history-fill fs-4 text-primary"></i>
      </div>
      <span class="fw-semibold">Memories</span>
    </a>

    <a href="#" class="list-group-item list-group-item-action border-0 bg-transparent d-flex align-items-center rounded-3 mb-1">
      <div class="me-3 d-flex align-items-center justify-content-center" style="width: 36px;">
        <i class="ri-bookmark-fill fs-4" style="color: #B229B6;"></i>
      </div>
      <span class="fw-semibold">Saved</span>
    </a>

    <a href="#" class="list-group-item list-group-item-action border-0 bg-transparent d-flex align-items-center rounded-3 mb-1">
      <div class="me-3 d-flex align-items-center justify-content-center" style="width: 36px;">
        <i class="ri-store-2-fill fs-4 text-primary"></i>
      </div>
      <span class="fw-semibold">Marketplace</span>
    </a>

    <a href="#" class="list-group-item list-group-item-action border-0 bg-transparent d-flex align-items-center rounded-3 mb-1">
      <div class="me-3 d-flex align-items-center justify-content-center" style="width: 36px;">
        <i class="ri-bar-chart-2-fill fs-4 text-primary"></i>
      </div>
      <span class="fw-semibold">Ads Manager</span>
    </a>

    <a href="#" class="list-group-item list-group-item-action border-0 bg-transparent d-flex align-items-center rounded-3 mb-1">
      <div class="me-3 d-flex align-items-center justify-content-center rounded-circle bg-secondary-subtle" style="width: 36px; height: 36px;">
        <i class="ri-arrow-down-s-line fs-5"></i>
      </div>
      <span class="fw-semibold">See more</span>
    </a>

  </div>

  <div class="mt-3 px-3">
    <small class="text-muted" style="font-size: 0.75rem;">
      Privacy · Terms · Advertising · Ad choices <i class="ri-play-fill small"></i> · Cookies · More
    </small>
  </div>
</div>
      </div>
    </div>

    <!-- FEED -->
    <div class="col-sm-5 p-2 gap-2" id="feed-post">
      <div class="d-flex gap-2 p-2 align-items-center rounded bg-white w-100" style="margin-top: 75px;">
        <div>
          <img src="/img/post-img3.jpg"
               class="rounded-circle object-fit-cover"
               style="width:40px;height:40px;object-position:top center;">
        </div>

        <div class="w-100">
          <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#feedPostModal" class="form-control text-decoration-none py-3" style="font-size:x-small;">
            what is on your mind?
          </a>
        </div>

        <!-- <div class="d-flex align-items-center fs-3 gap-2">
          <a class="btn btn-light rounded-circle"><i class="ri-video-fill"></i></a>
          <a class="btn btn-light rounded-circle"><i class="ri-multi-image-fill"></i></a>
          <a class="btn btn-light rounded-circle"><i class="ri-chat-voice-fill"></i></a>
        </div> -->
      </div>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="col d-none d-sm-block">
      <div class="sidebar-sticky">
        
        <div class="group-chats-section p-3" style="max-width: 320px;">
          <h6 class="text-muted fw-bold mb-3" style="font-size: 17px;">Group chats</h6>
          
          <a href="#" class="text-decoration-none d-flex align-items-center list-group-item-action p-2 rounded-3">
            <div class="me-3 d-flex align-items-center justify-content-center bg-secondary-subtle rounded-circle" style="width: 36px; height: 36px;">
              <i class="ri-add-line fs-5 text-dark"></i>
            </div>
            <span class="fw-semibold text-dark">Create group chat</span>
          </a>
        </div>
      </div>
    </div>

  </div>
</div>
<script>
  fetchfeeds();
  async function fetchfeeds() {
    document.getElementById('feed-post').innerHTML +=
      await getfileItem('/member/feeds/feeds.php');
  }
</script>
