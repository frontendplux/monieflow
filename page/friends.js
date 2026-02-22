import { loader_board } from "../pages.js";

export function people_you_may_know_ads (data){ 
 window.addFriend=function(friendId) {
 const card = document.getElementById(`friend-request-sent-${friendId}`);
  card.querySelector("button").outerHTML =
  `<button class="btn btn-success mb-1 btn-sm w-100 fw-bold" disabled>
     Request Sent
   </button>`;
  const fd = new FormData();
  fd.append('action', 'add_friend');
  fd.append('friend_id', friendId);
  fetch(loader_board, { method: 'POST', body: fd })
}
   return data.length ? `
   <div class="card m-2 border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
            <h6 class="fw-bold mb-0 text-capitalize">people you may know</h6>
            <span data-href="friends?type=people_you_may_know" class="small text-decoration-none" style="cursor: pointer;">See All</span>
        </div>

        <div class="d-flex overflow-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none;">
              ${data.map(e=>{return`<div class="flex-shrink-0 me-2" style="width: 160px;">
                <div id="friend-request-sent-${e.id}" class="card border h-100 rounded-3 overflow-hidden">
                    <img src="/uploads/${e.profile_pic}" class="card-img-top object-fit-cover" height="150">
                    <div class="p-2">
                        <p class="small fw-bold mb-2 text-truncate">${e.username}</p>
                        <button class="btn btn-primary btn-sm w-100 mb-1 fw-bold" onclick="addFriend(${e.id})">Add friend</button>
                        <button class="btn btn-light btn-sm w-100 fw-bold border" data-href="profiles?u=${e.id}">see more</button>
                    </div>
                </div>
            </div>`}).join('')}

        </div>
    </div>
</div>
  ` : '';
}


// export async function people_you_may_know() { 
//   // Define global addFriend handler
//   window.addFriend = async function(friendId) {
//     const card = document.getElementById(`friend-request-sent-${friendId}`);
//     if (card) {
//       // specifically target the Add Friend button (btn-primary)
//       const addBtn = card.querySelector("button.btn-primary");
//       if (addBtn) {
//         addBtn.outerHTML = `
//           <button class="btn btn-success mb-1 btn-sm w-100 fw-bold" disabled>
//             Request Sent
//           </button>`;
//       }
//     }

//     const fd = new FormData();
//     fd.append('action', 'add_friend');
//     fd.append('friend_id', friendId);

//     await fetch(loader_board, { method: 'POST', body: fd });
//   };

//   // Fetch non-friends
//   const fd = new FormData();
//   fd.append('action', 'get_non_friends');

//   const people_you_may_knows = await fetch(loader_board, { method: 'POST', body: fd })
//     .then(res => res.json());

//   // Render cards
//   const container = document.getElementById('loader-data-friends-add');
//   if (container && people_you_may_knows.success) {
//     container.innerHTML += people_you_may_knows.users.map(e => `
//       <div class="col-12 col-md-6 col-lg-4 col-xl-3">
//         <div class="card border-0 rounded-5 shadow-sm overflow-hidden h-100 p-2 connection-card">
//           <div class="rounded-4 overflow-hidden position-relative" style="height: 100px; background: #eef2f7;">
//             <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n4">
//               <img src="/uploads/${e.profile_pic}" 
//                    class="rounded-circle border border-4 border-white shadow-sm" 
//                    width="80" height="80">
//             </div>
//           </div>
//           <div class="card-body text-center pt-5">
//             <h6 class="fw-bold mb-1">${e.username}</h6>
//             <p class="text-muted small mb-3">${e.mutual_friends ?? 0} mutual friends</p>
//             <div class="d-flex gap-2" id="friend-request-sent-${e.id}">
//               <button data-href="profiles?u=${e.id}" 
//                       class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 fw-bold">
//                 Profile
//               </button>
//               <button onclick="addFriend(${e.id})" 
//                       class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-bold shadow-sm">
//                 Add friend
//               </button>
//             </div>
//           </div>
//         </div>
//       </div>`).join('');
//   }
// }

