    <style>
         .mobile-bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #ffffff;
            border-top: 1px solid rgba(0, 168, 232, 0.15);
            z-index: 1030;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }

        .mobile-bottom-nav .nav-link {
            color: var(--text-muted);
            font-size: 0.72rem;
            padding: 8px 0;
            text-align: center;
        }

        .mobile-bottom-nav .nav-link.active,
        .mobile-bottom-nav .nav-link:hover {
            color: var(--brand-skyblue);
        }

        .mobile-bottom-nav i {
            font-size: 1.25rem;
            display: block;
            margin-bottom: 2px;
        }
    </style>
 <!-- Mobile Navigation Bar -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col">
                    <a href="/member/index.php" class="nav-link <?= $page== 'home'  ? 'active' : ''  ?>">
                        <i class="bi bi-house-door"></i>
                        <span>Home</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/chart.php" class="nav-link <?= $page== 'chart'  ? 'active' : ''  ?>">
                        <i class="bi bi-graph-up"></i>
                        <span>Chart</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/tasks.php" class="nav-link <?= $page== 'task'  ? 'active' : ''  ?>">
                        <i class="bi bi-check2-square"></i>
                        <span>Tasks</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/peer2peer.php" class="nav-link <?= $page== 'p2p'  ? 'active' : ''  ?>">
                        <i class="bi bi-people"></i>
                        <span>P2P</span>
                    </a>
                </div>
                <div class="col">
                    <a href="/member/settings.php" class="nav-link <?= $page== 'settings'  ? 'active' : ''  ?>">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>