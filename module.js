import router from "./helperJs/helperRouter.js";
import helperRouter from "./helperJs/helperRouter.js";

window.onload = function() {
    const path = window.location.pathname;
    helperRouter(path);
}

window.onpopstate = function() {
    const path = window.location.pathname;
    helperRouter(path);
}

document.addEventListener("click", (e) => {
    const link = e.target.closest("[data-link]"); // 🔥 better than matches
    if (link) {
        e.preventDefault();
        const url = new URL(link.href); // convert to URL object
        helperRouter(url.pathname);
    }
});