async function getfileItem(path) {return await fetch(path).then(res => res.text());}

async function postfileItem(path, data) {
  return await fetch(path, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  }).then(res => res.json());
}

/* ROUTER */
const router = (url) => {
  const parsedUrl = new URL(url, window.location.origin);
  const path = parsedUrl.pathname.replace('/', '');
  const query = parsedUrl.searchParams;
  history.pushState({ path }, '', parsedUrl.pathname + parsedUrl.search);
  loadServer(path, query);
  bindLinks();
};

/* LOAD PAGE */
async function loadServer(path, query) {
  const roots = document.getElementById('roots');

  switch (path) {
    case '':
    case 'home':
      roots.innerHTML += await getfileItem('/pages/explore.php');
        bindLinks();
      break;

    case 'profile':
      roots.innerHTML = await getfileItem('/pages/profile.php');
      break;

    case 'profile-users': {
      const user = query.get('user') ?? '';
      roots.innerHTML = await getfileItem(`/pages/profile-2.php?user=${user}`);
      break;
    }

    default:
      roots.innerHTML = await getfileItem('/pages/404.php');
      break;
  }
}

/* HANDLE <a> CLICKS */
function bindLinks() {
  document.querySelectorAll('a[href]').forEach(a => {
    // ignore external or special links
    if (
      a.target === '_blank' ||
      a.href.startsWith('javascript:') ||
      a.href.startsWith('mailto:') ||
      a.href.startsWith('#')
    ) return;

    a.onclick = (e) => {
      e.preventDefault();
      router(a.href);
    };
  });
}

/* INITIAL LOAD */
window.addEventListener('DOMContentLoaded', () => {
  router(window.location.pathname + window.location.search);
});

/* BACK / FORWARD BUTTON SUPPORT */
window.addEventListener('popstate', () => {
  router(window.location.pathname + window.location.search);
});


// ------------------------------------------------------------------------------------------------