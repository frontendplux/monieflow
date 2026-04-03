import { Auth } from "../helperJs/helperauth.js";
import { Feed } from "../helperJs/helperfeeds.js";
import { friends } from "../helperJs/helperfriend.js";
import router from "../helperJs/helperRouter.js";
import { timeAgo } from "../helperJs/mainhelper.js";
import { isloggedin } from "./auth.js";


export default function Home() {
   const feedManager = new Feed();
   const auth=new Auth();
    let page = 0;
    const limit = 10;
    let initialFeeds = []; // ✅ stays an array

    async function checkislogin(){
       const islogin = await isloggedin();
       if (!islogin) return router('/login');
    }
   checkislogin();

  const getUsersInfo=async ()=>{
    const userInfo= await auth.getuser(null, 'me');
    document.querySelectorAll('.user-profile-right').forEach(el => {
      el.innerHTML= userInfo.success ? `
      <img src="${userInfo.data.avatar != null ? userInfo.data.avatar : '/uploads/user.png'}" class="rounded-circle border" width="40" onclick="router('/profile')">
      `:'<img src="/uploads/user.png" class="rounded-circle border" width="40" onclick="router(\'/login\')">';
    });
    
      document.querySelector('.user-profile-right-2').innerHTML= userInfo.success ? `
      <img src="${userInfo.data.avatar != null ? userInfo.data.avatar : '/uploads/user.png'}" class="rounded-circle border" width="22px" height="22px">
      `:'<img src="/uploads/user.png" class="rounded-circle border" width="22px" height="22px">';
    console.log(userInfo);
  }


  const loadFeeds = async () => {
    const container = document.getElementById('feed-container');
    if (!container) return;

    container.innerHTML = `
    <div id="loading" class="card placeholder-wave mb-4 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
  <div class="card-body p-0">
    <!-- Header with avatar + name -->
    <div class="d-flex align-items-center mb-3 p-2">
      <span class="placeholder rounded-circle me-3" style="width:45px; height:45px; border:2px solid #eee;"></span>
      <div>
        <h6 class="mb-0 fw-bold">
          <span class="placeholder col-6"></span>
        </h6>
        <small class="text-muted">
          <span class="placeholder col-4"></span>
        </small>
      </div>
    </div>

    <!-- Content text -->
    <p class="card-text px-2" style="font-size: 1.1rem;">
      <span class="placeholder col-12"></span>
      <span class="placeholder col-10"></span>
      <span class="placeholder col-8"></span>
    </p>

    <!-- Image area -->
    <div class="postfiled-imaging px-2">
      <span class="placeholder col-12" style="height:200px;"></span>
    </div>

    <!-- Stats row -->
    <div class="d-flex gap-2 px-2 mt-3" style="font-size:small; color:#555;">
      <span class="placeholder col-2"></span>
      <span class="placeholder col-2"></span>
      <span class="placeholder col-2"></span>
      <span class="placeholder col-2"></span>
    </div>
    <hr class="my-2 opacity-10">

    <!-- Action buttons -->
    <div class="d-flex gap-2 px-2">
      <span class="placeholder btn btn-light col-2"></span>
      <span class="placeholder btn btn-light col-2"></span>
      <span class="placeholder btn btn-light col-2"></span>
      <span class="placeholder btn btn-light col-2"></span>
      <span class="placeholder btn btn-light col-2"></span>
    </div>
  </div>
</div>

`;

    const data = await feedManager.fetchFeeds(null, page, limit);
    document.getElementById('loading').remove(); // ✅ remove loading indicator
    if (data.feeds.length > 0) {
        data.feeds.forEach(post => {
            container.insertAdjacentHTML('beforeend', PostCard(post));
        });
    }
};

    const getfriends= async()=>{
         const friendClass=new friends();
         const friendData= await friendClass.getFriends();
         document.getElementById('friend-count').textContent= friendData.length;
         document.getElementById('friend-list').innerHTML= friendData.map(f => {
          return   f.isFollowing ? '':
              
                            `
                                  <div class="d-flex justify-content-between my-2 align-items-center"> 
                                    <li class="d-flex align-items-center gap-2 p-2 hover-bg-light rounded cursor-pointer">
                                        <div class="position-relative">
                                            <img src="${f.avatar == null ? '/uploads/user.png' : f.avatar}" class="rounded-circle" width="35">
                                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
                                        </div>
                                        <span class="fw-bold">${f.username == '' ? f.firstname+'&nbsp;'+ f.lastname : f.username}</span>
                                    </li> 
                                    <button onclick="followUser('${f.id}')" class="btn btn-warning bi bi-plus py-0  rounded-pill"></button>
                                  </div>
         `}).join('');
         console.log(friendData);
    }
requestAnimationFrame(getfriends);
requestAnimationFrame(getUsersInfo);
requestAnimationFrame(loadFeeds);

    // 2. Setup Infinite Scroll Logic
    let loading = false;

    window.onscroll = async () => {
        if (loading) return;
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight -100) {
            loading = true;
            page++;
            const moreFeeds = await feedManager.fetchFeeds(null, page, limit);

            if (moreFeeds.feeds.length > 0) {
                const container = document.getElementById('feed-container');
                // ✅ safer append
                moreFeeds.feeds.forEach(post => {
                    container.insertAdjacentHTML('beforeend', PostCard(post));
                });
            }

            loading = false;
        }
    }


