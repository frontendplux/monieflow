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
        print_r($comments);
        $comment_data=json_decode($comments['data'], true);
        $comment_profile=json_decode($comments['profile'],true);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
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
            margin-left: 45px;
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

<header class="sticky-top bg-white border-bottom d-flex align-items-center justify-content-between px-3" style="height: 56px; z-index: 1000;">
    <div class="d-flex align-items-center">
        <a href="javascript:;" onclick="history.back()" class="fb-nav-icon me-3 text-dark text-decoration-none"><i class="ri-arrow-left-s-line fs-4"></i></a>
    </div>
</header>

<div class="split-wrapper container">
    <div class="post-viewer">
        <img src="https://picsum.photos/1200/800" alt="Post content">
        <div class="position-absolute bottom-0 start-0 p-3 text-white d-md-none" style="background: linear-gradient(transparent, rgba(0,0,0,0.8)); width: 100%;">
            <p class="mb-0">New UI Launch! 🚀</p>
        </div>
    </div>

    <div class="info-sidebar">
        <div class="scrollable-content">
            <div class="d-flex align-items-center mb-3">
                <img src="https://i.pravatar.cc/150?u=author" class="rounded-circle me-3" width="45" height="45">
                <div>
                    <h6 class="mb-0 fw-bold"><?= $comment_profile['first_name']. " ". $comment_profile['first_name']   ?></h6>
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
                    <span class="me-2"><small id="comment-<?= $comments['feed_id']  ?>"><?= $comments['like_count']  ?></small> Comments</span>
                    <span> Shares</span>
                </div>
            </div>

            <div class="comments-list">
                <div class="d-flex gap-2 mb-3">
                    <img src="https://i.pravatar.cc/150?u=u1" class="rounded-circle" width="32" height="32">
                    <div class="flex-grow-1">
                        <div class="comment-bubble">
                            <div class="fw-bold small">Sarah Stone</div>
                            <div>This layout is incredibly smooth. The 2-column view on desktop is exactly what we needed.</div>
                        </div>
                        <div class="small ms-2 mt-1">
                            <a href="#" class="text-secondary fw-bold text-decoration-none me-3">Like</a>
                            <a href="#" class="text-secondary fw-bold text-decoration-none me-3">Reply</a>
                            <span class="text-muted">2h</span>
                        </div>

                        <div class="reply-thread mt-2">
                            <div class="d-flex gap-2">
                                <img src="https://i.pravatar.cc/150?u=author" class="rounded-circle" width="24" height="24">
                                <div class="flex-grow-1">
                                    <div class="comment-bubble py-1 px-3">
                                        <div class="fw-bold small">Mark Johnson</div>
                                        <div class="small">Thanks Sarah! Performance is the priority here.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-3">
                    <img src="https://i.pravatar.cc/150?u=u5" class="rounded-circle" width="32" height="32">
                    <div class="flex-grow-1">
                        <div class="comment-bubble">
                            <div class="fw-bold small">Alex Rivera</div>
                            <div>The marketplace module looks fire 🔥</div>
                        </div>
                        <div class="small ms-2 mt-1">
                            <a href="#" class="text-secondary fw-bold text-decoration-none me-3">Like</a>
                            <a href="#" class="text-secondary fw-bold text-decoration-none me-3">Reply</a>
                            <span class="text-muted">1h</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="comment-footer">
            <div class="d-flex align-items-center gap-2">
                <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle" width="35" height="35">
                <div class="input-group-custom flex-grow-1">
                    <input type="text" placeholder="Write a comment...">
                    <div class="d-flex gap-2 text-secondary">
                        <i class="ri-emotion-line cursor-pointer"></i>
                        <i class="ri-camera-line cursor-pointer"></i>
                    </div>
                </div>
                <button class="btn btn-primary rounded-circle p-0 d-flex align-items-center justify-content-center" style="width:38px; height:38px;">
                    <i class="ri-send-plane-2-fill"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/feeds/comment/script.js"></script>
</body>
</html>