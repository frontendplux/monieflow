import { backend_url, header } from "../waget.js";

export default async function feeds(req,pro){
    var user_profile=JSON.parse(pro.profile)
    const container=document.querySelector('.main-div-for-root-per-page  #main-feeds');
    container.innerHTML=`
                        ${header({page:"home",data:pro})}
                        <div class="container my-3 d-flex px-0">

                        <!----------------------------------------------------------------------------------------------!>

                            <div class="col-lg-3 d-md-block d-none" id='container_RTL'>
                                <a href="/profile/" class="sidebar-link">
                                    <img src="" class="rounded-circle me-3" width="36" height="36" style="object-fit: cover;">
                                    <span><?= $profile['username'] ?></span>
                                </a>
                                ${[['friends','ri-group-fill','#2833ac'],['memories','ri-history-line', '#0dcaf0'],['saved','ri-bookmark-fill', '#a855f7'],['pages','ri-flag-fill','#f97316'],['events','ri-calendar-event-fill','#801d31']].map(e=>{ return`
                                    <span onclick="" data-menu-href="${e[0]}" class="sidebar-link d-block p-2">
                                    <i class="${e[1]} fs-5" style="color:${e[2]}"></i>
                                    ${e[0]}</span>`}).join('')}
                                <hr>
                                <h6 class="px-3 text-secondary">Shortcuts</h6>
                                ${[['Gaming Hub','ri-group-fill', '']].map(e=>{return `<span data-href="${e[0]}" class="sidebar-link"><i class="${e[1]}" ></i> ${e[0]}</span>`}).join('')}
                            </div>

                        <!----------------------------------------------------------------------------------------------!>

                          <div class="col-12 col-md-10 col-lg-6 mx-auto p-2 d-flex justify-content-center">
                            <div style="width: 100%; max-width: 590px;" id="feeds-main-update-div">
                                <!-- Stories/Reels Section (Horizontal Scroll) -->
                                <div class="container px-0">
                                    <div id="reelifield" class="d-flex gap-3 pb-3 overflow-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
                                        <a href="/feeds/reel/create.php"  class="card text-decoration-none bg-white border-0 shadow-sm flex-shrink-0" style="width: 130px; border-radius: 15px; overflow: hidden;">
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
                                        ${req.reels.map(e=>{
                                            var profile=JSON.parse(e.profile);
                                            var datas=JSON.parse(e.data);
                                            return`
                                        

                                            <a data-href="reels?u=${e.feed_id}" class="position-relative flex-shrink-0 shadow-sm user-reel" style="width: 130px; height: 210px; border-radius: 15px; overflow: hidden;">
                                                <div class="h-100 w-100" style="background: url('/uploads/reel_covers/${datas.cover} ) center/cover;"></div>
                                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-2" 
                                                    style="background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.8) 100%);">
                                                    <div class="avatar-ring">
                                                        <img src="/uploads/${profile.profile_pic}" class="rounded-circle border border-2 border-primary" width="35" height="35">
                                                    </div>
                                                    <div class="blur-footer rounded-2 py-1 px-2">
                                                        <div class="text-white small fw-semibold text-truncate" style="font-size: 0.75rem;">${profile['username']}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>`}).join('')}
                                    </div>
                                </div>
                                <div class="mb-3 bg-white rounded-3">
                                    <div class="d-flex align-items-center p-2">
                                        <span data-href="profile?u=${pro.id}"><img src="/uploads/${user_profile.profile_pic}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;" /></span>
                                        <span data-href="create-feed" class="composer-input text-decoration-none bg-light w-100 rounded-pill p-2">
                                            What's on your mind, ${user_profile.username} ?
                                        </span>
                                    </div>
                                    <hr class="my-2 border">
                                    <div class="d-flex justify-content-around">
                                        <span data-href="create-reels"  class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center"><i class="ri-video-add-fill text-danger me-2"></i> Reels</span>
                                        <span data-href="create-feed" class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center" onclick="document.getElementById('image-open').click()"><i class="ri-image-2-fill text-success me-2"></i> Photo</span>
                                        <span data-href="create-music" class="btn btn-link text-decoration-none text-secondary fw-bold flex-grow-1 py-2 rounded sidebar-link border-0 justify-content-center"><i class="ri-music-fill text-warning me-2"></i> Music</span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <!----------------------------------------------------------------------------------------------!>
                          <div class="col-lg-3 d-none d-md-block" id="container_LTR">
                            <h6 class="text-secondary fs-5  px-2 mb-3 d-flex justify-content-between">
                                <span>Contacts</span>
                                <div class="d-flex gap-3 ">
                                    <i class="ri-video-add-fill"></i>
                                    <i class="ri-search-line"></i>
                                    <i class="ri-more-fill"></i>
                                </div>
                            </h6>
                            <span data-href="contact?" class="d-block p-2"><img src="https://i.pravatar.cc/150?u=9" class="rounded-circle me-3" width="32" height="32" style="object-fit: cover;"> Sarah Stone</span>
                          </div>
                        </div>
                        `
}







export function create_post(){
    window.uploadfile=()=>{

    }
    var container=document.querySelector('.main-div-for-root-per-page #main-feed-post');
    var html=`
    <header class="bg-white py-3">
        <div class="d-flex justify-content-between align-items-center container">
            <span data-href='home' class="ri-arrow-left-s-fill"> Return</span>
            <span data-post='create-post' class="btn btn-light text-capitalize fw-medium rounded-pill">create post</span>
        </div>
    </header>
    <div class="container my-2">
        <div  class="w-100 bg-white m-auto p-3 rounded-3">
           <textarea placeholder="What is on your mind ?" class="form-control mb-3"></textarea>
           <div class="d-flex gap-3">
                <span class="d-block fw-bold text-warning ri-video-fill btn btn-light rounded-pill"> video</span>
                <span class="d-block fw-bold text-info ri-music-fill btn btn-light rounded-pill"> audio</span>
                <span class="d-block fw-bold text-muted ri-image-fill btn btn-light rounded-pill"> image</span>
           </div>
        </div>
    </div>
    `
    droppySammy('info', 'Auth Error',"Password must be at least 6 characters");
    container.innerHTML=html
}