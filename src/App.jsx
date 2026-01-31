import { Routes, Route } from "react-router-dom";
import Login from "./main/auth/login";
import ForgetPassword from "./main/auth/forget-password";
import Signup from "./main/auth/signup";
import CreatePage from "./main/auth/create-page";
import Member from "./main/member";
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'remixicon/fonts/remixicon.css'
import NotFound from "./main/auth/404";
import Page404 from "./main/auth/page-404";
import Home, { HomeSidemenu } from "./main/member/landing";
import Comment from "./main/member/landing/comment";
import Profile from "./main/member/profiles";
import Createfeeds from "./main/member/feeds/create";
const server='http://localhost:3000';
function App() {
  return (
    <Routes>
      {/* AUTH ROUTES (no layout) */}
      <Route path="/" element={<Login server={server} />} />
      <Route path="/forget-password" element={<ForgetPassword  />} />
      <Route path="/signup" element={<Signup />} />
      <Route path="/create-page" element={<CreatePage />} />
      <Route path="/profile" element={<Profile />} />
      <Route path="/create" element={<Createfeeds />} />

      {/* MEMBER LAYOUT ROUTE */}
      <Route path="/member/" element={<Member />}>
        <Route path="" element={<Home />}>
            <Route path="" element={<HomeSidemenu />} />
        </Route>
        <Route path="home/" element={<Home />}>
          <Route path="" element={<HomeSidemenu />} />
          <Route path="comment" element={<Comment />} />
          <Route path="*" element={<NotFound />} />
        </Route>
        <Route path="*" element={<NotFound />} />

      </Route>

      
      <Route path="*" element={<Page404 />} />
    </Routes>
  );
}

export default App;
