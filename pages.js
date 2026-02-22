import createPost from "./page/create-post.js";
import feeder from "./page/feeds.js";
import { friends, people_you_may_know } from "./page/friends.js";
import profile from "./page/profile.js";
    function createpage(page,data){
        document.getElementById('roots').innerHTML +=`
            <div id="page-${page}" class="w-100 h-100  bg-light overflow-auto flex-shrink-0" style="scroll-snap-align: center;">${data}</div>
        `;
    }
export const loader_board =window.location.protocol + '//' + window.location.host + '/data.php';

    window.addEventListener('DOMContentLoaded',async()=>{
        const formdata=new FormData();
        formdata.append('action','feeds');
        const getdata=await fetch(loader_board, {method:'post', body:formdata}).then(e => e.text());
        
        var mainDataREQs=JSON.parse(getdata);
        console.log(mainDataREQs);
        const mainDataREQ=mainDataREQs.feeds;
        console.log(mainDataREQ);

        createpage('home',await feeder(mainDataREQ));
        createpage('createPost', createPost())
        createpage('profile',await profile());
        createpage('profiles',await profile());
        createpage('friends',await friends());
// ==============profile page===================================
         router(window.location.pathname, window.location.search);
        document.getElementById('preloader-loading').classList.toggle('d-none');
    });

    



  window.router = (path, querystring = '') => {
     path=path.split('/').pop();
    //  path='/main/'+path.split('/').pop();
    const current_route = localStorage.getItem('url');
    if (current_route === path) {
      history.replaceState({ path }, '', path + querystring);
    } else {
      history.pushState({ path }, '',  path + querystring);
      localStorage.setItem('url', path);
    }
    pageLoader(path, querystring);
  }

window.onpopstate = () => {
  router(window.location.pathname, window.location.search);
};

async function pageLoader(path, querystring = '') {
    path=path.split('/').pop();
    const params = new URLSearchParams(querystring);
    switch (path) {
        case '':
        case 'home':
        case 'feeds':
        case 'member':
             document.getElementById('page-home').scrollIntoView()
            break;

        case 'create-post':
           document.getElementById('page-createPost').scrollIntoView()
        break;

        case 'profile':
             document.getElementById('page-profile').scrollIntoView()
        break;

        case 'profiles':
             document.getElementById('page-profiles').innerHTML=await profile()
             document.getElementById('page-profiles').scrollIntoView()
        break;

        case 'friends':
            document.getElementById('page-friends').scrollIntoView()
            const container = document.getElementById("loader-data-friends-add");
            container.innerHTML = Array.from({ length: 12 }).map(() => `
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
            `).join('');

            switch (params.get('type') ?? '') {
              case '':
                await myfriendlist()
                break;
                
              case 'people_you_may_know':
                await people_you_may_know()
                break;

              case 'people_you_may_know':
                await people_you_may_know()
                break;
            
              default:
                //  document.getElementById('page-friends').innerHTML=await profile()
                document.getElementById('page-friends').scrollIntoView()
                await people_you_may_know()
                break;
            }
          break;


        
        case 'member':
          document.getElementById('page-profile').scrollIntoView()
        break;
    
        default:
          
            break;
    }
//    console.log('Loading page:', path, params.get('u'));
}
// Attach a click listener to the whole document
document.addEventListener('click', e => {
  const target = e.target.closest('[data-href]');
  if (target) {
    const hrefValue = target.getAttribute('data-href');
    const url = new URL(hrefValue, window.location.origin);
    const params = url.searchParams;
    console.log('Clicked data-href:', params.get('u'), url.search, url.pathname);
    router(url.pathname,url.search);
  }
});



