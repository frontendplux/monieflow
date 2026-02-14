var feedup=0
function timeAgo(time) {
  const now = Date.now();
  const past = new Date(time).getTime();
  const diffInSeconds = Math.floor((now - past) / 1000);

  const units = [
    { label: "y", seconds: 31536000 }, // 365 days
    { label: "mo", seconds: 2592000 }, // 30 days
    { label: "d", seconds: 86400 },
    { label: "h", seconds: 3600 },
    { label: "m", seconds: 60 },
    { label: "s", seconds: 1 }
  ];

  for (const unit of units) {
    const count = Math.floor(diffInSeconds / unit.seconds);
    if (count >= 1) {
      return `${count}${unit.label}`;
    }
  }
  return "just now";
}


async function fetchfeeds() {
  const req = await fetch('/feeds/req.php', {
    method: "POST",
    body: JSON.stringify({ action: "feeds", limit: feedup })
  }).then(res => res.json());
  promo_feeds(req.feed, 18, 20);
  promo_reels(req.reels,0,15);
  promo_market(req.market, 10, 20);
  flowads_slide();
  promo_feeds(req.feed, 12, 18);
  show_Pages()
  promo_feeds(req.feed, 2, 12);
  promo_market_ads(req.market,0,10);
  promo_feeds(req.feed, 0, 2);
  promo_friends(req.friends,0,10)
  feedup += 20;
}

fetchfeeds();


window.addEventListener('scroll', () => {
  // current scroll position
  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  // total height of the page
  const scrollHeight = document.documentElement.scrollHeight;
  // visible height of the viewport
  const clientHeight = document.documentElement.clientHeight;

  // if user has scrolled to the bottom
  if (scrollTop + clientHeight >= scrollHeight - 5) {
    fetchfeeds();
  }
});



function promo_reels(reel,offset,limit){
  html=`
     <a href="/feeds/reel/create.php" class="card text-decoration-none border-0 shadow-sm flex-shrink-0" style="width: 130px; border-radius: 15px; overflow: hidden;">
                                    <div style="height: 150px; background: url('https://picsum.photos/200/300?random=1') center/cover;"></div>
                                    <div class="card-body p-0 position-relative text-center" style="height: 60px; background: #fff;">
                                        <div class="position-absolute translate-middle-x start-50" style="top: -18px;">
                                            <div class="bg-primary border border-3 border-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="ri-add-line text-white fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 small fw-bold text-dark">Create Reel</div>
                                    </div>
                                </a>


                                ${reel.slice(offset,limit).map(e=>{
                                    data=JSON.parse(e.data);
                                    profile=JSON.parse(e.profile)
                                    return`<a href="/feeds/reel/?r=${e.feed_id}" class="position-relative flex-shrink-0 shadow-sm user-reel" style="width: 130px; height: 210px; border-radius: 15px; overflow: hidden;">
                                    <div class="h-100 w-100" style="background: url('${data.poster}') center/cover;"></div>
                                    
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-2" 
                                        style="background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.8) 100%);">
                                        
                                        <div class="avatar-ring">
                                            <img src="/uploads/${profile.profile_pic}" class="rounded-circle border border-2 border-primary" width="35" height="35">
                                        </div>

                                        <div class="blur-footer rounded-2 py-1 px-2">
                                            <div class="text-white small fw-semibold text-truncate" style="font-size: 0.75rem;">${profile.username}</div>
                                        </div>
                                    </div>
                                </a>`}).join('')}
  `
  helper(html,true);
}

function promo_feeds(feeds, offset, limit) {
  const html = `
    <!-- Post List -->
    ${feeds.slice(offset, limit).map(e => {
      const data = JSON.parse(e.data);
      const profile = JSON.parse(e.profile);

      return `
        <div class="card py-3 mb-3 border-0 shadow-sm" style="border-radius:12px">
          <div class="d-flex px-2 justify-content-between align-items-start post-header mb-3">
            <div class="d-flex align-items-center">
              <img src="/uploads/${profile.profile_pic}" class="rounded-circle me-2" width="40" height="40">
              <div>
                <h6 class="mb-0">${profile.first_name} ${profile.last_name}</h6>
                <small class="text-muted">
                  ${profile.location ?? 'Hidden'} · ${timeAgo(e.updated_at)} · <i class="ri-earth-line"></i>
                </small>
              </div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-primary rounded-pill friend-btn">Add Friend</button>
              <button class="btn btn-sm btn-outline-secondary rounded-pill d-none d-sm-inline">Message</button>
              <button class="btn btn-sm"><i class="ri-more-line"></i></button>
            </div>
          </div>
          <p class="px-2 ps-3">${e.txt}</p>
          <div class="masonry">
            ${
              Array.isArray(data.images)
                ? data.images.map(url => `<img src="/uploads/${url}" class="masonry-item h-100" style="max-height:200px" />`).join('')
                : ''
            }
          </div>
          <div class="d-flex gap-3 border-top pt-1 mt-2 px-2 text-muted">
            <button onclick="like_post(${e.feed_id},'${e.status}')" class="btn btn-light text-muted">
              <i class="ri-thumb-up-line me-1"></i> Like . <small id="like-${e.feed_id}">${e.like_count}</small>
            </button>
            <a href='/feeds/comment/?feed=${e.feed_id}' class="btn btn-light text-decoration-none">
              <i class="ri-chat-3-line me-1"></i> Comment . <small id="comment-${e.feed_id}">${e.comment_count}</small>
            </a>
            <a href='/feeds/comment.php?p=${e.feed_id}' class="btn btn-light text-decoration-none">
              <i class="ri-share-line me-1"></i> Share
            </a>
          </div>
        </div>
      `;
    }).join('')}
  `;

  helper(html);
}


