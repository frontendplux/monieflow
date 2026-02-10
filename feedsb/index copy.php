<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__."/../main-function.php"; ?>
    <?php 
        $main=new main($conn);
        if($main->isLoggedIn() === false) header('location:/');
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Social Feed</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --bg-color: #f8f9fa;
            --accent-color: #6366f1; /* Modern Indigo */
            --glass-bg: rgba(255, 255, 255, 0.8);
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1f2937;
        }

        /* Glassmorphism Header */
        header {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            z-index: 1000;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -1px;
            background: linear-gradient(45deg, #6366f1, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Modern Card Styling */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 2rem;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: transparent;
            padding: 1.25rem;
            border: none;
        }

        /* Image Grid Enhancement */
        .post-images {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            padding: 0 1.25rem;
        }

        .post-images img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            transition: opacity 0.2s;
        }

        .post-images img:first-child:nth-last-child(3),
        .post-images img:first-child:nth-last-child(3) ~ img {
            grid-column: span 1;
        }
        
        .post-images img:first-child:last-child {
            grid-column: span 2;
            height: 350px;
        }

        /* Action Buttons */
        .action-bar {
            border-top: 1px solid #f3f4f6;
            padding: 0.5rem 1.25rem;
        }

        .btn-action {
            color: #6b7280;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 10px;
            padding: 8px 12px;
            transition: all 0.2s;
        }

        .btn-action:hover {
            background: #f3f4f6;
            color: var(--accent-color);
        }

        /* Floating Action Button */
        .fab {
            height: 60px;
            width: 60px;
            background: linear-gradient(45deg, #6366f1, #a855f7);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
            transition: transform 0.2s;
        }

        .fab:hover {
            transform: scale(1.1) rotate(90deg);
            color: white;
        }

        /* Comment Style */
        .comment-bubble {
            background-color: #f3f4f6;
            border-radius: 15px;
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .comment-input input {
            border-radius: 12px;
            background: #f3f4f6;
            border: 1px solid transparent;
            padding: 10px 15px;
        }

        .comment-input input:focus {
            background: white;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
    </style>
</head>
<body>

<header class="sticky-top">
    <div class="container d-flex justify-content-between align-items-center py-3">
        <div class="brand-logo">monieFlow</div>
        <div class="d-flex align-items-center">
            <!-- <button class="btn btn-light border  rounded-circle me-3 position-relative" style="height:40px; width:40px"> <i class="ri-search-fill"></i></button>
            <button class="btn btn-light border  rounded-circle me-3 position-relative" style="height:40px; width:40px"><span style="margin:-10px 0 0 10px;width:15px; height: 15px; font-size: x-small;" class="position-absolute rounded-circle bg-danger text-white">2</span> <i class="ri-group-fill"></i></button>
            <button class="btn btn-light border  rounded-circle me-3 position-relative" style="height:40px; width:40px"><span style="margin:-10px 0 0 10px;width:15px; height: 15px; font-size: x-small;" class="position-absolute rounded-circle bg-danger text-white">2</span> <i class="ri-notification-3-fill"></i></button> -->
            <img src="https://i.pravatar.cc/150?u=me" class="rounded-circle border" width="40" height="40">
        </div>
    </div>
</header>

<a href="#" data-bs-toggle="modal" 
    data-bs-target="#createPostModal" style="z-index: 1;" class="fab rounded-circle ri-add-line fs-2 position-fixed d-flex justify-content-center align-items-center bottom-0 end-0 m-4 text-decoration-none"></a>

<div class="container my-2" id="roots" style="max-width:650px">
    

<div class="card" id="preloader-loding">
  <div class="card-header d-flex align-items-center">
    <span class="placeholder-wave">
      <span class="placeholder rounded-circle me-3" style="width:48px; height:48px;"></span>
    </span>
    <div class="flex-grow-1">
      <span class="placeholder-glow">
        <span class="placeholder col-6"></span>
      </span>
      <span class="placeholder-wave">
        <span class="placeholder col-4"></span>
        <div class="placeholder col-4"></div>
      </span>
    </div>

    <span class="placeholder-glow ms-auto">
      <span class="placeholder rounded" style="width:24px; height:24px;"></span>
    </span>
  </div>

  <div class="card-body pt-0">
    <span class="placeholder-glow">
      <span class="placeholder col-9"></span>
    </span>
    <span class="placeholder-glow">
      <span class="placeholder col-7"></span>
    </span>
  </div>

  <div class="post-images mb-3">
    <span class="placeholder-glow">
      <span class="placeholder col-12" style="height:200px;"></span>
    </span>
  </div>

  <div class="p-2 px-4 d-flex gap-3 mb-3">
    <span class="placeholder-glow col-3 d-flex" ><span class="placeholder w-100"></span></span>
    <span class="placeholder-glow col-3 d-flex"><span class="placeholder w-100"></span></span>
    <span class="placeholder-glow col-3 d-flex"><span class="placeholder w-100"></span></span>
  </div>
</div>

</div>

<?php include  __DIR__."/component.php" ?>
<script src="/api.js"></script>
<script src="/feeds/script.js"></script>

</body>
</html>