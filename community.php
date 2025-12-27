<?php include __DIR__."/headers/header.php"; ?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">CommunityHub</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Community</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Members</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Events</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Header -->
<div class="container mt-4">
  <div class="card">
    <div class="card-body text-center">
      <h2>Welcome to Our Community</h2>
      <p class="text-muted">Connect, share ideas, and grow together</p>
      <button class="btn btn-primary">Join Community</button>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="container mt-4">
  <div class="row">

    <!-- Posts Section -->
    <div class="col-lg-8">

      <!-- Create Post -->
      <div class="card mb-3">
        <div class="card-body">
          <textarea class="form-control mb-2" rows="3" placeholder="What's on your mind?"></textarea>
          <button class="btn btn-primary btn-sm">Post</button>
        </div>
      </div>

      <!-- Post -->
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="fw-bold">Samuel Sunday</h6>
          <p>Excited to be part of this amazing community! 🚀</p>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm">Like</button>
            <button class="btn btn-outline-secondary btn-sm">Comment</button>
          </div>
        </div>
      </div>

   <!-- Post -->
<div class="card mb-3">
  <div class="card-body">

    <h6 class="fw-bold">Jane Doe</h6>
    <p>Anyone interested in collaborating on a new project?</p>
 <!-- Collapsible Comment Section -->
    <div class="collapse border-top pt-3" id="commentForm1">
      <div class="input-group mb-2">
        <input type="text" class="form-control" placeholder="Write a comment...">
        <button class="btn btn-primary">Send</button>
      </div>

      <div class="small text-muted">
        <strong>Samuel:</strong> I'm interested 🚀
      </div>
    </div>
    <!-- Action Buttons -->
    <div class="d-flex gap-2 mb-3 my-3">
      <button class="btn btn-outline-primary btn-sm">Like</button>

      <!-- Toggle Button -->
      <button
        class="btn btn-outline-secondary btn-sm"
        data-bs-toggle="collapse"
        data-bs-target="#commentForm1"
        aria-expanded="false">
        Comment
      </button>
    </div>

   

  </div>
</div>


    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">

      <!-- Members -->
      <div class="card mb-3">
        <div class="card-header fw-bold">Members</div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">Samuel Sunday</li>
          <li class="list-group-item">Jane Doe</li>
          <li class="list-group-item">John Smith</li>
        </ul>
      </div>

      <!-- Events -->
      <div class="card">
        <div class="card-header fw-bold">Upcoming Events</div>
        <div class="card-body">
          <h6>Community Meetup</h6>
          <p class="text-muted mb-1">📅 Jan 20, 2026</p>
          <button class="btn btn-outline-primary btn-sm">View Details</button>
        </div>
      </div>

    </div>
  </div>
</div>


<?php include __DIR__."/headers/footer.php"; ?>
</body>
</html>