function promo_market(market,offset,limit){
      html =`
    <div class="marketplace-section">
        <div class="marketplace-header">
            <h5>Marketplace Picks for You</h5>
            <a href="#" class="text-primary fw-medium text-decoration-none">See all</a>
        </div>

        <div class="swiper marketplace-swiper">
            <div class="swiper-wrapper">
                ${market.slice(offset,limit).map(e=>{
                   var data=JSON.parse(e.data);
                //    console.log(e);
                   return `
                     <div class="swiper-slide">
                        <div class="product-card">
                            <div class="product-img">
                                <img src="${data.media_url}" alt="Shoes">
                            </div>
                            <div class="product-info">
                                <div class="product-title">${e.txt ?? data.item}</div>
                                <div class="product-price">${data.price}</div>
                                <div class="product-location">Abuja</div>
                                <a href="${e.feed_id}" class="btn btn-sm btn-outline-primary w-100 mt-3 rounded-pill">Message Seller</a>
                            </div>
                        </div>
                    </div>`}
                ).join('')}
                <!-- Add more slides dynamically if needed -->
            </div>
            <!-- Navigation & Pagination -->
            <div class="swiper-button-prev"><i class="ri-arrow-left-s-line"></i></div>
            <div class="swiper-button-next"><i class="ri-arrow-right-s-line"></i></div>
            <!--<div class="swiper-pagination mt-3"></div> -->
        </div>
    </div>`
  helper(html);
}

function promo_market_ads(market, offset, limit) {
  console.log(market);

  const html = `
    <div class="container px-0 mt-5">
      <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
          <span class="text-primary fw-bold text-uppercase small">Marketplace</span>
          <h3 class="fw-bold mb-0 fs-5">Trending Products</h3>
        </div>
        <a href="#" class="btn btn-outline-dark btn-sm rounded-pill px-3">View Store</a>
      </div>

      <div class="d-flex gap-2 overflow-auto py-3 g-3 g-md-4 custom-scrollbar" style="scrollbar-width: none;">
        ${market.slice(offset, limit).map(e => {
          const data = JSON.parse(e.data);
          return `
            <a href="/shop/products.php?p=${e.feed_id}" class="col-6 text-decoration-none col-md-4 col-lg-5">
              <div class="card h-100 border-0 shadow-sm product-card">
                <div class="position-relative overflow-hidden rounded-top">
                  <span class="badge bg-danger position-absolute top-0 start-0 m-2 z-index-2">-20%</span>
                  <button class="btn btn-white btn-wishlist position-absolute top-0 end-0 m-2 shadow-sm rounded-circle">
                    <i class="ri-heart-line"></i>
                  </button>
                  <div class="ratio ratio-1x1 bg-light">
                    <img src="${Array.isArray(data.media_url) ? data.media_url[0] : data.media_url}" 
                         class="object-fit-cover" alt="${e.txt ?? data.item}">
                  </div>
                </div>
                <div class="card-body px-2 pt-3">
                  <div class="text-muted small mb-1">${e.category}</div>
                  <h6 class="fw-bold text-dark text-truncate mb-2">${e.txt ?? data.item}</h6>
                  <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-primary">${data.price}</span>
                    ${data.old_price ? `<span class="text-muted text-decoration-line-through small">${data.old_price}</span>` : ''}
                  </div>
                </div>
              </div>
            </a>
          `;
        }).join('')}
      </div>
    </div>
  `;

  helper(html);
}



