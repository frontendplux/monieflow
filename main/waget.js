import feeds, { create_post } from "../pages/feeds.js";
import profile from "../pages/profile.js";

export default async function LoadAllPage(){
  const fd=new FormData();
    fd.append('action', 'fetch_all_feeds');
    const req=await fetch(backend_url,{method:"post", body: fd}).then(res => res.json());
    if(!req.user_info[0])window.location.href='/';
    const user_profile=req.user_info[1].data;
    // const user_profile_compo=JSON.parse(user_profile.profile);
    // console.log(req);
    // feeds(req,user_profile);
    // create_post(user_profile);
    // update_feeds(req.feed,0,1)
    // show_Pages();
    // profile();
}

export  function header(data = {page:"home"}){
    // console.log(data);
     const user_profile_compo=JSON.parse(data.data.profile);
    return`
    <header class="bg-white sticky-top align-items-center d-flex justify-content-center">
        <div class="container  d-flex justify-content-between align-items-center py-2">
            <h1 class="m-0 col">
                
                 <img src='/logo.png' style="width:40px; height:40px" />
            </h1>
            <div class="d-none d-md-flex align-items-center header_link_2 col-md">
               ${
                [
                    ['home','ri-home-fill'],
                    ['friends','ri-group-fill'],
                    ['videos','ri-video-fill'],
                    ['market','ri-store-fill'],
                    ['gampad','ri-gamepad-fill']
                ].map(e=>{return`
                    <span 
                       data-href="${e[0]}" 
                       class="${e[1]} ${data['page'] == e[0] ? 'border-bottom' : '' }  border-primary border-4 fs-4 py-2 px-5"
                    >
                    </span>`}).join('')}
            </div>
            <div class="col-md d-flex gap-2 justify-content-end align-items-center">
              ${
                [
                    ['home','ri-menu-fill'],
                    ['friends','ri-search-fill'],
                    ['friends','ri-messenger-fill'],
                    ['videos','ri-video-fill'],
                    ['market','ri-notification-fill']
                ].map(e=>{return`
                    <span style="width:40px; height:40px" data-header-menu-href="${e[0]}" class="rounded-pill d-flex justify-content-center align-items-center btn btn-light  fs-4 btn">  <i class="${e[1]}"></i> </span>`}).join('')}
                    <span data-href="profile?u=${data.data.id}"><img class="rounded-circle" style="width:40px; height:40px" src="/uploads/${user_profile_compo['profile_pic']}"  /></span>
            </div>
        </div>
    </header>
    `
}

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


function update_feeds(feeds, offset, limit) {
    const container=document.querySelector('.main-div-for-root-per-page  #main-feeds #feeds-main-update-div');
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
              <button class="btn btn-sm btn-outline-secondary rounded-pill d-sm-inline px-3">Message</button>
              <!--<button class="btn btn-sm btn-primary rounded-pill friend-btn">View Profile</button>
              <button class="btn btn-sm"><i class="ri-more-line"></i></button>--!>
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
            <button onclick="like_post(${e.feed_id},'${e.status}')" class="btn btn-light  rounded-pill text-muted">
              <i class="ri-thumb-up-line me-1"></i> <span class="d-none d-md-inline">Like</span> . <b id="like-${e.feed_id}">${e.like_count}</b>
            </button>
            <a href='/feeds/comment/?feed=${e.feed_id}' class="btn btn-light rounded-pill text-decoration-none">
              <i class="ri-chat-3-line me-1"></i> <span class="d-none d-md-inline">Comment</span> . <b id="comment-${e.feed_id}">${e.comment_count}</b>
            </a>
            <span data-href='comment?p=${e.feed_id}' class="btn btn-light  rounded-pill text-decoration-none">
              <i class="ri-share-line me-1"></i> Share
            </span>
          </div>
        </div>
      `;
    }).join('')}
  `;
  container.innerHTML +=html;
}




function show_Pages(){
    const container=document.querySelector('.main-div-for-root-per-page  #main-feeds #feeds-main-update-div');
   var html=`
    <div class="container px-0 my-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h3 class="fw-bold mb-0 fs-5">Explore Groups</h3>
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
                    container.innerHTML += html
}


export const backend_url='/main/backend/req.php';