<?php  
include __dir__."/../../config/function.php";
$main = new Main($conn);
$user_data = $main->userData();
$viewer_id = $user_data['data']['id'] ?? 0;

// Fixed function: Added 'global $conn' so it can see the database
function get_all_feeds($limit = 0) {
    global $conn, $viewer_id; 
    $stmt = $conn->prepare("
        SELECT 
            f.*, u.username, u.profile,
            (SELECT COUNT(*) FROM likes WHERE feed_id = f.id) AS total_likes,
            (SELECT COUNT(*) FROM comments WHERE feed_id = f.id) AS total_comments,
            EXISTS(SELECT 1 FROM likes WHERE feed_id = f.id AND user_id = ?) AS is_liked
        FROM feeds f
        LEFT JOIN users u ON f.user_id = u.id
        WHERE f.status = 'active'
        GROUP BY f.id
        ORDER BY f.id DESC 
        LIMIT ?, 60
    ");
    $stmt->bind_param("ii", $viewer_id, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<?php foreach (get_all_feeds(0) as $feed): 
    $media_items = json_decode($feed['media'], true) ?? [];
    $isLiked = $feed['is_liked'];
?>
<div class="w-100 bg-white overflow-hidden my-3 border shadow-sm" style="border-radius:10px" id="feed-<?= $feed['id'] ?>">
    <div class="card-header border-0 d-flex px-3 py-3 align-items-center bg-white">
      <img src="<?= !empty($feed['profile']) ? $feed['profile'] : '/img/default-user.png' ?>" 
           style="width:45px; height: 45px; object-fit:cover;" class="rounded-circle me-2">
      <div>
        <h6 class="mb-0 fw-bold"><?= htmlspecialchars(json_decode($feed['profile'],true)['firstname'].' '.json_decode($feed['profile'],true)['lastname'] ) ?></h6>
        <small class="text-muted"><?= timeAgo($feed['created_at']) ?> · <i class="ri-earth-fill"></i></small>
      </div>
    </div>

    <div class="card-body pt-0">
      <p class="px-1"><?= nl2br(htmlspecialchars($feed['content'])) ?></p>
      
      <?php if(!empty($media_items)): ?>
     <div class="horizontal-masonry px-1">
        <?php foreach ($media_items as $item): ?>
            <div class="masonry-brick cursor-pointer" onclick="popMedia('<?= $item['path'] ?>', '<?= $item['type'] ?>')">
                <?php if($item['type'] === 'image'): ?>
                    <img src="/uploads/feeds/<?= $item['path'] ?>" alt="Media">
                <?php elseif($item['type'] === 'video'): ?>
                    <div class="position-relative h-100">
                        <video class="h-100"><source src="/<?= $item['path'] ?>" type="video/mp4"></video>
                        <div class="vid-overlay"><i class="ri-play-fill"></i></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
      <?php endif; ?>
    </div>

    <div class="card-body py-2 px-3">
      <div class="d-flex justify-content-between align-items-center text-muted small">
        <div>
          <i class="ri-thumb-up-fill text-primary"></i>
          <span class="ms-1" id="like-count-<?= $feed['id'] ?>"><?= number_format($feed['total_likes']) ?></span>
        </div>
        <div>
          <span class="cursor-pointer" onclick="openComments(<?= $feed['id'] ?>)"><?= number_format($feed['total_comments']) ?> comments</span> • 
          <span id="share-count-<?= $feed['id'] ?>"><?= number_format($feed['shares']) ?> shares</span>
        </div>
      </div>
    </div>

    <hr class="mx-3 my-0">

    <div class="card-body py-1">
      <div class="d-flex justify-content-around">
          <button class="btn btn-link text-decoration-none <?= $isLiked ? 'text-primary' : 'text-muted' ?> fw-semibold d-flex align-items-center gap-2" 
                  onclick="feedAction(<?= $feed['id'] ?>, 'like', this)">
            <i class="<?= $isLiked ? 'ri-thumb-up-fill' : 'ri-thumb-up-line' ?> fs-5"></i> Like
          </button>
          
          <button class="btn btn-link text-decoration-none text-muted fw-semibold d-flex align-items-center gap-2" 
                    onclick="openComments(<?= $feed['id'] ?>, <?= $feed['total_comments'] ?>)">
                <i class="ri-chat-3-line fs-5"></i> Comment
          </button>
          
          <button class="btn btn-link text-decoration-none text-muted fw-semibold d-flex align-items-center gap-2"
                  onclick="feedAction(<?= $feed['id'] ?>, 'share', this)">
            <i class="ri-share-forward-line fs-5"></i> Share
          </button>
      </div>
    </div>
</div>
<?php endforeach; ?>