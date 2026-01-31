import { Outlet } from "react-router-dom";
import NotFound from "../auth/404";
import Headerx from "../compo/header";
import Sidebar from "../compo/sidebar";

export default function Member(){
    return(
        <div className="position-fixed h-100 w-100 overflow-auto bg-light">
           <Headerx />
           <div className="container d-flex" id="rooter-main">
                <div className="col-3 p-2">
                    <Sidebar />
                </div>
                <div id="rooter-main-1" className="w-100 d-flex">
                    <Outlet />
                </div>
           </div>
        </div>
    )
}

