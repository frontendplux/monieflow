<header class="sticky-top bg-white py-3 shadow-sm d-flex align-items-center justify-content-between px-3 px-md-4">
    <!-- Left section: Logo + Search -->
    <div class="d-flex align-items-center flex-shrink-0">
        <div class="brand-logo fw-bold me-3 me-md-4" style="font-size: 1.65rem; letter-spacing: -1px;">
            monieFlow
        </div>
        <div class="fb-nav-icon d-none d-md-flex">
            <i class="ri-search-line"></i>
        </div>
    </div>

    <!-- Center navigation (hidden on mobile) -->
    <nav class="d-none d-md-flex align-items-center justify-content-center flex-grow-1 h-100">
        <a href="/feeds/" class="nav-icon-link active">
            <i class="ri-home-fill"></i>
        </a>
        <a href="/group" class="nav-icon-link">
            <i class="ri-group-line"></i>
        </a>
        <a href="/reels/" class="nav-icon-link">
            <i class="ri-tv-2-line"></i>
        </a>
        <a href="/shop/" class="nav-icon-link">
            <i class="ri-store-2-line"></i>
        </a>
    </nav>

    <!-- Right section: Icons + Profile -->
    <div class="d-flex align-items-center gap-2 gap-md-3 flex-shrink-0">
        <div class="fb-nav-icon d-md-none">
            <i class="ri-search-line"></i>
        </div>
        <div class="fb-nav-icon">
            <i class="ri-menu-line"></i>
        </div>
        <div class="fb-nav-icon position-relative">
            <i class="ri-messenger-fill"></i>
            <!-- Optional: notification dot -->
            <!-- <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger p-1"> </span> -->
        </div>
        <div class="fb-nav-icon position-relative">
            <i class="ri-notification-3-fill"></i>
            <!-- Optional: notification dot -->
            <!-- <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span> -->
        </div>
        <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?? 'default' ?>" 
             class="rounded-circle border border-light shadow-sm" 
             width="38" height="38" 
             alt="Profile" 
             style="object-fit: cover; cursor: pointer;">
    </div>
</header>

<style>
    :root {
        --fb-bg: #f0f2f5;
        --fb-white: #ffffff;
        --fb-blue: #1877f2;
        --fb-hover: #e4e6eb;
    }

    header {
        height: 76px;
        background: var(--fb-white);
        z-index: 1030;
        border-bottom: 1px solid #ddd;
    }

    .brand-logo {
        background: linear-gradient(45deg, #1877f2, #6366f1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .fb-nav-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--fb-hover);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #050505;
        cursor: pointer;
        transition: background 0.2s;
    }

    .fb-nav-icon:hover {
        background: #d8dade;
    }

    .nav-icon-link {
        width: 150px;
        height: 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #65676b;
        font-size: 1.75rem;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
    }

    .nav-icon-link:hover {
        color: #1877f2;
        background: var(--fb-hover);
    }

    .nav-icon-link.active {
        color: var(--fb-blue);
        border-bottom: 3px solid var(--fb-blue);
    }

    .nav-icon-link.active:hover {
        background: transparent;
    }

    @media (max-width: 767.98px) {
        header {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
        .fb-nav-icon {
            width: 36px;
            height: 36px;
            font-size: 1.15rem;
        }
    }
</style>