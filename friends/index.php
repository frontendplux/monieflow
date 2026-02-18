<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__."/../main-function.php"; ?>
    <?php 
        $main = new main($conn);
        if($main->isLoggedIn() === false) header('location:/');
        $userData = $main->getUserData()['data'];
        $profile = json_decode($userData['profile'], true);
        $page=['friends', 1]
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

<div class="position-fixed w-100 top-0 p-2" style="z-index: 23390; background: #f0f8ffab;">
        <div class="container px-0 d-flex justify-content-between " >
            <a href="javascript:;" onclick="history.back()" class="ri-arrow-left-s-line text-decoration-none fs-3 text-dark d-flex align-items-center"> 
                <span class="fs-3 fw-medium"><?=  $page[0] ?> </span>
            </a>
        </div>
</div>
<div class="container" style="margin-top:75px;">
    <div class="row">
        <div class="col-md-3 d-none d-md-block friends-sidebar">
            <h4 class="fw-bold mb-4">Friends</h4>
            <nav>
                <?php foreach(
                    [
                        ['ri-user-follow-fill','Home', 'friends/'],
                        ['ri-user-received-fill','Friend Requests', 'friends/friend-request.php'],
                        ['ri-user-add-fill','Suggestions','friends/Suggestions.php'],
                        ['ri-group-fill','All Friends', 'friends/all-friends.php'],
                        ['ri-cake-2-fill','Birthdays', 'friends/birthday.php']
                    ]
                        as $key => $menu
                ): $key += 1 ?>
                <a href="/<?= $menu[2]; ?>" class="nav-link-custom <?= $page[1] == $key ? 'active' : '' ?>  my-2">
                    <i class="<?= $menu[0]; ?>"></i> <?= $menu[1]; ?>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="col-12 col-md-9 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold">People You May Know</h5>
                <!-- <a href="#" class="text-decoration-none">See All</a> -->
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
function preloader() {
  document.getElementById('roots').innerHTML = `
    ${Array(12).fill(null).map(() => {
      return `
        <div class="col-6 col-sm-4 col-lg-4 col-xl-3 placeholder-wave">
          <div class="friend-card">
            <!-- Image placeholder -->
            <div class="friend-img col-12 placeholder" style="height:150px;"></div>
            
            <div class="friend-info">
              <!-- Name placeholder -->
              <div class="fw-bold text-truncate mb-1 placeholder col-8"></div>
              
              <!-- Mutual friends placeholder -->
              <div class="text-muted w-100 small mb-2 placeholder col-6"></div>
              
              <!-- Buttons placeholders -->
              <button class="btn btn-secondary col-12 my-2 disabled placeholder col-6"></button>
              <button class="btn btn-secondary col-12 border disabled placeholder col-6"></button>
            </div>
          </div>
        </div>
      `;
    }).join('')}
  `;
}
function fetchfriends() {
  preloader();
  const formData = new FormData();
  formData.append('action', 'get_friend_list_to_follow');
  formData.append('limit', limit);

  fetch('/friends/req.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(datas => {
    console.log(datas);

    const data = datas.map(e => {
      const profile = JSON.parse(e.profile);
      return ` 
        <div class="col-6 col-sm-4 col-lg-3 col-xl-3" id="friend-${e.id}">
          <div class="friend-card">
            <img src="/uploads/${profile.profile_pic}" class="friend-img">
            <div class="friend-info">
              <div class="fw-bold text-truncate mb-1">${profile.first_name} ${profile.last_name}</div>
              <div class="text-muted small mb-2">5 mutual friends</div>
              <button class="btn btn-primary btn-action" onclick="sendReq(${e.id})">Add Friend</button>
              <button class="btn btn-light btn-action border" onclick="removeSug(${e.id})">Remove</button>
            </div>
          </div>
        </div>
      `;
    }).join('') + `
    <style>
      .btn-gradient {
        background: linear-gradient(45deg, #0d6efd, #0dcaf0);
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
        color: white;
      }
    </style>

    <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-3">
      <button class="btn btn-light border text-muted px-4" onclick="previewFriends()">
        Preview
      </button>

      <button class="btn btn-gradient text-white fw-bold px-5 py-2 rounded-3" onclick="nextfriendpage()">
        Next Step
      </button>
    </div>`;

    document.getElementById('roots').innerHTML = data;
  })
  .catch(error => {
    console.error('Error fetching friends:', error);
    document.getElementById('roots').innerHTML = '<p>Failed to load friends.</p>';
  });
}

window.nextfriendpage = () => {
  limit += 12;
  fetchfriends();
};

window.previewFriends = () => {
  limit = Math.max(0, limit - 12);
  fetchfriends();
};

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