export async function myfriendlist() {

  const container = document.getElementById('loader-data-friends-add');
  if (!container) return;

  /* ==============================
     FETCH NON FRIENDS
  ============================== */

  const fd = new FormData();
  fd.append('action', 'get_friend_requests');

  const response = await fetch(loader_board, {
    method: 'POST',
    body: fd
  });

  const people_you_may_knows = await response.json();

  if (!people_you_may_knows.success) return;

  /* ==============================
     RENDER CARDS
  ============================== */

  container.innerHTML= people_you_may_knows.users.map(e => `
    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
      <div class="card border-0 rounded-5 shadow-sm overflow-hidden h-100 p-2 connection-card">
        
        <div class="rounded-4 overflow-hidden position-relative" 
             style="height: 100px; background: #eef2f7;">
             
          <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n4">
            <img src="/uploads/${e.profile_pic}" 
                 class="rounded-circle border border-4 border-white shadow-sm" 
                 width="80" height="80">
          </div>
        </div>

        <div class="card-body text-center pt-5">
          <h6 class="fw-bold mb-1">${e.username}</h6>
          <p class="text-muted small mb-3">
            ${e.mutual_friends ?? 0} mutual friends
          </p>

          <div class="d-flex gap-2">
            <button data-href="profiles?u=${e.id}" 
                    class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 fw-bold">
              Profile
            </button>

            <button data-id="${e.id}" 
                    class="btn btn-primary add-friend-btn btn-sm rounded-pill flex-grow-1 fw-bold shadow-sm">
              Add friend
            </button>
          </div>
        </div>

      </div>
    </div>
  `).join('');

  /* ==============================
     EVENT DELEGATION (NO GLOBAL FUNCTION)
  ============================== */

  container.addEventListener('click', async function(e) {

    const button = e.target.closest('.add-friend-btn');
    if (!button) return;

    const friendId = button.dataset.id;

    /* ---- Loading State ---- */
    button.textContent = "Sending...";
    button.disabled = true;

    const fd = new FormData();
    fd.append('action', 'add_friend');
    fd.append('friend_id', friendId);

    try {
      const res = await fetch(loader_board, {
        method: 'POST',
        body: fd
      });

      const data = await res.json();

      if (data.success) {
        button.classList.remove('btn-primary');
        button.classList.add('btn-success');
        button.textContent = "Request Sent";
      } else {
        button.classList.remove('btn-primary');
        button.classList.add('btn-secondary');
        button.textContent = "Failed";
      }

    } catch (error) {
      button.classList.remove('btn-primary');
      button.classList.add('btn-danger');
      button.textContent = "Error";
    }

  });

}


export async function people_you_may_know() {

  const container = document.getElementById('loader-data-friends-add');
  if (!container) return;

  /* ==============================
     FETCH NON FRIENDS
  ============================== */

  const fd = new FormData();
  fd.append('action', 'get_non_friends');

  const response = await fetch(loader_board, {
    method: 'POST',
    body: fd
  });

  const people_you_may_knows = await response.json();

  if (!people_you_may_knows.success) return;

  /* ==============================
     RENDER CARDS
  ============================== */

  container.innerHTML= people_you_may_knows.users.map(e => `
    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
      <div class="card border-0 rounded-5 shadow-sm overflow-hidden h-100 p-2 connection-card">
        
        <div class="rounded-4 overflow-hidden position-relative" 
             style="height: 100px; background: #eef2f7;">
             
          <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n4">
            <img src="/uploads/${e.profile_pic}" 
                 class="rounded-circle border border-4 border-white shadow-sm" 
                 width="80" height="80">
          </div>
        </div>

        <div class="card-body text-center pt-5">
          <h6 class="fw-bold mb-1">${e.username}</h6>
          <p class="text-muted small mb-3">
            ${e.mutual_friends ?? 0} mutual friends
          </p>

          <div class="d-flex gap-2">
            <button data-href="profiles?u=${e.id}" 
                    class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 fw-bold">
              Profile
            </button>

            <button data-id="${e.id}" 
                    class="btn btn-primary add-friend-btn btn-sm rounded-pill flex-grow-1 fw-bold shadow-sm">
              Add friend
            </button>
          </div>
        </div>

      </div>
    </div>
  `).join('');

  /* ==============================
     EVENT DELEGATION (NO GLOBAL FUNCTION)
  ============================== */

  container.addEventListener('click', async function(e) {

    const button = e.target.closest('.add-friend-btn');
    if (!button) return;

    const friendId = button.dataset.id;

    /* ---- Loading State ---- */
    button.textContent = "Sending...";
    button.disabled = true;

    const fd = new FormData();
    fd.append('action', 'add_friend');
    fd.append('friend_id', friendId);

    try {
      const res = await fetch(loader_board, {
        method: 'POST',
        body: fd
      });

      const data = await res.json();

      if (data.success) {
        button.classList.remove('btn-primary');
        button.classList.add('btn-success');
        button.textContent = "Request Sent";
      } else {
        button.classList.remove('btn-primary');
        button.classList.add('btn-secondary');
        button.textContent = "Failed";
      }

    } catch (error) {
      button.classList.remove('btn-primary');
      button.classList.add('btn-danger');
      button.textContent = "Error";
    }

  });

}




