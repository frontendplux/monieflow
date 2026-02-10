<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Creative Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body { background-color: #ffffff; font-family: 'Inter', sans-serif; }

        /* Gallery Filter Buttons */
        .filter-btn {
            border: none;
            background: none;
            font-weight: 600;
            color: #6c757d;
            padding: 8px 20px;
            transition: 0.3s;
            border-bottom: 2px solid transparent;
        }
        .filter-btn.active, .filter-btn:hover {
            color: #000;
            border-bottom: 2px solid #000;
        }

        /* Gallery Item */
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            cursor: pointer;
            margin-bottom: 1.5rem;
        }

        .gallery-item img {
            width: 100%;
            transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
            display: block;
        }

        /* Overlay Effect */
        .gallery-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .overlay-content h5 { color: #fff; margin: 0; transform: translateY(10px); transition: 0.4s; }
        .overlay-content p { color: rgba(255,255,255,0.7); font-size: 0.85rem; transform: translateY(10px); transition: 0.5s; }

        .gallery-item:hover .overlay-content h5,
        .gallery-item:hover .overlay-content p {
            transform: translateY(0);
        }

        /* Masonry-like Column Layout */
        .gallery-columns {
            column-count: 3;
            column-gap: 1.5rem;
        }

        @media (max-width: 992px) { .gallery-columns { column-count: 2; } }
        @media (max-width: 576px) { .gallery-columns { column-count: 1; } }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Visual Portfolio</h1>
        <p class="text-muted">Capturing moments across the monieFlow ecosystem.</p>
        
        <div class="d-flex justify-content-center gap-2 mt-4 flex-wrap">
            <button class="filter-btn active">All Work</button>
            <button class="filter-btn">Architecture</button>
            <button class="filter-btn">Lifestyle</button>
            <button class="filter-btn">Abstract</button>
            <button class="filter-btn">Events</button>
        </div>
    </div>

    <div class="gallery-columns">
        
        <div class="gallery-item">
            <img src="https://picsum.photos/600/800?random=1" alt="Gallery Image">
            <div class="gallery-overlay">
                <div class="overlay-content">
                    <h5>Modern Structure</h5>
                    <p>Architecture • 2026</p>
                </div>
            </div>
        </div>

        <div class="gallery-item">
            <img src="https://picsum.photos/600/400?random=2" alt="Gallery Image">
            <div class="gallery-overlay">
                <div class="overlay-content">
                    <h5>Urban Life</h5>
                    <p>Lifestyle • Lagos</p>
                </div>
            </div>
        </div>

        <div class="gallery-item">
            <img src="https://picsum.photos/600/900?random=3" alt="Gallery Image">
            <div class="gallery-overlay">
                <div class="overlay-content">
                    <h5>Golden Hour</h5>
                    <p>Nature • Landscape</p>
                </div>
            </div>
        </div>

        <div class="gallery-item">
            <img src="https://picsum.photos/600/600?random=4" alt="Gallery Image">
            <div class="gallery-overlay">
                <div class="overlay-content">
                    <h5>Creative Flow</h5>
                    <p>Abstract • Digital</p>
                </div>
            </div>
        </div>

        <div class="gallery-item">
            <img src="https://picsum.photos/600/750?random=5" alt="Gallery Image">
            <div class="gallery-overlay">
                <div class="overlay-content">
                    <h5>Night Market</h5>
                    <p>Events • Culture</p>
                </div>
            </div>
        </div>

        <div class="gallery-item">
            <img src="https://picsum.photos/600/500?random=6" alt="Gallery Image">
            <div class="gallery-overlay">
                <div class="overlay-content">
                    <h5>Minimalist Interior</h5>
                    <p>Architecture • Design</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>