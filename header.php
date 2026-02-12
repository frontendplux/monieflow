<header class="sticky-top bg-white py-3 shadow-sm d-flex align-items-center justify-content-between px-3 px-md-4">
    <!-- Left section: Logo + Search -->
    <div class="d-flex align-items-center flex-shrink-0">
        <div class="brand-logo fw-bold me-3 me-md-4" style="font-size: 1.65rem; letter-spacing: -1px;">
            monieFlow
        </div>
        <div class="fb-nav-icon d-md-flex dropdown d-none d-md-block">
            <!-- Trigger -->
            <a href="#" role="button" data-bs-toggle="dropdown" class="text-decoration-none text-dark" data-bs-auto-close="outside" aria-expanded="false">
                <i class="ri-search-line"></i>
            </a>

            <!-- Dropdown menu -->
            <ul class="dropdown-menu  mt-4 py-3 shadow border-0 rounded-2 me-2 w-100" style="min-width:340px">
                <div class="px-2 position-relative">
                <span class="ri-search-fill position-absolute m-3 mt-2"></span>
                <input type="search" class="form-control px-2 ps-5" placeholder="search feeds, products reels and more...">
                </div>

                <a href="/profile/" class="justify-content-between my-2 text-decoration-none d-flex px-2 border-bottom py-2 align-items-center" onclick="toggleMobileChat()">
                <img src="https://i.pravatar.cc/150?u=a2" class="rounded-circle" width="40px" height="40px">
                <div class="ms-3 overflow-hidden w-100">
                    <div class="d-flex justify-content-between">
                    <h6 class="mb-0 fw-bold small">
                        <?= $profile['first_name']. " " .$profile['last_name'] ?> 
                        (<span class="text-muted small text-uppercase">
                        <?= htmlspecialchars($userProfile['level'] ?? 'newbie') ?>
                        </span>)
                    </h6>
                    </div>
                    <p class="text-dark fw-light small mb-0 text-muted">
                    <?= htmlspecialchars($userProfile['bio'] ?? 'Hi there, am on flow') ?>
                    </p>
                </div>
                </a>

                <div class="d-flex gap-3 px-2 text-capitalize">
                <a href="">feeds</a>
                <a href="">pages</a>
                <a href="">groups</a>
                </div>
            </ul>
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
        
        <div class="fb-nav-icon d-md-flex dropdown d-block d-flex justify-content-center align-items-center  d-md-none">
            <!-- Trigger -->
            <a href="#" role="button" data-bs-toggle="dropdown" class="text-decoration-none text-dark" data-bs-auto-close="outside" aria-expanded="false">
                <i class="ri-search-line"></i>
            </a>

            <!-- Dropdown menu -->
            <ul class="dropdown-menu  mt-4 py-3 shadow border-0 rounded-2 me-2 w-100" style="min-width:340px">
                <div class="px-2 position-relative">
                <span class="ri-search-fill position-absolute m-3 mt-2"></span>
                <input type="search" class="form-control px-2 ps-5" placeholder="search feeds, products reels and more...">
                </div>

                <a href="/profile/" class="justify-content-between my-2 text-decoration-none d-flex px-2 border-bottom py-2 align-items-center" onclick="toggleMobileChat()">
                <img src="https://i.pravatar.cc/150?u=a2" class="rounded-circle" width="40px" height="40px">
                <div class="ms-3 overflow-hidden w-100">
                    <div class="d-flex justify-content-between">
                    <h6 class="mb-0 fw-bold small">
                        <?= $profile['first_name']. " " .$profile['last_name'] ?> 
                        (<span class="text-muted small text-uppercase">
                        <?= htmlspecialchars($userProfile['level'] ?? 'newbie') ?>
                        </span>)
                    </h6>
                    </div>
                    <p class="text-dark fw-light small mb-0 text-muted">
                    <?= htmlspecialchars($userProfile['bio'] ?? 'Hi there, am on flow') ?>
                    </p>
                </div>
                </a>

                <div class="d-flex gap-3 px-2 text-capitalize">
                <a href="">feeds</a>
                <a href="">pages</a>
                <a href="">groups</a>
                </div>
            </ul>
        </div>
        <div class="fb-nav-icon" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
            <i class="ri-menu-line"></i>
        </div>
        <div class="fb-nav-icon position-relative" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ri-messenger-fill"></i>
             <ul class="dropdown-menu mt-4 p-2 shadow border-0 rounded-2 me-2 " style="min-width:340px">
                <div class="fw-medium fs-5 d-flex justify-content-between align-items-center">
                    <span>chats (0)</span>
                    <a href="" class="text-uppercase text-decoration-none fs-6">see all</a>
                </div>
                <hr>
                <div class="justify-content-between d-flex align-items-center" onclick="toggleMobileChat()">
                    <img src="https://i.pravatar.cc/150?u=a2" class="rounded-circle" width="40px" height="40px">
                    <div class="ms-3 overflow-hidden w-100">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 fw-bold small">Compliance Desk</h6>
                            <span class="text-muted small">2m</span>
                        </div>
                        <p class="text-dark fw-light small mb-0 text-muted">Your agent status is active!</p>
                    </div>
                </div>
            </ul>
        </div>
        <div class="fb-nav-icon position-relative dropdown">
            <!-- Trigger -->
            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                <i class="ri-notification-3-fill"></i>
            </a>

            <!-- Dropdown menu -->
            <ul class="dropdown-menu mt-4 p-2 shadow border-0 rounded-2 me-2 w-100" style="min-width:340px">
                <div class="fw-medium fs-5 d-flex justify-content-between align-items-center">
                <span>notifications (0)</span>
                <a href="#" class="text-uppercase text-decoration-none fs-6">see all</a>
                </div>
                <hr>
                <div class="justify-content-between d-flex align-items-center" onclick="toggleMobileChat()">
                <img src="https://i.pravatar.cc/150?u=a2" class="rounded-circle" width="40px" height="40px">
                <div class="ms-3 overflow-hidden w-100">
                    <div class="d-flex justify-content-between">
                    <h6 class="mb-0 fw-bold small">Compliance Desk</h6>
                    <span class="text-muted small">2m</span>
                    </div>
                    <p class="text-dark fw-light small mb-0 text-muted">Your agent status is active!</p>
                </div>
                </div>
            </ul>
        </div>

        
            <div class="dropdown">
                <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?? 'default' ?>" 
             class="rounded-circle border border-light shadow-sm" 
             width="38" height="38" 
             alt="Profile" 
             style="object-fit: cover; cursor: pointer;" role="button" data-bs-toggle="dropdown" aria-expanded="true">

             <!-- Dropdown menu -->
            <ul class="dropdown-menu mt-4 py-3 shadow border-0 rounded-2 me-2 w-100" style="min-width:340px">
                <a href="/profile/" class="justify-content-between text-decoration-none  d-flex px-2 border-bottom py-2 align-items-center" onclick="toggleMobileChat()">
                    <img src="https://i.pravatar.cc/150?u=a2" class="rounded-circle" width="40px" height="40px">
                    <div class="ms-3 overflow-hidden w-100">
                        <div class="d-flex justify-content-between">
                        <h6 class="mb-0 fw-bold small"><?= $profile['first_name']. " " .$profile['last_name'] ?> (<span class="text-muted small text-uppercase"> <?= htmlspecialchars($userProfile['level'] ?? 'newbie') ?></span>)</h6>
                        </div>
                        <p class="text-dark fw-light small mb-0 text-muted"> <?= htmlspecialchars($userProfile['bio'] ?? 'Hi there, am on flow') ?></p>
                    </div>
                </a>
                <a class="justify-content-between btn rounded-0 btn-light py-2 d-flex align-items-center" onclick="toggleMobileChat()">
                    <i class="ri-user-fill"></i>
                    <div class="ms-3 overflow-hidden w-100">
                        <div class="d-flex justify-content-between">
                        <h6 class="mb-0 fw-bold small">Log Out </h6>
                        </div>
                    </div>
                </a>
            </ul>
            </div>
    </div>
</header>

<div class="offcanvas offcanvas-top" style="background:#f0f8ffde;" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">monieFlow</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-flex flex-wrap gap-3 text-capitalize">
      <a href="">home</a>
      <a href="">games</a>
      <a href="">hotel management</a>
      <a href="">gaming</a>
      <a href="">gift card</a>
    </div>
  </div>
</div>
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

     /* Chat Components */
        .contact-item {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: 0.2s;
        }
        .contact-item:hover { background: #f0f7ff; }
        .contact-item.active { background: #e7f3ff; border-left: 4px solid var(--mflow-blue); }

        .conv-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: #f9fbff;
        }

        .msg-bubble {
            max-width: 75%;
            padding: 12px 16px;
            border-radius: 20px;
            margin-bottom: 10px;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .received { background: #fff; border: 1px solid #eee; align-self: flex-start; border-bottom-left-radius: 4px; }
        .sent { background: var(--mflow-blue); color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }

        .back-btn { display: none; cursor: pointer; margin-right: 15px; }
        @media (max-width: 768px) { .back-btn { display: block; } }

        .avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
         .chat-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Nav */
        .chat-nav {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            padding: 0 15px;
            flex-shrink: 0;
        }
</style>