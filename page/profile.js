import { loader_board } from "../pages.js";

export default async function profile(){
    const params = new URLSearchParams(window.location.search);
    const profile_id = params.get("u") ?? '';
    const fd= new FormData();
    fd.append('action','get_user_profile');
    fd.append('profile_id', profile_id);
    const req_fetch=await fetch(loader_board,{method:'post', body:fd}).then(res => res.json());
    console.log(req_fetch);
    const  profile=req_fetch.profile;
    return`
    <div class="w-100 d-flex p-0 bg-white shadow-sm mb-4">
    <div class="container p-0">
        <div class="position-relative">
            <div class="overflow-hidden shadow-sm" style="height: 150px;">
                <img src="https://picsum.photos/id/10/1200/400" class="w-100 h-100 object-fit-cover" alt="Cover">
            </div>
            <div class="position-absolute bottom-0 start-0 end-0 d-flex justify-content-center  d-md-block px-5" style="top:30px">
                <div class="position-relative">
                    <img src="/uploads/${profile.profile_pic}" class="rounded-circle border border-4 border-white shadow" width="168" height="168">
                    <button class="btn btn-light btn-sm rounded-circle position-absolute bottom-0 end-0 border shadow-sm">
                        <i class="ri-camera-fill"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center justify-content-md-start pt-2 pb-3 mt-md-5 mt-5">
            <div class="col-md-8 ps-md-5 text-center text-md-start">
                <h1 class="fw-bold mb-0 mt-2 mt-md-0 text-capitalize">${profile.fn} ${profile.ln}</h1>
                <p class="text-muted fw-bold">${req_fetch.total_friends} Friends • <span class="text-primary">${req_fetch.mutual_friends} Mutual</span></p>
                <div class="d-flex justify-content-center justify-content-md-start mb-3 mb-md-0">
                    <div class="avatar-group d-flex">
                        <img src="https://i.pravatar.cc/40?img=1" class="rounded-circle border border-2 border-white ms-n2" width="32">
                        <img src="https://i.pravatar.cc/40?img=2" class="rounded-circle border border-2 border-white ms-n2" width="32">
                        <img src="https://i.pravatar.cc/40?img=3" class="rounded-circle border border-2 border-white ms-n2" width="32">
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-center justify-content-md-end gap-2">
                <button class="btn btn-primary px-3 fw-bold"><i class="bi bi-plus-lg"></i> Add to Story</button>
                <button class="btn btn-light px-3 fw-bold"><i class="bi bi-pencil-fill"></i> Edit Profile</button>
            </div>
        </div>
        <hr class="my-0">
        <ul class="nav nav-pills nav-fill d-flex justify-content-start gap-2 py-1">
            <li class="nav-item"><a class="nav-link  fw-bold border-0">Posts</a></li>
            <li class="nav-item"><a class="nav-link text-muted fw-bold" href="#">About</a></li>
            <li class="nav-item"><a class="nav-link text-muted fw-bold d-md-block" href="#">Friends</a></li>
            <li class="nav-item"><a class="nav-link text-muted fw-bold d-md-block" href="#">Photos</a></li>
        </ul>
    </div>
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Intro</h5>
                    <p class="text-center small">Digital Creator & UI/UX Enthusiast. Living life one pixel at a time. 🎨</p>
                    <button class="btn btn-light w-100 fw-bold mb-3">Edit Bio</button>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><i class="bi bi-briefcase text-muted me-2"></i> Works at <b>Dev Agency</b></li>
                        <li class="mb-2"><i class="bi bi-mortarboard text-muted me-2"></i> Studied at <b>Design University</b></li>
                        <li class="mb-2"><i class="bi bi-geo-alt text-muted me-2"></i> Lives in <b>Lagos, Nigeria</b></li>
                        <li class="mb-0"><i class="bi bi-rss text-muted me-2"></i> Followed by <b>845 people</b></li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0">Friends</h5>
                            <span class="text-muted small">1,240 friends</span>
                        </div>
                        <a href="#" class="text-decoration-none small">See all friends</a>
                    </div>
                    <div class="row g-2">
                        <div class="col-4 text-center">
                            <img src="https://i.pravatar.cc/100?img=5" class="rounded-3 w-100 mb-1">
                            <span class="small fw-bold d-block text-truncate">James</span>
                        </div>
                        <div class="col-4 text-center">
                            <img src="https://i.pravatar.cc/100?img=6" class="rounded-3 w-100 mb-1">
                            <span class="small fw-bold d-block text-truncate">Sara</span>
                        </div>
                        <div class="col-4 text-center">
                            <img src="https://i.pravatar.cc/100?img=7" class="rounded-3 w-100 mb-1">
                            <span class="small fw-bold d-block text-truncate">Michael</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="https://i.pravatar.cc/168" class="rounded-circle me-2" width="40" height="40">
                        <button onclick="window.location.href='create-post.php'" class="btn btn-light rounded-pill flex-grow-1 text-start text-muted ps-3 border">
                            What's on your mind?
                        </button>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-around">
                        <button class="btn btn-light border-0 fw-bold flex-grow-1 mx-1"><i class="bi bi-camera-reels text-danger"></i> Reel</button>
                        <button class="btn btn-light border-0 fw-bold flex-grow-1 mx-1"><i class="bi bi-images text-success"></i> Photo</button>
                    </div>
                </div>
            </div>

            <div id="user-posts-container">
                </div>
        </div>
    </div>
</div>`
}