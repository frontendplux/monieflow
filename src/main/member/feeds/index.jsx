export default function FeedsIndex(){
    return(
        <div className="p-2 bg-white mb-2 rounded">
            <div className="d-flex gap-2">
                <img src="/assets/image/h.webp" style={{height:"50px", width:"50px", borderRadius:"50%"}} alt="profile" />
                <button 
                    className="form-control rounded-pill text-start bg-light border-0"
                    data-bs-toggle="modal" 
                    data-bs-target="#createPostModal"
                >
                    What's on your mind, Blossom?
                </button>
            </div> 
    )
}