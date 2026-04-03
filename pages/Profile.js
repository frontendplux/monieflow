import { Feed } from "../helperJs/helperfeeds.js";
import { Footer } from "./feeds.js";

export default async function Profile() {
    // 1. USER STATE
    const user = {
        name: "Felix The Explorer",
        handle: "@felix_flow",
        bio: "Collecting rare crystals and building the future of decentralized flows. 💎🚀",
        avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Felix",
        cover: "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80",
        stats: {
            followers: "1.2k",
            following: "450",
            vaultValue: "$12,450.00"
        },
        badges: ["Early Adopter", "Top Trader", "Crystal Master"]
    };

    // 2. UI FRAGMENTS
    const StatBox = (label, value) => `
        <div class="text-center px-3">
            <div class="fw-black fs-5 text-dark">${value}</div>
            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">${label}</small>
        </div>
    `;

    // 3. MAIN TEMPLATE
    return /* html */ `
    <div class="container-fluid p-0" style="background: #fdfdfd; min-height: 100vh;">
        <div class="position-relative" style="height: 220px; background: url('${user.cover}') center/cover no-repeat;">
            <div class="position-absolute bottom-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.3))"></div>
            
            <button onclick="window.history.back()" class="btn btn-blur m-3 rounded-circle shadow-sm">
                <i class="bi bi-arrow-left text-white"></i>
            </button>
        </div>

        <div class="container" style="margin-top: -60px;">
            <div class="row px-3">
                <div class="col-12 bg-white shadow-sm border-0 p-4 position-relative" style="border-radius: 30px;">
                    
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <div class="position-relative">
                            <img src="${user.avatar}" 
                                 class="rounded-circle border border-5 border-white shadow-lg bg-white" 
                                 style="width: 120px; height: 120px; object-fit: cover; margin-top: -80px;">
                            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-4 border-white rounded-circle shadow-sm"></span>
                        </div>
                        <div class="d-flex gap-2">
                            <button onclick="router('/profile-edit')" class="btn btn-outline-dark rounded-pill px-4 fw-bold btn-sm">Edit Profile</button>
                            <button class="btn btn-warning rounded-pill px-4 fw-black btn-sm shadow-sm">
                                <i class="bi bi-share-fill me-1"></i> Share
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h3 class="fw-black mb-0">${user.name}</h3>
                        <p class="text-primary fw-bold mb-2">${user.handle}</p>
                        <p class="text-secondary mb-3" style="max-width: 500px; line-height: 1.6;">${user.bio}</p>
                        
                        <div class="d-flex gap-2 mb-3">
                            ${user.badges.map(badge => `
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">
                                    ✨ ${badge}
                                </span>
                            `).join('')}
                        </div>
                    </div>

                    <hr class="opacity-10 my-4">
                    <div class="d-flex justify-content-start gap-4">
                        ${StatBox("Followers", user.stats.followers)}
                        ${StatBox("Following", user.stats.following)}
                        ${StatBox("Vault Value", user.stats.vaultValue)}
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <ul class="nav nav-pills justify-content-center mb-4 gap-2 p-1 bg-light rounded-pill mx-auto" style="max-width: 400px;">
                <li class="nav-item">
                    <button class="nav-link active rounded-pill fw-bold px-4" data-bs-toggle="pill">Flows</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link text-muted rounded-pill fw-bold px-4" data-bs-toggle="pill">Vault</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link text-muted rounded-pill fw-bold px-4" data-bs-toggle="pill">Media</button>
                </li>
            </ul>

            <div class="row justify-content-center">
                <div class="col-lg-7" id="profile-feed">
                    <div class="text-center py-5 opacity-50">
                        <i class="bi bi-journal-text fs-1 mb-2"></i>
                        <p class="fw-bold">No public flows yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fw-black { font-weight: 900; }
        .btn-blur {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .nav-pills .nav-link.active {
            background-color: #000 !important;
            color: #fff !important;
        }
        .transition-all { transition: all 0.3s ease; }
    </style>
    ${Footer({page: 'profile'})}
    `;
}


