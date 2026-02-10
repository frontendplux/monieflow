<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__."/../main-function.php"; ?>
    <?php 
        $main = new main($conn);
        if($main->isLoggedIn() === false) header('location:/');
        $userData = $main->getUserData()['data'];
        $profile = json_decode($userData['profile'], true);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Discussion | monieFlow</title>

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

        body { background-color: var(--fb-bg); font-family: 'Segoe UI', sans-serif; }

        /* Main Discussion Container */
        .discussion-container {
            max-width: 750px;
            margin: 20px auto;
        }

        .post-card, .comment-section {
            background: var(--fb-white);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Comment Styling */
        .comment-bubble {
            background-color: #f0f2f5;
            border-radius: 18px;
            padding: 8px 12px;
            display: inline-block;
            max-width: 100%;
        }

        .comment-item {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }

        .reply-thread {
            margin-left: 50px;
            border-left: 2px solid var(--fb-hover);
            padding-left: 15px;
        }

        .action-link {
            font-size: 12px;
            font-weight: bold;
            color: var(--fb-gray-text);
            text-decoration: none;
            margin-right: 12px;
        }
        .action-link:hover { text-decoration: underline; }

        /* Sticky Bottom Comment Input */
        .comment-input-area {
            position: sticky;
            bottom: 0;
            background: white;
            border-top: 1px solid var(--fb-hover);
            padding: 15px;
            z-index: 100;
        }

        .input-wrapper {
            background: #f0f2f5;
            border-radius: 20px;
            padding: 5px 15px;
            display: flex;
            align-items: center;
        }

        .input-wrapper input {
            border: none;
            background: transparent;
            flex-grow: 1;
            padding: 8px;
            outline: none;
        }
    </style>
</head>
<body>

<header class="sticky-top bg-white border-bottom d-flex align-items-center px-3" style="height: 56px;">
    <a href="/home" class="text-dark me-3"><i class="ri-arrow-left-line fs-4"></i></a>
    <h6 class="mb-0 fw-bold">Post Discussion</h6>
</header>

<div class="discussion-container">
    
    <div class="post-card p-3 mb-3">
        <div class="d-flex align-items-center mb-3">
            <img src="https://i.pravatar.cc/150?u=author" class="rounded-circle me-2" width="40" height="40">
            <div>
                <div class="fw-bold">Mark Johnson</div>
                <div class="text-muted small">2 hours ago • <i class="ri-global-line"></i></div>
            </div>
        </div>
        <p>Just finished the new UI update for monieFlow! What do you guys think about the new Swiper integration? 🚀</p>
        <img src="https://picsum.photos/800/400" class="img-fluid rounded border mb-3">
        
        <div class="d-flex justify-content-between text-muted small pb-2 border-bottom">
            <span><i class="ri-thumb-up-fill text-primary"></i> 124 Likes</span>
            <span>48 Comments</span>
        </div>
        
        <div class="d-flex justify-content-around pt-2">
            <button class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1"><i class="ri-thumb-up-line"></i> Like</button>
            <button class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1"><i class="ri-share-forward-line"></i> Share</button>
        </div>
    </div>

    <div class="comment-section p-3 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-bold mb-0">All Comments</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">Most Relevant</button>
            </div>
        </div>

        <div class="comment-item">
            <img src="https://i.pravatar.cc/150?u=user1" class="rounded-circle" width="36" height="36">
            <div>
                <div class="comment-bubble">
                    <div class="fw-bold small">Sarah Stone</div>
                    <div class="small">The Swiper JS makes it feel so much more like a native app! Great job on the marketplace layout.</div>
                </div>
                <div class="mt-1 ms-2">
                    <a href="#" class="action-link">Like</a>
                    <a href="#" class="action-link">Reply</a>
                    <span class="text-muted small">12m</span>
                </div>

                <div class="reply-thread mt-3">
                    <div class="comment-item">
                        <img src="https://i.pravatar.cc/150?u=author" class="rounded-circle" width="28" height="28">
                        <div>
                            <div class="comment-bubble">
                                <span class="fw-bold small">Mark Johnson</span>
                                <span class="small">Thanks Sarah! Glad you liked the smooth transitions.</span>
                            </div>
                            <div class="mt-1 ms-2">
                                <a href="#" class="action-link">Like</a>
                                <a href="#" class="action-link">Reply</a>
                                <span class="text-muted small">5m</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="comment-item">
            <img src="https://i.pravatar.cc/150?u=user3" class="rounded-circle" width="36" height="36">
            <div>
                <div class="comment-bubble">
                    <div class="fw-bold small">Alex Rivera</div>
                    <div class="small">Can we add dark mode support next? 😎</div>
                </div>
                <div class="mt-1 ms-2">
                    <a href="#" class="action-link">Like</a>
                    <a href="#" class="action-link">Reply</a>
                    <span class="text-muted small">1h</span>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="comment-input-area">
    <div class="discussion-container m-0 mx-auto">
        <div class="d-flex align-items-center gap-2">
            <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle" width="36" height="36">
            <div class="input-wrapper flex-grow-1">
                <input type="text" placeholder="Write a comment...">
                <div class="d-flex gap-2 text-secondary px-2">
                    <i class="ri-emotion-line cursor-pointer"></i>
                    <i class="ri-camera-line cursor-pointer"></i>
                    <i class="ri-gift-line cursor-pointer"></i>
                </div>
            </div>
            <button class="btn btn-primary rounded-circle" style="width: 40px; height: 40px; padding:0">
                <i class="ri-send-plane-2-fill"></i>
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>