//     const PostCard = (post) => `
//         <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
//             <div class="card-body p-0">
//                 <div class="d-flex align-items-center mb-3 p-2">
//                     <img src="${post.userAvatar || 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + post.userId}" 
//                          class="rounded-circle me-3" style="width: 45px; height: 45px; border: 2px solid #eee;">
//                     <div>
//                         <h6 class="mb-0 fw-bold">${post.userName || 'Explorer'}</h6>
//                         <small class="text-muted">
//                             ${timeAgo(post.updated_at) || 'Just now'} • 
//                             <i class="bi bi-globe-americas"></i> ${post.stage_name}
//                         </small>
//                     </div>
//                 </div>

//                 <p class="card-text px-2" style="font-size: 1.1rem;">
//                     ${post.content || ""}
//                 </p>

//                 <div class="postfiled-imaging ">
//                     ${
//                       post.image && post.image.length > 0
//                         ? post.image.map((post_img,key) => `<img src="${post_img}" class="">`).join("")
//                         : ""
//                     }
//                 </div>

//                 <div class="d-flex gap-1 px-2" style="font-size:small; color: #555;">
//                     <span>${post.like_count}</span>.<span id="post-like-name-${post.id}">Like</span> 
//                     <span>${post.comment_count}</span>.<span id="post-comment-${post.id}">Comment</span>
//                     <span>${post.like_count}</span>.<span id="post-like-name-${post.id}">tips</span>
//                     <span>${post.like_count}</span>.<span id="post-like-name-${post.id}">Share</span>
//                     <span>${post.like_count}</span>.<span id="post-like-name-${post.id}">Repost</span>
//                 </div>
//                 <hr class="my-2 opacity-10">

//                 <div class="bg-light bg-opacity-50 p-3">
          
//           ${post.comments.map((e, key) => (
//   <div key={key} className="d-flex gap-2 mb-3">
//     <img 
//       src={`https://api.dicebear.com/7.x/avataaars/svg?seed=${e.username || 'User'}`} 
//       alt={e.username || "User"} 
//       className="rounded-circle" 
//       style={{ width: '32px', height: '32px' }} 
//     />
//     <div className="p-2 px-3 bg-white rounded-4 shadow-sm" style={{ fontSize: '0.85rem' }}>
//       <span className="fw-bold d-block">{e.username}</span>
//       <span>{e.content}</span>
//       <div className="mt-1 d-flex gap-3 text-muted" style={{ fontSize: '0.75rem' }}>
//         <span className="fw-bold" style={{ cursor: 'pointer' }}>{e.like_count}.Like</span>
//         <span className="fw-bold" style={{ cursor: 'pointer' }}>{e.reply_count}.Reply</span>
//       </div>
//     </div>
//   </div>
// ))}

//           <div className="d-flex align-items-center gap-2">
//             <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" alt="User" className="rounded-circle" style={{ width: '32px' }} />
//             <div className="input-group">
//               <input type="text" className="form-control rounded-pill border-0 px-3 shadow-sm" placeholder="Write a friendly comment..." style={{ fontSize: '0.85rem' }} />
//             </div>
//           </div>
//         </div>

