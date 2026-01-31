import { Link } from "react-router-dom";
import NotFound from "./404";

export default function Page404(){
    return(
        <>
        <header style={{backgroundImage:"linear-gradient(#648be8, #254c94)"}}>
                <div className="d-flex justify-content-between align-items-center col-8 mx-auto py-3">
                    <h1 className="text-white m-0 fw-bolder d-flex gap-2">
                        <span className="">monieflow</span> 
                        <Link to='/signup' className="fw-medium align-self-start btn btn-success">sign up</Link>
                    </h1>
                    <div className="d-flex gap-2 align-items-center">
                        <Link className="text-decoration-none text-white" to="/signup">Join or log in to Monieflow</Link>
                        <button type="button" style={{background:"none"}} class="border-0 text-white fs-4 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <div className="p-2">
                                <div>Email or phone</div>
                                <div><input type="text" className="form-control" /></div>
                                <div>Password</div>
                                <div><input type="password" className="form-control" /></div>
                                <div className="text-end"><Link to="/forgot-password" className="text-end" style={{fontSize:"small"}}>Forget Password?</Link></div>
                                <div className="text-center  mt-2"><button className="btn rounded-0 w-100 btn-primary px-4">Log In</button></div>
                                <div className="py-3 bg-white pb-2">
                                    <div className="text-center text-muted" style={{fontSize:"small"}}>Do you want to join Facebook?</div>
                                    <p className="text-center mt-2">
                                        <Link className="btn-success btn  px-5" to="/signup">sign up</Link>
                                    </p>
                                </div>
                            </div>
                        </ul>
                    </div>
                </div>
            </header>
            <NotFound />
        </>
            

    )
}