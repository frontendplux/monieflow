import { Link } from "react-router-dom";
import Headerx from "../../compo/header";
import Sidebar from "../../compo/sidebar";


export default function Profile(){
    return(
        <div className="position-fixed h-100 w-100 overflow-auto bg-light">
           <Headerx />
           <div className="bg-white">
                <div className="container bg-white position-relative" id="rooter-main">
                <img src="/assets/image/h.webp" style={{height:'250px',objectFit:'cover'}} className="w-100" alt="" />
                <button className="btn btn-light me-4 border end-0 position-absolute m-3 gap-2 d-flex fw-bold bottom-0 px-4">
                    <span className="ri-camera-fill"></span>
                    <span className="">Edit cover photos</span>
                </button>
            </div>
            <div className="container bg-white d-flex my-4 d-flex gap-2">
                <div><img src="/assets/image/h.webp" style={{borderRadius:"50%", height:"100px",width:"100px"}} alt="" /></div>
                <div className="w-100">
                    <div className="d-flex justify-content-between w-100">
                        <div>
                            <Link to="" className="fw-bold fs-3 text-dark text-decoration-none">Blossom</Link>
                        </div>
                        <div className="d-flex gap-3">
                            <button className="btn btn-primary border px-3">
                                <span className="ri-add-fill">Add to story </span>
                            </button>
                            <button className="border">
                                <span className="ri-pencil-fill"> Edit profile</span>
                            </button>
                            <button className="btn btn-toggle"></button>
                        </div>
                    </div>
                    <div className="d-flex gap-4">
                        <Link className="d-flex gap-2  text-decoration-none fw-bold"><span>followers</span><span> 20.k</span></Link>
                        <Link className="d-flex gap-2 text-decoration-none fw-bold"><span>following</span><span> 20.k</span></Link>
                    </div>
                    <div>Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe, magnam!</div>
                    <div><span className="ri-map-2-fill"></span> <span>nigeria</span></div>
                </div>
            </div>
           </div>
        </div>
    )
}