//                 <div class="">
//                     <button class="btn btn-ourline-light py-2 border-0 bg-transparent text-muted fw-bold"><i class="bi bi-hand-thumbs-up"></i></button>
//                     <button class="btn btn-ourline-light py-2 border-0 bg-transparent text-muted fw-bold"><i class="bi bi-chat-square"></i></button>
//                     <button class="btn btn-ourline-light py-2 border-0 bg-transparent text-muted fw-bold"><i class="bi bi-share"></i></button>
//                     <button class="btn btn-ourline-light py-2 border-0 bg-transparent text-muted fw-bold"><i class="bi bi-diamond"></i></button>
//                     <button class="btn btn-ourline-light py-2 border-0 bg-transparent text-muted fw-bold"><i class="bi bi-link"></i></button>
//                 </div>
//             </div>
//         </div>
//     `;

    // 4. Return the full Layout
    
    const PostCard = (post) => `
  <div class="card mb-4 border-0 shadow-sm" style="border-radius: 5px; overflow: hidden;">
    <div class="card-body p-0">
      <!-- Post header -->
      <div class="d-flex align-items-center mb-3 p-2">
        <img src="/${post.userAvatar || 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + post.userId}" 
             class="rounded-circle me-3" style="width: 45px; height: 45px; border: 2px solid #eee;">
        <div>
          <h6 class="mb-0 fw-bold">${post.username || post.firstname + ' ' + post.lastname}</h6>
          <small class="text-muted">
            ${timeAgo(post.updated_at) || 'Just now'} • 
            <i class="bi bi-globe-americas"></i> ${post.stage_name}
          </small>
        </div>
      </div>

      <!-- Post content -->
      <p class="card-text px-2" style="font-size: 1.1rem;">
        ${post.content || ""}
      </p>

      <!-- Post images -->
      <div class="postfiled-imaging">
        ${
          post.image && post.image.length > 0
            ? post.image.map((post_img) => `<img src="/uploads/${post_img}" class="">`).join("")
            : ""
        }
      </div>

      <!-- Stats -->
      <div class="d-flex gap-1 px-2" style="font-size:small; color: #555;">
        <span>${post.like_count}</span>.<span id="post-like-name-${post.id}">Like</span> 
        <span>${post.comment_count}</span>.<span id="post-comment-${post.id}">Comment</span>
        <span>${post.like_count}</span>.<span>Tips</span>
        <span>${post.like_count}</span>.<span>Share</span>
        <span>${post.like_count}</span>.<span>Repost</span>
      </div>
      <hr class="my-2 opacity-10">

      <!-- Comments Section -->
      <div class="bg-light bg-opacity-50 p-3">
        ${
          post.comments && post.comments.length > 0
            ? post.comments.map((e) => `
              <div class="d-flex gap-2 mb-3">
                <img 
                  src="https://api.dicebear.com/7.x/avataaars/svg?seed=${e.username || 'User'}" 
                  alt="${e.username || 'User'}" 
                  class="rounded-circle" 
                  style="width:32px; height:32px;" 
                />
                <div class="p-2 px-3 bg-white rounded-4 shadow-sm" style="font-size:0.85rem;">
                  <span class="fw-bold d-block">${e.username}</span>
                  <span>${e.content}</span>
                  <div class="mt-1 d-flex gap-3 text-muted" style="font-size:0.75rem;">
                    <span class="fw-bold" style="cursor:pointer;">${e.like_count}.Like</span>
                    <span class="fw-bold" style="cursor:pointer;">${e.reply_count}.Reply</span>
                  </div>
                </div>
              </div>
            `).join("")
            : `<div class="text-muted px-2">No comments yet</div>`
        }
      </div>
      <!-- Action Buttons -->
      <div class="px-2 mt-2 py-3">
        <button class="btn btn-light py-2 px-3 bg-transparent text-muted fw-bold">
          <i class="bi bi-hand-thumbs-up-fill"></i>
        </button>
        <button class="btn btn-light py-2 px-3 bg-transparent text-muted fw-bold">
          <i class="bi bi-chat-square-fill"></i>
        </button>
        <button class="btn btn-light py-2 px-3 bg-transparent text-muted fw-bold">
          <i class="bi bi-share"></i>
        </button>
        <button class="btn btn-light py-2 px-3 bg-transparent text-muted fw-bold">
          <i class="bi bi-gem"></i>
        </button>
        <button class="btn btn-light py-2 px-3 bg-transparent text-muted fw-bold">
          <i class="bi bi-fullscreen"></i>
        </button>
      </div>
    </div>
  </div>
`;

    
    return /* html */ `
    <form action="search" id="searchPort" class="w-100 p-2 bg-white d-none d-flex align-items-center d-md-none position-fixed top-0" style="z-index: 10050;">
        <input type="search" name="q" class="form-control" placeholder="Search MonieFlow..." />
        <i onclick="document.getElementById('searchPort').classList.toggle('d-none');" class="rounded-circle ms-2 fs-1 bi bi-x-lg"></i>
    </form>
    <nav class="sticky-top position-fixed start-0 end-0 border-bottom bg-white shadow-sm" style="backdrop-filter: blur(20px); z-index: 1050;">
        <div class="container py-2">
            <div class="row align-items-center">
                <div class="col-4 col-lg-3">
                    <a class="navbar-brand fw-bold text-primary fs-3" href="/">
                        monie<span class="text-warning">Flow</span>
                    </a>
                </div>
                <div class="col-4 col-lg-6 d-none d-lg-block">
                    <div class="input-group bg-light rounded-pill px-3">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-transparent border-0 shadow-none" placeholder="Search MonieFlow">
                    </div>
                </div>
                <div class="col-8 col-lg-3 text-end">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <div onclick="router('/chat')" style="width:40px; height:40px" class="bg-light d-md-flex d-none justify-content-center align-items-center rounded-circle"><i class="bi bi-chat-dots"></i></div>
                        <div onclick="document.getElementById('searchPort').classList.toggle('d-none');" style="width: 40px; height: 40px;" class="bg-light d-flex d-lg-none justify-content-center align-items-center rounded-circle cursor-pointer"><i class="bi bi-search"></i></div>
                        <div onclick="router('/squads')" style="width: 40px; height: 40px;" class="bg-light d-flex d-md-none justify-content-center align-items-center rounded-circle cursor-pointer"><i class="bi bi-people-fill"></i></div>
                        <div onclick="router('/trade')" style="width: 40px; height: 40px;" class="bg-light d-none d-md-flex justify-content-center align-items-center rounded-circle cursor-pointer"><i class="bi bi-rocket"></i></div>
                        <div onclick="router('/alerts')" style="width: 40px; height: 40px;" class="bg-light d-flex justify-content-center align-items-center rounded-circle cursor-pointer"><i class="bi bi-bell"></i></div>
                        <div class="user-profile-right"><i class="spinner-border text-warning"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4 my-5 pb-5">
        <div class="row g-4">
            <aside class="col-md-3 d-none d-md-block position-sticky" style="top: 0px; height: fit-content;">
                <div class="list-group list-group-flush bg-transparent">
                      ${
                        [['Create post', 'bi-plus-circle', 'create-post'], ['Friends', 'bi-people', 'squads'], ['Market place','bi-shop', 'store'], ['Notification', 'bi-bell', 'alerts']].map((item, index) => `
                        <a data-link href="${item[2]}" class="list-group-item  list-group-item-action border-0 bg-transparent fw-bold py-3">
                            <i class="bi ${item[1]} me-3 fs-4 text-primary"></i> <span>${item[0]}</span>
                        </a>
                        `).join('')
                      }
                </div>
            </aside>

            <main class="col-12 col-md-8 col-lg-5 mx-auto">
                <div class="card mb-2 border-0 shadow-sm p-3 py-2" style="border-radius: 15px;">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="user-profile-right"><i class="spinner-border text-warning"></i></div>
                        <button onclick="router('/create-post')" class="btn btn-light rounded-pill w-100 text-start text-muted px-4 py-2 bg-light border-0" style="font-size: 1.1rem;">
                            What's on your mind?
                        </button>
                        <button onclick="router('/create-post')" style="height: 40px; width: 40px;" class="d-flex justify-content-center align-items-center rounded-circle bg-warning btn"><i class="bi bi-plus px-3"></i></button>
                    </div>
                </div>

                <div id="feed-container">
                    ${initialFeeds.map(post => PostCard(post)).join('')}
                </div>
                
                <div id="loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </main>

            <aside class="col-md-3 rounded-4 d-none  d-lg-block position-sticky" style="top: 0px; height: fit-content;">
                <div class="card border-0">
                    <div class="bg-white rounded-4">
                        <div class="card-header bg-white  border-bott0 fw-bold text-muted d-flex justify-content-between">
                          <div> Get Connected</div>
                            <div class="badge bg-primary rounded-pill" id="friend-count">0</div>
                              <!-- <i class="bi bi-camera-video-fill me-2"></i>
                                <i class="bi bi-search"></i> -->
                            </div>
                        </div>
                        <div class="card-body px-6 ">
                            <ul class="list-unstyled" id="friend-list">
                            </ul>
                        </div>
                    </div>
                    <div class="l-pro-line bg-white p-2 my-3 rounded-4" style="">
                       <img src="/uploads/user.png" alt="">
                       <img src="/uploads/user.png" alt="">
                       <img src="/uploads/user.png" alt="">
                    </div>
                </div>
            </aside>
        </div>
        ${Footer({page: 'home'})}
    </div>`
}

