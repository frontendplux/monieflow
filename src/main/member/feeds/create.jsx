import Headerx from "../../compo/header";

export default function Createfeeds(){
    return(
        <div className="position-fixed h-100 w-100 overflow-auto bg-light">
            <Headerx />
            <div className="bg-white rounded-3 shadow-sm col-6 mx-auto">
                <div className="d-flex gap-2 p-2   my-4">
                    <img src="/assets/image/h.webp" style={{height:"50px", width:"50px", borderRadius:"50%"}} alt="profile" />
                    <div className="w-100">
                        <textarea name="postContent" id="postContent" className="form-control border-0 bg-light rounded-3" placeholder="What's on your mind, Blossom?"></textarea>
                        <div className="d-flex justify-content-end my-2 d-flex gap-2">
                            <button className="btn btn-primary text-capitalize btn-danger ">create products</button>
                            <button className="btn btn-primary text-capitalize">upload Post</button>
                        </div>
                    </div>
                </div> 
            </div>
            <div>
            </div>
        </div>
    )
}