function promo_friends(friends, offset, limit) {
  console.log(friends);

  const html = `
    <div class="container mt-4 px-0">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">People You May Know</h6>
        <a href="/shop/" class="text-primary text-decoration-none small fw-bold">See All</a>
      </div>

      <div class="d-flex gap-3 pb-3 overflow-auto custom-scrollbar" style="scrollbar-width: none;">
        ${friends.slice(offset, limit).map(e => {
          const profile = JSON.parse(e.profile);
          return `
            <div class="card border-0 shadow-sm flex-shrink-0 friend-suggestion-card col-6 col-md-5" style=" border-radius: 18px;">
              <div class="position-relative">
                <div class="suggestion-cover" style="height: 60px; background: url('https://picsum.photos/200/100?random=${e.id}') center/cover; border-radius: 18px 18px 0 0;"></div>
                <div class="position-absolute start-50 translate-middle" style="top: 60px;">
                  <img src="/uploads/${profile.profile_pic}" class="rounded-circle border border-3 border-white shadow-sm" width="55" height="55">
                </div>
              </div>
              <div class="card-body text-center pt-5 pb-3">
                <div class="fw-bold text-dark text-truncate mb-0" style="font-size: 0.9rem;">${profile.first_name} ${profile.last_name}</div>
                <div class="text-muted mb-3" style="font-size: 0.75rem;">${e.mutual_count} Mutual Friends</div>
                <div class="d-grid gap-2 px-2">
                  <button class="btn btn-primary btn-sm rounded-pill fw-bold btn-add-friend">
                    <i class="ri-user-add-line me-1"></i> Add
                  </button>
                  <button class="btn btn-light btn-sm rounded-pill text-muted fw-bold">Remove</button>
                </div>
              </div>
            </div>
          `;
        }).join('')}
      </div>
    </div>
  `;

  helper(html);
}



function helper(html, reel = false){
    document.getElementById(!reel ? 'rooterfild' : 'reelifield').insertAdjacentHTML("afterbegin",html);
      sliderfunction();
      enginering();
}



function sliderfunction(){
     const marketplaceSwiper = new Swiper('.marketplace-swiper', {
            slidesPerView: 2.0,
            spaceBetween: 5,
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                480:  { slidesPerView: 2.5, spaceBetween: 16 },
                768:  { slidesPerView: 2.5, spaceBetween: 20 },
                992:  { slidesPerView: 3.2, spaceBetween: 20 },
                1200: { slidesPerView: 3, spaceBetween: 24 }
            }
        });
}


function flowads_slide(){
    var html=`
    <div id="sammyHeroSlider" class="carousel slide carousel-fade" data-bs-ride="carousel">
    
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#sammyHeroSlider" data-bs-slide-to="0" class="active rounded-circle" style="width:12px; height:12px;"></button>
        <button type="button" data-bs-target="#sammyHeroSlider" data-bs-slide-to="1" class="rounded-circle" style="width:12px; height:12px;"></button>
    </div>

    <div class="carousel-inner">
        
        <div class="carousel-item active" style="height: 85vh; background: #fdfcf0;">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-6 text-center text-lg-start">
                        <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2 rounded-pill fw-bold">v3.0 IS LIVE</span>
                        <h1 class="display-3 fw-black text-dark mb-4">Master Your <span class="text-primary">Flow</span></h1>
                        <p class="lead text-muted mb-5">Connect with friends, trade digital assets, and grow your monieCoins in the most advanced social ecosystem.</p>
                        <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                            <button class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg">Join Now</button>
                            <button class="btn btn-outline-dark btn-lg rounded-pill px-4"><i class="ri-play-fill"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="position-relative">
                            <img src="https://picsum.photos/600/600?random=1" class="img-fluid rounded-5 shadow-lg" alt="Slide 1">
                            <div class="position-absolute bottom-0 start-0 m-4 p-3 border border-white border-opacity-50 shadow-lg" 
                                 style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border-radius: 20px; width: 220px;">
                                <div class="small fw-bold text-dark">Recent Harvest</div>
                                <div class="h4 text-success mb-0">+1,240 MC</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="carousel-item" style="height: 85vh; background: #1d1d1d;">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-6 text-center text-lg-start text-white">
                        <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold">EARN PASSIVE INCOME</span>
                        <h1 class="display-3 fw-black mb-4 text-white">The <span class="text-warning">Barn</span> is Open</h1>
                        <p class="lead opacity-75 mb-5">Stake your coins in high-yield silos. High risk, high reward. Start growing your digital portfolio today.</p>
                        <button class="btn btn-warning btn-lg rounded-pill px-5 fw-bold text-dark">Start Staking</button>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="position-relative text-center">
                            <div class="bg-warning opacity-10 position-absolute start-50 top-50 translate-middle rounded-circle" style="width: 400px; height: 400px; filter: blur(80px);"></div>
                            <img src="https://picsum.photos/600/600?random=2" class="img-fluid rounded-5 border border-warning border-opacity-25" style="z-index: 2; position: relative;" alt="Slide 2">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#sammyHeroSlider" data-bs-slide="prev">
        <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#sammyHeroSlider" data-bs-slide="next">
        <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </span>
    </button>
</div>
`
helper(html);
}



