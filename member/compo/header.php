<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Remix Icon -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="/style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1/index.js"></script>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" /> -->

  <script src="/asset/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>
  <script src="/libery.js"></script>
  <script>
    const dataSetMenu = [
  {
    heading: "Social",
    more: [
      {
        icon: "/img/event-list.png",
        header: "Events",
        desc: "Organise or find events and other things to do online and nearby.",
        link: "/member/event/"
      },
      {
        icon: "/img/friends.png",
        header: "Friends",
        desc: "Search for friends or people you may know.",
        link: "/member/friends/"
      },
      {
        icon: "/img/partners.png",
        header: "Groups",
        desc: "Connect with people who share your interests.",
        link: "/member/groups/"
      },
      {
        icon: "/img/hot-news.png",
        header: "News Feed",
        desc: "See relevant posts from people and Pages you follow.",
        link: "/member/news/"
      },
      {
        icon: "/img/feed.png",
        header: "Feeds",
        desc: "See the most recent posts from your friends, groups, Pages and more.",
        link: "/member/feeds/"
      },
      {
        icon: "/img/website-content.png",
        header: "Pages",
        desc: "Discover and connect with business on facebook",
        link: "/member/news/"
      }
    ]
  },
  {
    heading:"Entertainment", more:[
      {
        icon: "/img/game-console.png",
        header: "Gaming videos",
        desc: "watch and connect with your favorite games and streamers.",
        link: "/member/feeds/"
      },
      {
        icon: "/img/gamepad.png",
        header: "Play games",
        desc: "Play your favourite games",
        link: "/member/feeds/"
      },
      {
        icon: "/img/film-reel.png",
        header: "Reels",
        desc: "A Reels destination personalised to your intrests and connections.",
        link: "/member/feeds/"
      },
    ]
  },
  {
    heading:"Shopping", more:[
      {
        icon: "/img/manifest.png",
        header: "Orders and payments",
        desc: "A seamless, secure way to pay in the app you already use.",
        link: "/member/feeds/"
      },
      {
        icon: "/img/retailer.png",
        header: "Marketplace",
        desc: "Buy and sell in your community",
        link: "/member/feeds/"
      },
    ]
  },
  {
    heading:"Personal", more:[
      {
        icon: "/img/physical.png",
        header: "Recent ads activity",
        desc: "See all of the ads you've interacted with on monieflow",
        link: "/member/feeds/"
      },
      {
        icon: "/img/server.png",
        header: "Memories",
        desc: "Buy and sell in your community",
        link: "/member/feeds/"
      },
      {
        icon: "/img/data-storage.png",
        header: "Saved",
        desc: "Find posts, photos and videos that you've saved for later.",
        link: "/member/feeds/"
      },
    ]
  },
  {
    heading:"Professional", more:[
      {
        icon: "/img/social-media-marketing.png",
        header: "Ads manager",
        desc: "Create, manage and track the performance of your ads.",
        link: "/member/feeds/"
      }
    ]
  },
];


  const dataSetMenu2= [
    {
        more:[
            {icon:"ri-quill-pen-ai-fill", label: "Post", link: ""},
            {icon:"ri-book-open-fill", label: "Story", link: ""},
            {icon:"ri-movie-2-fill", label: "Reel", link: ""},
            {icon:"ri-bard-fill", label: "Life update", link: ""}
        ]
    },
    {
        more:[
            {icon:"ri-flag-2-fill", label: "Page", link: ""},
            {icon:"ri-megaphone-fill", label: "Ads", link: ""},
            {icon:"ri-team-fill", label: "Group", link: ""},
            {icon:"ri-calendar-event-fill", label: "Event", link: ""},
            {icon:"ri-store-3-fill", label: "Marketplace Listing", link: ""}
        ]
    }
  ];
  </script>
  <title>monieflow - </title>
