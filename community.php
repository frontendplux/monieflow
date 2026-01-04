<?php include __DIR__."/config/function.php";
$main = new main($conn);
include __DIR__."/headers/header.php"; ?>

<div class="container mt-4">
  <div class="card">
    <div class="card-body text-center">
      <h2>Welcome to Our Community</h2>
      <p class="text-muted">Connect, share ideas, and grow together</p>
      <button class="btn btn-primary">Join Community</button>
    </div>
  </div>
</div>

<div class="container mt-4">
  <div class="row">

    <!-- Posts Section -->
    <div class="col-lg-8">

      <!-- Create Post -->
      <div class="card mb-3">
        <div class="card-body">
          <textarea class="form-control mb-2" rows="3" placeholder="What's on your mind?"></textarea>
          <button class="btn btn-primary btn-sm bi bi-image"></button>
          <button class="btn btn-primary btn-sm">Post</button>
        </div>
      </div>

      <!-- Display Feeds -->
      <?php foreach ($main->feeds() as $feed): ?>
        <div class="card mb-3">
          <div class="card-body">
            <h6 class="fw-bold"><?= htmlspecialchars($feed['title']); ?></h6>
            <p><?= htmlspecialchars($feed['content']); ?></p>

            <!-- Comments -->
            <?php if (!empty($feed['comments'])): ?>
              <div class="border-top pt-2">
                <h6>Comments:</h6>
                <?php foreach ($feed['comments'] as $comment): ?>
                  <p class="small text-muted"><?= htmlspecialchars($comment['text']); ?></p>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mb-3 my-3">
              <button class="btn btn-outline-primary btn-sm bi bi-heart" onclick="sendlike(<?= $feed['id']; ?>)">
                <span class="ms-1">Like</span>
              </button>
              <button
                class="btn btn-outline-secondary btn-sm bi bi-chat"
                data-bs-toggle="collapse"
                data-bs-target="#commentForm<?= $feed['id']; ?>"
                aria-expanded="false">
                <span class="ms-1">Comment</span>
              </button>
            </div>

            <!-- Collapsible Comment Form -->
            <div class="collapse border-top pt-3" id="commentForm<?= $feed['id']; ?>">
              <div class="input-group mb-2">
                <input type="text" class="form-control" placeholder="Write a comment...">
                <button class="btn btn-primary" onclick="sendcomment(<?= $feed['id']; ?>)">Send</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header fw-bold">Members</div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">Samuel Sunday</li>
          <li class="list-group-item">Jane Doe</li>
          <li class="list-group-item">John Smith</li>
        </ul>
      </div>

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
