import router from "../helperJs/helperRouter.js";
import { Footer } from "./feeds.js";

export default async function Connections() {
    // 1. DATA STATE
    const state = {
        activeTab: 'followers', // 'followers' or 'following'
        searchQuery: '',
        users: {
            followers: [
                { id: 101, name: "Luna Star", handle: "@luna_crypto", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Luna", status: "Following", mutuals: 12 },
                { id: 102, name: "Crypto King", handle: "@cking99", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=King", status: "Follow", mutuals: 5 },
                { id: 103, name: "Sarah Jenkins", handle: "@sara_j", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah", status: "Following", mutuals: 21 },
            ],
            following: [
                { id: 201, name: "Vitalik B.", handle: "@vbuterin", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Vitalik", status: "Following", mutuals: 150 },
                { id: 202, name: "Elon Tusk", handle: "@tusk_m", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Elon", status: "Following", mutuals: 0 },
            ]
        }
    };

    // 2. LOGIC
    window.switchTab = (tab) => {
        state.activeTab = tab;
        renderConnections();
    };

    window.toggleFollow = (id) => {
        // Find user in either list and flip status
        const allUsers = [...state.users.followers, ...state.users.following];
        const user = allUsers.find(u => u.id === id);
        if (user) {
            user.status = user.status === 'Following' ? 'Follow' : 'Following';
            renderConnections();
        }
    };

    // 3. UI FRAGMENTS
    const UserCard = (user) => `
     
        <div class="col-12 col-md-6 mb-3">
            <div class="card border-0 shadow-sm p-3 hover-lift transition-all" style="border-radius: 20px; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px);">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative">
                            <img src="${user.avatar}" class="rounded-circle border border-2 border-white shadow-sm" width="55" height="55">
                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" style="width:12px; height:12px;"></span>
                        </div>
                        <div>
                            <h6 class="fw-black mb-0 text-dark">${user.name}</h6>
                            <small class="text-muted d-block mb-1">${user.handle}</small>
                            ${user.mutuals > 0 ? `<span class="badge bg-light text-muted border fw-normal" style="font-size: 0.65rem;">${user.mutuals} mutual friends</span>` : ''}
                        </div>
                    </div>
                    <button onclick="toggleFollow(${user.id})" 
                            class="btn ${user.status === 'Following' ? 'btn-outline-secondary' : 'btn-primary'} btn-sm rounded-pill px-3 fw-bold shadow-sm transition-all"
                            style="min-width: 100px;">
                        ${user.status}
                    </button>
                </div>
            </div>
        </div>
    `;

    // 4. THE LIVE RE-RENDERER
    const renderConnections = () => {
        const container = document.getElementById('connections-list');
        const countDisplay = document.getElementById('current-count');
        const data = state.users[state.activeTab];
        
        if (container) {
            container.innerHTML = data.length > 0 
                ? data.map(UserCard).join('') 
                : `<div class="text-center py-5 opacity-50"><i class="bi bi-people fs-1 d-block mb-2"></i>No users found.</div>`;
        }
        if (countDisplay) countDisplay.innerText = data.length;
    };

    // 5. MAIN TEMPLATE
    return /* html */ `
     <nav class="bg-white px-2 sticky-top py-3 shadow-sm" style="background-color:#232f3e">
        <div class="container d-flex justify-content-between mx-auto">
          <div class="d-flex align-items-center gap-2">
            <span class="btn btn-light cursor-pointer" onclick="window.history.back()">
              <i class="bi bi-arrow-left"></i>
            </span>
            <h2 class="fw-light mb-0 ms-2" style="color:#fff;font-size:1.2rem">
              <span class="text-dark text-capitalize fw-bold">Squad</span>
            </h2>
          </div>
        </div>
      </nav>
    <div class="container py-4 px-md-5" style="max-width: 900px;">
        <!--<div class="text-center mb-5">
            <h2 class="fw-black mb-1">Your Network</h2>
            <p class="text-muted">Manage your connections and discover new flows</p>
        </div>-->

        <div class="card border-0 shadow-sm mb-4 rounded-pill p-1 mx-auto" style="max-width: 500px; background: #eee;">
            <div class="row g-0 text-center">
                <div class="col-6">
                    <button onclick="switchTab('followers')" 
                            class="btn w-100 rounded-pill py-2 fw-black transition-all ${state.activeTab === 'followers' ? 'bg-white shadow-sm text-primary' : 'text-muted border-0 bg-transparent'}">
                        Followers
                    </button>
                </div>
                <div class="col-6">
                    <button onclick="switchTab('following')" 
                            class="btn w-100 rounded-pill py-2 fw-black transition-all ${state.activeTab === 'following' ? 'bg-white shadow-sm text-primary' : 'text-muted border-0 bg-transparent'}">
                        Following
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <div class="fw-bold text-secondary">
                Showing <span id="current-count" class="text-dark fw-black">${state.users[state.activeTab].length}</span> ${state.activeTab}
            </div>
            <div class="input-group bg-white rounded-pill shadow-sm border px-3 py-1" style="max-width: 300px;">
                <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control bg-transparent border-0 shadow-none small" placeholder="Search names...">
            </div>
        </div>

        <div class="row" id="connections-list">
            ${state.users[state.activeTab].map(UserCard).join('')}
        </div>
    </div>

    <style>
        .hover-lift:hover {
            transform: translateY(-3px);
            background: #fff !important;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }
        .transition-all {
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .fw-black { font-weight: 900; }
    </style>
    ${Footer({page: 'squads'})}
    `;
}