</head>
<body>
    <header class="bg-white px-3 position-fixed top-0 start-0 end-0 py-3 py-sm-0 d- d-flex sticky-top justify-content-between align-items-center">
        <div class="d-flex gap-3 align-items-center">
            <h1 class="text-uppercase fs-5 m-0"><img src="/MONIEFLOW.png" style="width: 60px;" alt="monieflow"></h1>
            <div class="position-relative w-100 d-none d-sm-block" style="max-width: 300px;">
                <input type="search" placeholder="search monieflow" class="p-2 bg-light rounded-pill ps-5 form-control">
                <i class="ri-search-line position-absolute start-0 text-secondary  top-0 pt-1 ms-3" style="margin-top: 6px;"></i>
            </div>
            <a href="javascript:;" onclick="document.getElementById('header-sm-search-nav').classList.toggle('d-none')" class="ri-search-2-line p-2 rounded-pill d-sm-none d-block text-decoration-none btn btn-light fs-3 py-1"></a>
            <div id="header-sm-search-nav" class="position-fixed start-0 d-none d-flex gap-2 end-0 px-2 bg-white" style="z-index: 1000;transition: 0.5s;">
                <input type="search" placeholder="search monieflow" class="p-2 bg-light rounded-pill ps-5 form-control">
                <i class="ri-search-line position-absolute start-0 text-secondary  top-0 pt-1 ms-4" style="margin-top: 6px;"></i>
                <a href="javascript:;" onclick="document.getElementById('header-sm-search-nav').classList.toggle('d-none')" class="ri-close-line p-2 rounded-pill d-sm-none d-block text-decoration-none text-black fs-2 py-1"></a>
            </div>
        </div>
        <div class="d-none d-sm-flex">
            <a href="" class="active  px-5 py-3 fs-4 text-decoration-none ri-home-5-line"></a>
            <a href="" class="px-5 py-3 fs-4 text-decoration-none ri-group-line"></a>
            <a href="" class="px-5 py-3 fs-4 text-decoration-none ri-group-3-line"></a>
        </div>
        <div class=" d-flex align-items-center gap-2">
            <a href="" class="text-capitalize px-3 d-none d-sm-block p-2 rounded-pill btn btn-light">find&nbsp;friends</a>
            <a href="javascript:;" onclick="document.getElementById('dataSetMenu').classList.toggle('d-none')" class="px-2 py-0 rounded-circle btn btn-light border-0 fs-2"><span class="ri-grid-fill"></span></a>
            <a href="" class="px-2 py-0 rounded-circle btn btn-light border-0 fs-2"><span class="ri-messenger-fill"></span></a>
            <a href="javascript:;" onclick="document.getElementById('dataSetMenuNotification').classList.toggle('d-none')" class="px-2 py-0 rounded-circle btn btn-light border-0 fs-2"><span class="ri-notification-2-fill"></span></a>
            <a href="javascript:;" onclick="document.getElementById('dataSetMenuUser').classList.toggle('d-none')" class="px-3 py-0 rounded-circle position-relative border-0 fs-4">
                <img src="/img/post-img3.jpg"  class="rounded-circle object-fit-cover" style="width:40px; height:40px; object-position: top center;" alt="">
                <i class="position-absolute end-0  p-0 py-0" style="margin-top: 15px;"><span class="ri-arrow-down-s-fill p-0 rounded bg-light"></span></i>
            </a>
        </div>
    </header>
    <div id="dataSetMenu" class="col-11 h-75 d-none bg-light shadow p-2 col-sm-5 position-fixed end-0" style="z-index: 1000000000;margin:80px 30px">
            <div class="h3 m-0 p-2">menu</div>
            <div style="height: 90%;" class="overflow-auto d-sm-flex p-2 gap-3">
                <div class="col-sm-7 h-100 overflow-auto col-12  p-2 px-sm-3 bg-white ">
                    <div class="position-relative w-100 my-2">
                        <input type="search" placeholder="search monieflow" class="p-2 bg-light rounded-pill ps-5 form-control">
                        <i class="ri-search-line position-absolute start-0 text-secondary  top-0 pt-1 ms-3" style="margin-top: 6px;"></i>
                    </div>
                </div>
                <div class="w-100 h-100 overflow-auto  p-2 px-sm-3 bg-white ">
                    <h4>Create</h4>
                </div>
            </div>
    </div>




 <div id="dataSetMenuUser" class="dropdown-menu d-none show p-3 shadow rounded-4 position-fixed end-0" style="width: 300px; margin: 80px 30px;">

  <!-- Profile -->
  <div class="d-flex align-items-center mb-3">
    <img src="/img/post-img3.jpg"
         class="rounded-circle me-2"
         style="width:45px;height:45px;object-fit:cover;">
    <div>
      <div class="fw-semibold">Samuel Sunday</div>
      <a href="#" class="text-decoration-none text-primary small">
        See all profiles
      </a>
    </div>
  </div>

  <hr>

  <!-- Menu Items -->
  <a href="#" class="dropdown-item d-flex align-items-center gap-3 py-2">
    <span class="icon-box">
      <i class="ri-settings-3-line"></i>
    </span>
    <span>Settings & privacy</span>
    <i class="ri-arrow-right-s-line ms-auto"></i>
  </a>

  <a href="#" class="dropdown-item d-flex align-items-center gap-3 py-2">
    <span class="icon-box">
      <i class="ri-question-line"></i>
    </span>
    <span>Help & support</span>
    <i class="ri-arrow-right-s-line ms-auto"></i>
  </a>

  <a href="#" class="dropdown-item d-flex align-items-center gap-3 py-2">
    <span class="icon-box">
      <i class="ri-moon-line"></i>
    </span>
    <span>Display & accessibility</span>
    <i class="ri-arrow-right-s-line ms-auto"></i>
  </a>

  <a href="#" class="dropdown-item d-flex align-items-center gap-3 py-2">
    <span class="icon-box">
      <i class="ri-feedback-line"></i>
    </span>
    <span>Give feedback</span>
    <small class="text-muted ms-1">CTRL B</small>
  </a>

  <a href="#" class="dropdown-item d-flex align-items-center gap-3 py-2">
    <span class="icon-box">
      <i class="ri-logout-box-r-line"></i>
    </span>
    <span>Log out</span>
  </a>

  <hr>

  <!-- Footer -->
  <div class="small text-muted text-center">
    Privacy · Terms · Advertising · Ad choices · Cookies · More
  </div>
