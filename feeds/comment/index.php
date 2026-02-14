<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__."/req.php"; ?>
    <?php 
        $main = new comments($conn);
        if($main->isLoggedIn() === false) header('location:/');
        $userData = $main->getUserData()['data'];
        $profile = json_decode($userData['profile'], true);
        $feeds=$_GET['feed'] ?? header('location:/feeds/'); 
        $comments=$main->feedWithComments($feeds);
       
        $comment_data=json_decode($comments['data'], true);
        $comment_profile=json_decode($comments['profile'],true);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=0.9, user-scalable=0">
    <title>Discussion | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --fb-bg: #f0f2f5;
            --fb-white: #ffffff;
            --fb-blue: #1877f2;
            --fb-gray-text: #65676b;
            --fb-hover: #e4e6eb;
        }

        body { 
            background-color: var(--fb-white); /* White background for split view */
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden; /* Prevent double scrollbars */
        }

        /* Split Screen Layout */
        .split-wrapper {
            display: flex;
            height: calc(100vh - 56px);
        }

        /* Left Side: The Post Content */
        .post-viewer {
            flex: 1.2;
            background-color: #000; /* Cinematic dark background for media */
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .post-viewer img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Right Side: Information and Comments */
        .info-sidebar {
            flex: 0.8;
            background: var(--fb-white);
            border-left: 1px solid var(--fb-hover);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .scrollable-content {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
        }

        /* Comment Bubbles */
        .comment-bubble {
            background-color: #f0f2f5;
            border-radius: 18px;
            padding: 8px 15px;
            font-size: 0.95rem;
        }

        .reply-thread {
            margin-left: 2px;
            border-left: 2px solid var(--fb-hover);
            padding-left: 10px;
        }

        /* Sticky Footer Input */
        .comment-footer {
            padding: 15px;
            border-top: 1px solid var(--fb-hover);
            background: white;
        }

        .input-group-custom {
            background: #f0f2f5;
            border-radius: 25px;
            padding: 5px 15px;
            display: flex;
            align-items: center;
        }

        .input-group-custom input {
            border: none;
            background: transparent;
            flex-grow: 1;
            padding: 8px;
            outline: none;
        }

        /* Mobile Adjustments */
        @media (max-width: 992px) {
            body { overflow: auto; }
            .split-wrapper { flex-direction: column; height: auto; }
            .post-viewer { height: 300px; flex: none; }
            .info-sidebar { flex: none; height: auto; }
        }
    </style>
</head>
<body>
<pre>
    <?=  print_r(json_encode($comments,JSON_PRETTY_PRINT)); ?>
</pre>
<header class="sticky-top bg-white border-bottom d-flex align-items-center justify-content-between px-3" style="height: 56px; z-index: 1000;">
    <div class="d-flex align-items-center">
        <a href="javascript:;" onclick="history.back()" class="fb-nav-icon me-3 text-dark text-decoration-none"><i class="ri-arrow-left-s-line fs-4"></i></a>
        <span class="fw-bold">comment</span>
    </div>
</header>

<div class="split-wrapper container px-0">
    <div class="post-viewer">
        <img src="https://picsum.photos/1200/800" alt="Post content">
        <div class="position-absolute bottom-0   start-0 p-3 text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.8)); width: 100%;">
        <div class="avatar-group d-flex me-2">
            <img src="https://i.pravatar.cc/100?u=6" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -15px;">
            <img src="https://i.pravatar.cc/100?u=7" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -15px;">
            <img src="https://i.pravatar.cc/100?u=8" class="rounded-circle border border-2 border-white" width="28"  style="margin-right: -15px;">
            <b class="mx-4 ri-eye-fill"> 67k</b>
        </div>
        </div>
    </div>

    <div class="info-sidebar">
        <div class="scrollable-content">
            <div class="d-flex align-items-center mb-3">
               <a href="/profile/?u=<?= $comments['user_id']  ?>" class="text-decoration-none text-secondary"><img src="/uploads/<?= $comment_profile['profile_pic']; ?>" class="rounded-circle me-3" width="45" height="45"></a>
                <div>
                    <a href="/profile/?u=<?= $comments['user_id']  ?>" class="text-decoration-none text-secondary"><h6 class="mb-0 fw-bold"><?= $comment_profile['first_name']. " ". $comment_profile['first_name']   ?></h6></a>
                    <small class="text-muted"><?= $comments['feed_created']  ?> · <i class="ri-global-line"></i></small>
                </div>
            </div>

            <div class="post-description mb-4">
                <p><?= $comments['txt'] ?? $comment_data['content']  ?></p>
                <div class="d-flex gap-2 mb-2">
                    <?php

$parts = array_filter(explode('#', $comments['keywords']));
// rebuild each part into a badge span
$badges = array_map(function($tag) {
    $tag = trim($tag);
    return $tag !== '' 
        ? '<span class="badge bg-light text-primary border">#' . htmlspecialchars($tag) . '</span>' 
        : '';
}, $parts);

// output inline
echo implode(' ', $badges);
?>
                </div>
            </div>

            <div class="d-flex justify-content-between py-2 border-top border-bottom mb-3">
                <div class="d-flex align-items-center gap-1 text-primary" style="cursor:pointer" onclick="like_post(<?= $comments['feed_id']  ?>,'<?= $comments['status']  ?>')">
                    <i class="ri-thumb-up-fill"></i> <small id="like-<?= $comments['feed_id']  ?>"><?= $comments['like_count']  ?></small>
                </div>
                <div class="text-muted small">
                    <span class="me-2"><span id="comment-<?= $comments['feed_id']  ?>"><?= $comments['comment_count']  ?></span> Comments</span>
                    <span> Shares</span>
                </div>
            </div>

            <div class="comments-list" id="commentList">
                <?php foreach ($comments['comments'] as $value):
                   $comment_data_comms= json_decode($value['profile'], true) ?>
                <div class="d-flex gap-2 mb-3">
                    <img src="/uploads/<?= $comment_data_comms['profile_pic']; ?>" class="rounded-circle" width="32" height="32">
                    <div class="flex-grow-1">
                        <div class="comment-bubble">
                            <div class="fw-bold small"><?= $comment_data_comms['first_name']. " " . $comment_data_comms['last_name'] ; ?></div>
                            <div><?= $value['data']['text'] ?></div>
                        </div>
                        <div class="small ms-2 mt-1">
                            <a href="javascript:;"
                             onclick="like_comment(<?= $value['feed_id'] ?>, '<?= $comments['status'] ?>','<?= $value['comment_id'] ?>','<?= $value['type'] ?>')"
                             class="text-secondary fw-bold text-decoration-none me-3"><span id="like-comes-<?= $value['comment_id'] ?>"><?= $value['like_count'] ?></span> .Like</a>
                            <a href="javascript:;" onclick="replyTo(<?= $value['feed_id'] ?>, '<?= $comments['status'] ?>','<?= $value['comment_id'] ?>','<?= $value['comment_id'] ?>','<?= $comment_data_comms['first_name']. '-' . $comment_data_comms['last_name'] ; ?>','<?= $comment_data_comms['username'] ?>')" class="text-secondary fw-bold text-decoration-none me-3"><span><?= $value['reply_count'] ?></span> .Reply</a>
                            <span class="text-muted"><?= $new->timeAgoSimple($value['created_at']) ?></span>
                        </div>
                        <?php foreach ($value['replies'] as $replies):
                            $comment_data_replies = json_decode($replies['profile'], true); ?>
                            <div class="reply-thread mt-2">
                                <div class="d-flex gap-2">
                                    <img src="/uploads/<?= $comment_data_replies['profile_pic']; ?>" class="rounded-circle" width="24" height="24">
                                    <div class="flex-grow-1">
                                        <div class="comment-bubble py-1 px-3">
                                            <div class="fw-bold small"><a href=''><?= $comment_data_replies['username']."</a> <i class='ri-play-fill'></i><a href=''>" . $comment_data_replies['last_name'] ?> </a></div>
                                            <div class="small"><?= $replies['data']['text'] ?></div>
                                        </div>   
                                        <div class="small ms-2 mt-1">
                                            <a href="javascript:;"
                                            onclick="like_comment(<?= $replies['feed_id'] ?>, '<?= $comments['status'] ?>','<?= $replies['reply_id'] ?>','<?= $replies['type'] ?>')"
                                            class="text-secondary fw-bold text-decoration-none me-3">
                                            <span id="like-comes-<?= $replies['reply_id'] ?>"><?= $replies['like_count'] ?></span> .Like
                                            </a>
                                            <a href="javascript:;"
                                            onclick="replyTo(<?= $replies['feed_id'] ?>, '<?= $comments['status'] ?>','<?= $replies['reply_id'] ?>','<?= $value['comment_id'] ?>','<?= $comment_data_replies['first_name']. '-' . $comment_data_replies['last_name'] ; ?>','<?= $comment_data_replies['username'] ?>')"
                                            class="text-secondary fw-bold text-decoration-none me-3">
                                            <span><?= $replies['reply_count'] ?></span> .Reply
                                            </a>
                                            <span class="text-muted"><?= $new->timeAgoSimple($replies['created_at']) ?></span>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                           <?= $value['reply_count'] > 3  ? "<div class='reply-thread'><a href='' class='ms-5 text-decoration-none'> --------- see more (".$value['reply_count'].") -----------</a></div>" : '' ?>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </div>

<!-- your existing footer markup -->
<div class="sticky-bottom bottom-0 start-0 end-0">
  <div class="comment-footer position-relative"> 
    <div class="d-flex align-items-center gap-2">
      <!-- avatar -->
      <a href="/profile/?u=<?= $new->getUserData()['data']['id']  ?>" class="text-decoration-none text-secondary">
        <img src="/uploads/<?= json_decode($new->getUserData()['data']['profile'],true)['profile_pic']  ?>" class="rounded-circle" width="35" height="35">
      </a>
      <!-- input -->
      <div class="input-group-custom custom-interaction-group flex-grow-1 d-flex align-items-center bg-light rounded-pill px-3 py-1">
        <textarea id="commentInput" style="height:25px" class="form-control main-input border-0 bg-transparent shadow-none" placeholder="Comment.., <?= $comment_profile['username'] ?>"></textarea>
        <div class="d-flex gap-1 text-secondary">
          <i class="ri-emotion-line pop-trigger px-2 fs-3" data-type="emoji"></i>
          <i class="ri-gift-line pop-trigger px-0 fs-3" data-type="gift"></i>
        </div>
      </div>
      <!-- send button -->
      <button id="sendCommentBtn" 
              data-feed="<?= $comments['feed_id'] ?>"
              data-status="<?= $comments['status'] ?>"
              data-username="<?= $comment_profile['username'] ?>"
              data-avatar="/uploads/<?= json_decode($new->getUserData()['data']['profile'],true)['profile_pic']  ?>"
              data-parent_id="0" 
              class="btn btn-primary rounded-circle p-0 d-flex align-items-center justify-content-center" 
              style="width:38px; height:38px;">
        <i class="ri-send-plane-2-fill text-white"></i>
      </button>
    </div>
  </div>
</div>




    </div>
</div>
<script src="/api.js"></script>
<script src="/feeds/comment/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('commentInput');
  const sendBtn = document.getElementById('sendCommentBtn');

  sendBtn.addEventListener('click', () => {
    const text = input.value.trim();
    if (!text) {
      droppySammy('info', 'Sending Error', "there are no comment updated");
      return;
    }

    var feedId = sendBtn.dataset.feed;
    var status = sendBtn.dataset.status || 'feeds';
    var parent_id = sendBtn.dataset.parent_id || 0;
    var avatar = sendBtn.dataset.avatar || 0;
    var username = sendBtn.dataset.username || 0;

    fetch('/feeds/comment/req.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'sendComment', feed_id: feedId, text, status, parent_id })
    })
    .then(res => res.text())
    .then(data => {
      console.log(data);
      if (data) {
             sendBtn.dataset.feed=<?= $comments['feed_id'] ?>;
             sendBtn.dataset.status ="<?= $comments['status'] ?>";
             parent_id = sendBtn.dataset.parent_id =0;
             sendBtn.dataset.avatar = "/uploads/<?= json_decode($new->getUserData()['data']['profile'],true)['profile_pic']  ?>";
             sendBtn.dataset.username = <?= $comment_profile['username'] ?>;
             input.Placeholder="Comment.., <?= $comment_profile['username'] ?>";
             input.value="";
             droppySammy('danger', 'Successfull', "comment was sent successfully!");
      }
      else{
        droppySammy('danger', 'Network Error', "Unable to post comment.");
      }
    })
    .catch(err => {
      console.error('Error posting comment:', err);
      droppySammy('danger', 'Network Error', "Unable to post comment.");
    });
  });
});
</script>

</body>
</html>