export function Footer(data = {page: 'home', islogin: false}) {
  return /* html */ `
    <footer class="fixed-bottom d-md-none bg-white border-top shadow-lg">
      <div class="container-fluid">
        <div class="row text-center py-2">
          <!-- 1. HOME -->
          <a data-link href="home" class="col px-0 text-decoration-none" style="cursor:pointer">
            <div style="${data.page === 'home' ? 'color:#ff9900;' : 'color:#232f3e;'}font-size:1.5rem;" class="bi bi-house-heart"></div>
            <div style="font-size:0.65rem;font-weight:${data.page === 'home' ? '800' : '600'};color:#131921;margin-top:-4px;">HOME</div>
            ${data.page === 'home' ? `<div style="height:3px;width:20px;background:#ff9900;margin:2px auto 0;border-radius:10px;"></div>` : ''}
          </a>

          <!-- 2. SQUADS -->
          <a data-link href="/trade" class="col px-0 text-decoration-none" style="cursor:pointer">
            <div style="color:${data.page === 'trade' ? '#ff9900' : '#232f3e'};font-size:1.5rem;opacity:0.8" class="bi bi-rocket"></div>
            <div style="font-size:0.65rem;font-weight:${data.page === 'trade' ? '800' : '600'};color:#232f3e">TRADE</div>
            ${data.page === 'trade' ? `<div style="height:3px;width:20px;background:#ff9900;margin:2px auto 0;border-radius:10px;"></div>` : ''}
          </a>

          <!-- 3. PLUS BUTTON -->
          <a data-link href="create-post" class="col px-0" style="cursor:pointer;margin-top:-15px">
            <div style="background:${data.page === 'create-post' ? 'wheat' : 'linear-gradient(to bottom,#ffd814,#f7ca00);'};
              width:50px;height:50px;border-radius:15px;margin:0 auto;
              display:flex;align-items:center;justify-content:center;
              font-size:1.8rem;${data.page === 'create-post' ? '':'box-shadow:0 4px 10px rgba(0,0,0,0.2)'};
              border:1px solid #a88734">
              <i class="bi bi-plus"></i>
            </div>
          </a>

          <!-- 4. ALERTS -->
          <a data-link href="chat" class="col px-0 text-decoration-none" style="cursor:pointer;position:relative">
            <div style="color:${data.page === 'chat' ? '#ff9900' : '#232f3e'};font-size:1.5rem;opacity:0.8" class="bi bi-chat-dots"></div>
            <span class="badge rounded-pill bg-danger" style="position:absolute;top:0;right:20%;font-size:0.6rem">3</span>
            <div style="font-size:0.65rem;font-weight:${data.page === 'chat' ? '800' : '600'};color:#232f3e">CHATS</div>
            ${data.page === 'chat' ? `<div style="height:3px;width:20px;background:#ff9900;margin:2px auto 0;border-radius:10px;"></div>` : ''}
          </a>

          <!-- 5. PROFILE -->
          <a data-link href="profile" class="col px-0 text-decoration-none" style="cursor:pointer">
            <div class="user-profile-right-2" style="width:26px;height:26px;margin:4px auto;border-radius:50%;
              border:2px solid ${data.page === 'profile' ? '#ff9900' : '#232f3e'};overflow:hidden">
              <i class="spinner-border text-warning"></i>
            </div>
            <div style="font-size:0.65rem;font-weight:${data.page === 'profile' ? '800' : '600'};color:#232f3e">PROFILE</div>
            ${data.page === 'profile' ? `<div style="height:3px;width:20px;background:#ff9900;margin:2px auto 0;border-radius:10px;"></div>` : ''}
          </a>
        </div>
      </div>
    </footer>
  `;
}
