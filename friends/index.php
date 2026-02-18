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
    <title>Friends | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --fb-bg: #f0f2f5;
            --fb-white: #ffffff;
            --fb-blue: #1877f2;
            --fb-hover: #e4e6eb;
        }

        body { background-color: var(--fb-bg); font-family: 'Segoe UI', sans-serif; }

        /* Sidebar Styling */
        .friends-sidebar {
            background: var(--fb-white);
            height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
            border-right: 1px solid var(--fb-hover);
            padding: 15px;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            color: #050505;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            background: var(--fb-hover);
        }

        .nav-link-custom i {
            width: 36px;
            height: 36px;
            background: #e4e6eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 20px;
        }

        .nav-link-custom.active i {
            background: var(--fb-blue);
            color: white;
        }

        /* Friend Card Styling */
        .friend-card {
            background: var(--fb-white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            border: 1px solid #ddd;
        }

        .friend-card:hover { transform: translateY(-3px); }

        .friend-img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
        }

        .friend-info { padding: 12px; }

        .btn-action {
            width: 100%;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<header class="sticky-top bg-white border-bottom d-flex align-items-center justify-content-between px-3" style="height: 56px; z-index: 1050;">
    <div class="d-flex align-items-center">
        <div class="brand-logo me-3" style="font-weight: 800; font-size: 1.5rem; color: var(--fb-blue);">monieFlow</div>
    </div>
    <div class="d-flex gap-2">
        <div class="fb-nav-icon bg-light rounded-circle p-2"><i class="ri-settings-4-fill"></i></div>
        <img src="https://i.pravatar.cc/150?u=<?= $userData['id'] ?>" class="rounded-circle" width="40">
    </div>
</header>

<div class="container my-3">
    <div class="row">
        <div class="col-md-3 d-none d-md-block friends-sidebar">
            <h4 class="fw-bold mb-4">Friends</h4>
            <nav>
                <a href="#" class="nav-link-custom active">
                    <i class="ri-user-follow-fill"></i> Home
                </a>
                <a href="#" class="nav-link-custom">
                    <i class="ri-user-received-fill"></i> Friend Requests
                </a>
                <a href="#" class="nav-link-custom">
                    <i class="ri-user-add-fill"></i> Suggestions
                </a>
                <a href="#" class="nav-link-custom">
                    <i class="ri-group-fill"></i> All Friends
                </a>
                <a href="#" class="nav-link-custom">
                    <i class="ri-cake-2-fill"></i> Birthdays
                </a>
            </nav>
        </div>

        <div class="col-12 col-md-9 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold">People You May Know</h5>
                <a href="#" class="text-decoration-none">See All</a>
            </div>

            <div class="row g-3" id="roots">
                <?php for($i=1; $i<=8; $i++): ?>
                <div class="col-6 placeholder-glow col-sm-4 col-lg-4 col-xl-3" id="friend-102">
                    <div class="friend-card">
                        <!-- Image placeholder -->
                        <div class="friend-img col-12 placeholder" style="height:150px;"></div>
                        <div class="friend-info">
                        <div class="fw-bold text-truncate mb-1 placeholder col-8"></div>
                        
                        <!-- Mutual friends placeholder -->
                        <div class="text-muted w-100 small mb-2 placeholder col-6"></div>
                        
                        <!-- Buttons placeholders -->
                        <button class="btn btn-secondary col-12 my-2  disabled placeholder col-6"></button>
                        <button class="btn btn-secondary col-12  border disabled placeholder col-6"></button>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<script>
var limit=0
window.addEventListener('DOMContentLoaded',e=>{
    fetchfriends();
})
function fetchfriends() {
  const formData = new FormData();
   formData.append('action','get_friend_list_to_follow')
   formData.append('limit',limit )
   limit +=12
     fetch('/friends/req.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json()) // or response.json()
  .then(datas => {
    console.log(datas);
    
    const data=datas.map(e=>{
       const profile=JSON.parse(e.profile);
      return  ` 
                <div class="col-6 col-sm-4 col-lg-3 col-xl-3" id="friend-${e.id}">
                    <div class="friend-card">
                        <img src="/uploads/${profile.profile_pic}" class="friend-img">
                        <div class="friend-info">
                            <div class="fw-bold text-truncate mb-1">${profile.first_name}  ${profile.last_name}</div>
                            <div class="text-muted small mb-2">5 mutual friends</div>
                            <button class="btn btn-primary btn-action" onclick="sendReq(${e.id})">Add Friend</button>
                            <button class="btn btn-light btn-action border" onclick="removeSug(${e.id})">Remove</button>
                        </div>
                    </div>
                </div>
        `
    }).join('');
    document.getElementById('roots').innerHTML=data;
    console.log('Success:', data);
  })
}


    function sendReq(id) {
        const card = document.querySelector(`#friend-${id} .btn-primary`);
        card.innerHTML = '<i class="ri-check-line"></i> Requested';
        card.classList.replace('btn-primary', 'btn-secondary');
        card.disabled = true;
    }

    function removeSug(id) {
        const el = document.getElementById('friend-' + id);
        el.style.opacity = '0';
        el.style.transform = 'scale(0.8)';
        setTimeout(() => el.remove(), 300);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>