</div>



<div id="dataSetMenuNotification" class="card d-none shadow-sm border-0 position-fixed end-0" style="z-index: 100000000; width: 320px; border-radius: 12px;margin:80px 50px">
  <div class="card-body p-0">
    <div class="d-flex justify-content-between align-items-center px-3 py-2">
      <h5 class="fw-bold mb-0">Notifications</h5>
      <i class="ri-more-fill fs-4 text-muted cursor-pointer"></i>
    </div>

    <div class="px-3 mb-2">
      <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 me-2">All</span>
      <span class="text-muted fw-semibold small">Unread</span>
    </div>

    <div class="d-flex justify-content-between px-3 py-2">
      <span class="fw-bold small">Earlier</span>
      <a href="#" class="text-decoration-none small">See all</a>
    </div>

    <div class="list-group list-group-flush">
      
      <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-start py-2">
        <div class="position-relative me-3">
          <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px;">
            <i class="ri-error-warning-fill fs-4"></i>
          </div>
          <span class="position-absolute bottom-0 end-0 badge rounded-circle bg-dark p-1 border border-2 border-white">
            <i class="ri-notification-3-fill text-white" style="font-size: 10px;"></i>
          </span>
        </div>
        <div class="flex-grow-1">
          <p class="mb-0 small text-dark">We did not restore your comment. See why.</p>
          <small class="text-muted">1w</small>
        </div>
      </a>

      <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-start py-2">
        <div class="position-relative me-3">
          <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px;">
            <i class="ri-facebook-fill fs-3"></i>
          </div>
          <span class="position-absolute bottom-0 end-0 badge rounded-circle bg-primary p-1 border border-2 border-white">
            <i class="ri-user-fill text-white" style="font-size: 10px;"></i>
          </span>
        </div>
        <div class="flex-grow-1">
          <p class="mb-0 small text-dark">Your temporary profile picture has expired.</p>
          <small class="text-primary fw-bold">1w</small>
        </div>
        <div class="ms-2 align-self-center">
          <div class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>
        </div>
      </a>

      <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-start py-2">
        <div class="position-relative me-3">
          <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px;">
            <i class="ri-error-warning-fill fs-4"></i>
          </div>
          <span class="position-absolute bottom-0 end-0 badge rounded-circle bg-dark p-1 border border-2 border-white">
            <i class="ri-notification-3-fill text-white" style="font-size: 10px;"></i>
          </span>
        </div>
        <div class="flex-grow-1">
          <p class="mb-0 small text-dark">We added restrictions to your account. See why.</p>
          <small class="text-muted">1w</small>
        </div>
      </a>

    </div>
  </div>
</div>






<?php include __DIR__."/../feeds/create-post-component.html"; ?>

</body>
</html>

<script>
    loadMenu();
function loadMenu(){
     const container = document.querySelector(
  '#dataSetMenu > div:nth-child(2) > div:nth-child(1)'
);

     const container2 = document.querySelector(
  '#dataSetMenu > div:nth-child(2) > div:nth-child(2)'
);
dataSetMenu.forEach(section => {
  container.innerHTML += `<hr><h5 class="my-3">${section.heading}</h5>`
  section.more.forEach(item => {
    container.innerHTML += `
      <a href="${item.link}" class="d-flex gap-3 btn text-start">
        <div>
          <img style="width:30px" src="${item.icon}" alt="${item.header}">
        </div>
        <div>
          <b>${item.header}</b><br>
          <small class="text-muted">${item.desc}</small>
        </div>
      </a>
    `;
  });
});

dataSetMenu2.forEach(section => {
  container2.innerHTML += `<hr>`
  section.more.forEach(item => {
    container2.innerHTML += `
      <a href="${item.link}" class="d-flex align-items-center gap-2 btn text-start">
        <div>
          <i class="${item.icon} p-2 btn bg-light rounded-circle py-1 fs-5"></i>
        </div>
        <div class="fw-medium">
          ${item.label}
        </div>
      </a>
    `;
  });
});
}
</script>


<script>
    async function feedAction(feedId, actionType, btn) {
    try {
        const response = await fetch('/member/feeds/post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: actionType, feed_id: feedId })
        });
        const res = await response.json();

        if (res.status === 'success') {
            if (actionType === 'like') {
                document.getElementById(`like-count-${feedId}`).innerText = res.count;
                const icon = btn.querySelector('i');
                if (res.type === 'liked') {
                    btn.classList.replace('text-muted', 'text-primary');
                    icon.classList.replace('ri-thumb-up-line', 'ri-thumb-up-fill');
                } else {
                    btn.classList.replace('text-primary', 'text-muted');
                    icon.classList.replace('ri-thumb-up-fill', 'ri-thumb-up-line');
                }
            } 
            if (actionType === 'share') {
                alert("Shared successfully!");
                location.reload(); // Or update share count UI
            }
        }
    } catch (e) { console.error(e); }
}
</script>