export  async function EditProfile() {
    // 1. MOCK INITIAL STATE
    const user = {
        name: "Felix The Explorer",
        handle: "felix_flow",
        bio: "Collecting rare crystals and building the future of decentralized flows. 💎🚀",
        avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Felix",
        email: "felix@monieflow.com"
    };

    // 2. UI LOGIC
    window.previewImage = (event) => {
        const reader = new FileReader();
        reader.onload = () => {
            document.getElementById('profile-preview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    };

    window.saveProfile = (e) => {
        e.preventDefault();
        const btn = document.getElementById('save-btn');
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Saving...`;
        
        setTimeout(() => {
            btn.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> Profile Updated!`;
            btn.className = "btn btn-success w-100 py-3 rounded-pill fw-black shadow-sm";
        }, 1500);
    };

    // 3. MAIN TEMPLATE
    return /* html */ `
    <div class="container py-5 px-md-5" style="max-width: 800px;">
        <div class="row g-4">
            
            <div class="col-md-4 text-center">
                <div class="card border-0 shadow-sm p-4 sticky-top" style="border-radius: 30px; top: 100px;">
                    <div class="position-relative d-inline-block mb-3">
                        <img id="profile-preview" src="${user.avatar}" 
                             class="rounded-circle border border-5 border-white shadow" 
                             style="width: 130px; height: 130px; object-fit: cover;">
                        
                        <label for="avatar-upload" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow-sm cursor-pointer border border-3 border-white" style="width: 40px; height: 40px;">
                            <i class="bi bi-camera-fill"></i>
                            <input type="file" id="avatar-upload" class="d-none" accept="image/*" onchange="previewImage(event)">
                        </label>
                    </div>
                    <h5 class="fw-black mb-0">${user.name}</h5>
                    <p class="text-muted small">@${user.handle}</p>
                    
                    <hr class="opacity-10">
                    
                    <div class="text-start">
                        <small class="text-uppercase fw-black text-muted" style="font-size: 0.6rem; letter-spacing: 1px;">Account Status</small>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-warning text-dark rounded-pill fw-bold">PRO Member</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-lg p-4 p-md-5" style="border-radius: 35px; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
                    <h3 class="fw-black mb-4">Edit Profile</h3>
                    
                    <form onsubmit="saveProfile(event)">
                        <div class="row g-3">
                            <div class="col-12 mb-3">
                                <label class="form-label small fw-black text-muted px-2">FULL NAME</label>
                                <input type="text" class="form-control custom-input" value="${user.name}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-black text-muted px-2">USERNAME</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-transparent border-0 text-primary fw-bold">@</span>
                                    <input type="text" class="form-control bg-transparent border-0 ps-0 shadow-none" value="${user.handle}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-black text-muted px-2">EMAIL ADDRESS</label>
                                <input type="email" class="form-control custom-input" value="${user.email}" disabled>
                            </div>

                            <div class="col-12 mb-4">
                                <label class="form-label small fw-black text-muted px-2">BIO</label>
                                <textarea class="form-control custom-input" rows="3">${user.bio}</textarea>
                                <div class="form-text text-end" style="font-size: 0.7rem;">Max 160 characters</div>
                            </div>
                        </div>

                        <div class="bg-light rounded-4 p-3 mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0">Private Profile</h6>
                                <small class="text-muted">Only followers can see your flows</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input fs-4 cursor-pointer shadow-none" type="checkbox" role="switch">
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12">
                                <button type="submit" id="save-btn" class="btn btn-primary w-100 py-3 rounded-pill fw-black shadow text-uppercase ls-1">
                                    Save Changes
                                </button>
                            </div>
                            <div class="col-12 text-center mt-3">
                                <button type="button" class="btn btn-link text-danger fw-bold text-decoration-none small">
                                    <i class="bi bi-trash-fill me-1"></i> Deactivate Account
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fw-black { font-weight: 900; }
        .ls-1 { letter-spacing: 1px; }
        .cursor-pointer { cursor: pointer; }

        .custom-input, .custom-input-group {
            background-color: #f8f9fa !important;
            border: 2px solid #f8f9fa !important;
            border-radius: 15px !important;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .custom-input:focus, .custom-input-group:focus-within {
            background-color: #fff !important;
            border-color: #FF9900 !important;
            box-shadow: 0 5px 15px rgba(255, 153, 0, 0.1) !important;
        }

        .form-check-input:checked {
            background-color: #FF9900;
            border-color: #FF9900;
        }

        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    </style>
    ${Footer({page: 'profile'})}
    `;
}