export function friends(type = "People you may know", desc="get connected to users on monieFlow"){
    return`
    <header class="bg-white sticky-top">
        <span onclick="history.back()" class=" container d-block cursor-pointer py-2"> <i style="width:40px; height:40px" class="ri-arrow-left-s-fill d-flex justify-content-center align-self-center p-0 fs-1  bg-light rounded-circle"></i> </span>
    </header>
    <div class="container py-4 pb-5 mb-5">
    <div class="row mb-4 align-items-center">
        <div class=" text-center  mb-3">
            <h2 class="fw-bolder mb-0 text-uppercase">${type}</h2>
            <span class="text-muted fw-bold small">${desc}</span>
        </div>
        <div class="col-md-6 mx-auto">
            <div class="input-group bg-white rounded-pill shadow-sm border p-1">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Find a friend...">
                <button class="btn btn-primary rounded-pill px-4">Search</button>
            </div>
        </div>
        <div class="d-flex my-3 gap-3 overflow-auto">
            <span class="btn btn-outline-primary rounded-pill">people&nbsp;you&nbsp;may&nbsp;know</span>
            <span class="btn btn-outline-primary rounded-pill">friends</span>
            <span class="btn btn-outline-primary rounded-pill">friend&nbsp;request</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="row g-4" id="loader-data-friends-add">


            ${
                Array.from({ length: 12 }).map(() => `
<div class="col-12 col-md-6 col-lg-4 col-xl-3">
    <div class="card border-0 rounded-5 shadow-sm overflow-hidden h-100 p-2 placeholder-glow">
        
        <div class="rounded-4 position-relative glow-shimmer" style="height: 100px;">
            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n4">
                <div class="rounded-circle border border-4 border-white skeleton-avatar"
                     style="width: 80px; height: 80px;">
                </div>
            </div>
        </div>

        <div class="card-body text-center pt-5">
            <h6 class="placeholder col-8 rounded-pill mb-2"></h6>
            <p class="placeholder col-5 small mb-3 rounded-pill"></p>
            
            <div class="d-flex gap-2">
                <span class="btn btn-primary disabled placeholder col-6 rounded-pill"></span>
                <span class="btn btn-outline-primary disabled placeholder col-6 rounded-pill"></span>
            </div>
        </div>
    </div>
</div>
`).join('')
            }

            </div> 
            </div>
    </div>
</div>
<style>
@keyframes skeleton-glow {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.glow-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-glow 1.5s infinite;
    border: none !important;
}

.skeleton-avatar {
    background: #e0e0e0;
    display: inline-block;
}
</style>`
}





export default async function friendsMEmoS(params) {


const fd = new FormData();
fd.append('action', 'get_non_friends');
const people_you_may_knows= await fetch(loader_board, { method: 'POST', body: fd }).then(res => res.json());
console.log(people_you_may_knows.users);
return `
${people_you_may_know_ads(people_you_may_knows.users.slice(0,10))}
`
}



function acceptFriend(friendId) {
  const fd = new FormData();
  fd.append('action', 'accept_friend');
  fd.append('friend_id', friendId);

  fetch(loader_board, { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => alert(data.message));
}
