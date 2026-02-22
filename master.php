<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
       include __DIR__."/main-function.php";
       $new=new Main($conn); 
       if(!$new->isLoggedIn()) header('location:/');
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/bootstrap-5.3.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/RemixIcon_Fonts_v4.1.0/rimix-icon/remixicon.css">
    <script src="/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="/pages.js"></script>
    <script src="/api.js"></script>
    <title></title>
    <style>
        html,body,#roots{
            width: 100%;height: 100%;
            position: fixed;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        #roots{
            display: flex; overflow: hidden; scroll-behavior: hidden;
            scroll-snap-type: x mandatory;
            scroll-snap-align: center;
        }
        .gallery-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px; /* Professional tight gap */
  padding: 8px;
  background-color: #fff;
}

.gallery-item {
  position: relative;
  overflow: hidden;
  border-radius: 4px; /* Slight roundness for a modern feel */
  background-color: #f0f0f0;
}

/* Image styling */
.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s ease;
}

/* Hover Effect */
.gallery-item:hover img {
  transform: scale(1.05);
}

/* --- THE LOGIC --- */

/* Full Width (Col-1) */
.gallery-item.full-width {
  flex: 0 0 100%;
  aspect-ratio: 16 / 9; /* Cinema feel */
}

/* Half Width (Col-2) */
.gallery-item.col-2 {
  flex: 0 0 calc(50% - 4px); /* Subtracted half the gap */
  aspect-ratio: 1 / 1.2; /* Taller portrait feel */
}

/* Responsive: Everything stacks on very small screens if needed */
@media (max-width: 480px) {
  .gallery-item.col-2 {
    aspect-ratio: 1 / 1; /* Squares look better on small phones */
  }
}




/* --- MOBILE & GLOBAL BASE --- */
.modal-bottom {
    display: flex;
    align-items: flex-end; /* Anchors to floor */
    margin: 0 !important;
    max-width: 100% !important;
    height: 100%;
}

.modal-bottom .modal-content {
    height: 50vh; /* 50% screen height */
    border-radius: 20px 20px 0 0;
    border: none;
}

/* Slide up from bottom animation */
.modal.fade .modal-dialog.modal-bottom {
    transform: translateY(100%);
    transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}
.modal.show .modal-dialog.modal-bottom {
    transform: translateY(0);
}

/* Scroll area sizing */
.modal-bottom .modal-body {
    flex: 1;
    overflow-y: auto;
    padding-bottom: 80px; /* Room for fixed input */
}

/* --- BIG SCREENS (Desktop) --- */
@media (min-width: 992px) {
    .modal-bottom {
        align-items: stretch; /* Full vertical */
        justify-content: flex-start; /* Aligns to left */
    }

    .modal-bottom .modal-content {
        width: 400px; /* Side drawer width */
        height: 100vh;
        border-radius: 0;
    }

    /* Slide from left animation */
    .modal.fade .modal-dialog.modal-bottom {
        transform: translateX(-100%);
    }
    .modal.show .modal-dialog.modal-bottom {
        transform: translateX(0);
    }
}

/* Styling the "Reply can reply" line */
.border-start {
    border-left: 2px solid #dee2e6 !important;
}


.cursor-pointer{cursor: pointer;}
    </style>
</head>
<body>
    <div id="preloader-loading" style="z-index: 20000;" class="position-fixed bg-white top-0 bottom-0 w-100 justify-content-center d-flex align-items-center placeholder-glow">
        <img src="/logo.png" alt="" style="width: 80px;" class="placeholder bg-white">
    </div>
    <div id="roots"></div>
</body>
</html>
