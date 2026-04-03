import Assets from "/pages/gssets.js";
import Login, { ForgotPassword, isloggedin, SignupPage, visibilityToggle } from "/pages/auth.js";
import Bank from "/pages/bank.js";
import Chat from "/pages/chat.js";
import Comments from "/pages/comment.js";
import { CreateFeedPageTemplate, CreateFeedPageTemplateFunction } from "/pages/create.js";
import Home from "/pages/feeds.js";
import Notifications from "/pages/notify.js";
import Profile, { EditProfile } from "/pages/Profile.js";
import Connections from "/pages/squard.js";
import Trading, { cartjsfunction } from "/pages/trade.js";
import Transfer, { TransactionStatus } from "/pages/transfer.js";

window.router = (path) => {
    const currentRoute = window.location.pathname;

    if (currentRoute === path) {
        // Replace if same route
        history.replaceState({}, "", path + window.location.search);
    } else {
        // Push new route
        history.pushState({}, "", path + window.location.search);
    }

    handleRouteChange(path);
};

export default router;


async function handleRouteChange(path) {
    const app= document.getElementById("app");
    switch (path) {
        case "/":
        case "/login":
            app.innerHTML =await Login(); // Assuming homePageContent is a function that returns HTML for the home page
            visibilityToggle();
            break;

        case '/register':
            app.innerHTML=await SignupPage()
             visibilityToggle();
            break;

        case "/forgot-password":
                app.innerHTML =await ForgotPassword(); // Assuming homePageContent is a function that returns HTML for the home page
            break;

        case "/index.html":
        case "/home":
            app.innerHTML =await Home(); // Assuming homePageContent is a function that returns HTML for the home page
            break;
        case "/comments":
            app.innerHTML=await Comments();
            break;
        case "/trade":
           app.innerHTML=await Trading();
           cartjsfunction();
            break;
        case "/assets":
            app.innerHTML=await Assets();
            break;  

        case "/bank":
            app.innerHTML=await Bank();
            break;
        case "/squads":
            app.innerHTML=await Connections();
            break;

        case "/create-post":
            app.innerHTML = await CreateFeedPageTemplate();
            CreateFeedPageTemplateFunction();
            break;

        case "/alerts":
            app.innerHTML = await Notifications();
            break;

       case "/profile":
            app.innerHTML = await Profile();
            break;

        case "/profile-edit":
            app.innerHTML = await EditProfile();
            break;
        
        case "/chat":
            app.innerHTML = await Chat();
            break;
        case "/transfer":
            app.innerHTML = await Transfer();
            break;

        case "/transfer-status":
            app.innerHTML = await TransactionStatus();
            break;
        
        default:
            console.log("Page not found");
            break;
    }
}