function show_Pages(){
    html=`
    <div class="container px-0 mt-5">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h3 class="fw-bold mb-0">Explore Groups</h3>
                                    <p class="text-muted small">Find communities that match your interests.</p>
                                </div>
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3">View All Groups</button>
                            </div>

                            <div class="d-flex gap-2 overflow-auto py-3 g-4  custom-scrollbar" style="scrollbar-width: none;">
                                <div class="col-md-6 col-9 col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm hover-lift" style="border-radius: 20px;">
                                        <div class="position-relative">
                                            <div style="height: 100px; background: url('https://picsum.photos/400/200?random=50') center/cover; border-radius: 20px 20px 0 0;"></div>
                                            <span class="badge bg-white text-primary position-absolute top-0 end-0 m-3 shadow-sm rounded-pill">Trending</span>
                                        </div>
                                        
                                        <div class="card-body pt-0">
                                            <div class="bg-white p-1 rounded-3 shadow-sm d-inline-block position-relative" style="margin-top: -30px; margin-left: 15px;">
                                                <img src="https://picsum.photos/60/60?random=5" class="rounded-3" width="60" height="60">
                                            </div>
                                            
                                            <div class="mt-3">
                                                <h5 class="fw-bold mb-1">Crypto Whales 🐳</h5>
                                                <p class="text-muted small mb-3">Daily market analysis and monieCoin trading signals for the elite.</p>
                                                
                                                <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-group d-flex me-2">
                                                            <img src="https://i.pravatar.cc/100?u=1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                                            <img src="https://i.pravatar.cc/100?u=2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                                            <img src="https://i.pravatar.cc/100?u=3" class="rounded-circle border border-2 border-white" width="28">
                                                        </div>
                                                        <small class="text-muted fw-bold">4.2k members</small>
                                                    </div>
                                                    <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">Join</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-6 ">
                                    <div class="card h-100 border-0 shadow-sm hover-lift" style="border-radius: 20px;">
                                        <div class="position-relative">
                                            <div style="height: 100px; background: url('https://picsum.photos/400/200?random=51') center/cover; border-radius: 20px 20px 0 0;"></div>
                                            <span class="badge bg-dark text-white position-absolute top-0 end-0 m-3 shadow-sm rounded-pill">Private</span>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="bg-white p-1 rounded-3 shadow-sm d-inline-block position-relative" style="margin-top: -30px; margin-left: 15px;">
                                                <img src="https://picsum.photos/60/60?random=6" class="rounded-3" width="60" height="60">
                                            </div>
                                            <div class="mt-3">
                                                <h5 class="fw-bold mb-1">Design Squad 🎨</h5>
                                                <p class="text-muted small mb-3">Share your UI/UX designs and get feedback from pro monieFlow creators.</p>
                                                <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-group d-flex me-2">
                                                            <img src="https://i.pravatar.cc/100?u=4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                                            <img src="https://i.pravatar.cc/100?u=5" class="rounded-circle border border-2 border-white" width="28">
                                                        </div>
                                                        <small class="text-muted fw-bold">1.8k members</small>
                                                    </div>
                                                    <button class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">Request</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm hover-lift" style="border-radius: 20px;">
                                        <div class="position-relative">
                                            <div style="height: 100px; background: url('https://picsum.photos/400/200?random=52') center/cover; border-radius: 20px 20px 0 0;"></div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="bg-white p-1 rounded-3 shadow-sm d-inline-block position-relative" style="margin-top: -30px; margin-left: 15px;">
                                                <img src="https://picsum.photos/60/60?random=7" class="rounded-3" width="60" height="60">
                                            </div>
                                            <div class="mt-3">
                                                <h5 class="fw-bold mb-1">Gamers Hub 🎮</h5>
                                                <p class="text-muted small mb-3">Find teammates for the monieFlow Arena and climb the leaderboards together.</p>
                                                <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-group d-flex me-2">
                                                            <img src="https://i.pravatar.cc/100?u=6" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                                            <img src="https://i.pravatar.cc/100?u=7" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -10px;">
                                                            <img src="https://i.pravatar.cc/100?u=8" class="rounded-circle border border-2 border-white" width="28">
                                                        </div>
                                                        <small class="text-muted fw-bold">12k members</small>
                                                    </div>
                                                    <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">Join</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                        helper(html)
}




function enginering() {
  window.like_post = async function like_post(feedId, status) {
    try {
      const res = await fetch('/feeds/req.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "like_post", feed_id: feedId, status: status })
      });

      const req = await res.text(); // or res.json() if you return JSON

      // Update the UI if you have an element like <span id="like-123"></span>
      const target = document.querySelector(`#like-${feedId}`);
      if (target) {
        target.innerHTML = req;
      }

      console.log(req);
    } catch (err) {
      console.error("Error liking post:", err);
    }
  }
}
