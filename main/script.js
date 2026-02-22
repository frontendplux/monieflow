import { create_post } from "../pages/feeds.js";
import LoadAllPage from "./waget.js";
// LoadAllPage();
window.addEventListener('DOMContentLoaded', () => {
  document.getElementById('preloader-loading').classList.toggle('d-none');
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
  };
  router(window.location.pathname, window.location.search);
  });
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
              document.querySelector('.main-div-for-root-per-page  #main-feeds').scrollIntoView(true,'smooth');
              break;

          case 'create-feed':
            document.querySelector('.main-div-for-root-per-page #main-feed-post').scrollIntoView(true,'smooth')
          break;

          case 'create-reels':
            document.querySelector('.main-div-for-root-per-page #main-feed-reel-post').scrollIntoView(true,'smooth')
          break;

          
          case 'profile':
            document.querySelector('.main-div-for-root-per-page #main-profile').scrollIntoView(true,'smooth')
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


