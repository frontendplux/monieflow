import { useState } from "react";
import { Link } from "react-router-dom";

export default function Headerx() {
    
    const [activeIndex, setActiveIndex] = useState(null);
    
    // --- New State for Uploading ---
    const [selectedImages, setSelectedImages] = useState([]);

    const handleImageChange = (e) => {
        if (e.target.files) {
            const filesArray = Array.from(e.target.files).map((file) =>
                URL.createObjectURL(file)
            );
            setSelectedImages(filesArray);
        }
    };

    return (
        <header className="bg-white sticky-top" style={{ zIndex: "9999" }}>
            <div className="bg-white d-flex justify-content-between align-items-center container">
                <div className="d-flex gap-3 align-items-center w-25">
                    <h1 className="fw-bold bg-primary d-flex m-0 justify-content-center text-white rounded align-items-center fs-2" style={{ width: "60px", height: "60px", maxHeight: 'max-content' }}><del>M</del></h1>
                    <div className="w-100 position-relative">
                        <span className="ri-search-line position-absolute top-0 m-3 mt-1 text-muted fs-4"></span>
                        <input type="search" placeholder="search monieflow" className="rounded-pill ps-5 fs-6  bg-light form-control" />
                    </div>
                </div>

                <div className="fs-3 d-flex">
                    {[
                        ['ri-home-line', '/home', 'ri-home-fill'],
                        ['ri-group-line', '/group', 'ri-group-fill'],
                        ['ri-movie-2-ai-line', '/movies', 'ri-movie-2-fill'],
                        ['ri-store-3-line', '/store', 'ri-store-3-fill'],
                        ['ri-group-2-line', '/community', 'ri-group-2-fill']
                    ].map((item, index) => (
                        <Link
                            key={index}
                            to={"/member" + item[1]}
                            onClick={() => setActiveIndex(index)}
                            className={`px-5 text-dark text-decoration-none fs-4 py-3 ${activeIndex === index ? 'active-nav-header border-bottom border-primary border-3' : ''}`}
                        >
                            <span className={activeIndex === index ? item[2] : item[0]}></span>
                        </Link>
                    ))}
                </div>

                <div className="d-flex gap-2 align-items-center">
                    <a href="" className="btn btn-light d-flex justify-content-center align-items-center rounded-circle fs-4" style={{ height: '50px', width: '50px' }}><span className="ri-grid-fill"></span></a>
                    <a href="" className="btn btn-light d-flex justify-content-center align-items-center rounded-circle fs-4" style={{ height: '50px', width: '50px' }}><span className="ri-mail-unread-fill"></span></a>
                    <a href="" className="btn btn-light d-flex justify-content-center align-items-center rounded-circle fs-4" style={{ height: '50px', width: '50px' }}><span className="ri-notification-3-fill"></span></a>
                    <Link to="/profile" className="btn btn-light d-flex justify-content-center align-items-center rounded-circle fs-4" style={{ height: '50px', width: '50px' }}><img style={{ width: "48px", height: "48px", }} src="/assets/image/community_illustration.png" alt="" className="rounded-circle" /></Link>
                </div>
            </div>

            {/* --- UPLOAD MODAL --- */}
{/* --- UPLOAD MODAL --- */}
<div 
    className="modal fade" 
    id="exampleModal" 
    tabIndex="5" 
    aria-labelledby="exampleModalLabel" 
    aria-hidden="true" 
    style={{ zIndex: "10000000" }} // High Z-Index for the main wrapper
>
    <div className="modal-dialog modal-dialog-centered">
        <div className="modal-content border-0 shadow-lg">
            <div className="modal-header">
                <h1 className="modal-title fs-5 fw-bold text-center w-100" id="exampleModalLabel">Create Post</h1>
                <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div className="modal-body">
                {/* Text Input */}
                <textarea 
                    className="form-control border-0 shadow-none fs-5 mb-2" 
                    rows="3" 
                    placeholder="What is on your mind Samuel?"
                ></textarea>
                
                {/* Smart Grid Preview: 1 image = full, 2+ images = 2 columns */}
                {selectedImages.length > 0 && (
                    <div 
                        className="d-grid gap-1 mt-2 rounded overflow-hidden" 
                        style={{ 
                            gridTemplateColumns: selectedImages.length > 1 ? "1fr 1fr" : "1fr" 
                        }}
                    >
                        {selectedImages.map((img, i) => (
                            <img 
                                key={i} 
                                src={img} 
                                alt="preview" 
                                className="w-100" 
                                style={{ maxHeight: "300px", objectFit: "cover" }} 
                            />
                        ))}
                    </div>
                )}

                {/* Upload Trigger Area */}
                <div className="d-flex justify-content-between align-items-center mt-3 border rounded p-3 bg-light">
                    <span className="fw-bold">Add to your post</span>
                    <label htmlFor="feed-upload" className="m-0" style={{ cursor: 'pointer' }}>
                        <i className="ri-image-add-fill fs-3 text-success"></i>
                        <input 
                            type="file" 
                            id="feed-upload" 
                            multiple 
                            className="d-none" 
                            accept="image/*" 
                            onChange={handleImageChange} 
                        />
                    </label>
                </div>
            </div>

            <div className="modal-footer border-0 p-3">
                <button type="button" className="btn btn-primary w-100 fw-bold py-2">
                    Post
                </button>
            </div>
        </div>
    </div>
</div>
        </header>
    );
}