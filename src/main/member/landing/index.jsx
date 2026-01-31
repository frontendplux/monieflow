import React, { useEffect, useState } from "react"; // Added useEffect and useState
import { Link, Outlet } from "react-router-dom";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { Navigation, Pagination, Autoplay } from "swiper/modules";

export default function Home() {
    // State to handle image previews in the modal
    const [uploadImages, setUploadImages] = useState([]);

    useEffect(() => {
        const myModal = document.getElementById('exampleModal');
        const myInput = document.getElementById('myInput');

        if (myModal && myInput) {
            const focusInput = () => myInput.focus();
            myModal.addEventListener('shown.bs.modal', focusInput);
            return () => myModal.removeEventListener('shown.bs.modal', focusInput);
        }
    }, []);

    const handleFileChange = (e) => {
        if (e.target.files) {
            const filesArray = Array.from(e.target.files).map(file => URL.createObjectURL(file));
            setUploadImages(filesArray);
        }
    };

    // Example feed data (You can replace this with your real data)
    const feedImages = ["/assets/image/h.webp"]; 

    return (
        <div className="w-100 d-flex">
            <div className="w-100">
                {/* POST INPUT AREA */}
                <div className="w-100 px-3 rounded-2 my-3 d-flex align-items-center shadow bg-white">
                    <div className="w-100 d-flex gap-2 align-items-center p-2 rounded ">
                        <Link to="/profile">
                            <img className="rounded-circle" style={{ width: "40px", height: "40px" }} src="/assets/image/biz_illustration.png" alt="" />
                        </Link>
                        <div className="w-100">
                            <Link 
                                to="#" 
                                data-bs-toggle="modal" 
                                data-bs-target="#exampleModal" 
                                className="w-100 fw-medium px-3 d-block text-decoration-none text-secondary p-2 rounded-pill bg-light"
                            >
                                What is on your mind Samuel?
                            </Link>
                        </div>
                    </div>
                    <div className="d-flex gap-2 align-items-center">
                        <Link style={{ height: '40px', width: '40px' }} className="rounded-circle btn p-2 btn-light d-flex justify-content-center align-items-center fs-3"><i className="ri-live-fill"></i></Link>
                        <Link style={{ height: '40px', width: '40px' }} className="rounded-circle btn p-2 btn-light d-flex justify-content-center align-items-center fs-3"><i className="ri-multi-image-fill"></i></Link>
                        <Link style={{ height: '40px', width: '40px' }} className="rounded-circle btn p-2 btn-light d-flex justify-content-center align-items-center fs-3"><i className="ri-emotion-laugh-fill"></i></Link>
                    </div>
                </div>

                {/* MODAL FOR UPLOADING FEED */}
                <div className="modal fade" id="exampleModal" tabIndex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content">
                            <div className="modal-header border-0">
                                <h5 className="modal-title fw-bold w-100 text-center" id="exampleModalLabel">Create Post</h5>
                                <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div className="modal-body">
                                <textarea id="myInput" className="form-control border-0 shadow-none fs-5" rows="3" placeholder="What is on your mind Samuel?"></textarea>
                                
                                {/* Dynamic Preview Grid inside Modal */}
                                {uploadImages.length > 0 && (
                                    <div className="d-grid gap-1 my-2" style={{ gridTemplateColumns: uploadImages.length > 1 ? '1fr 1fr' : '1fr' }}>
                                        {uploadImages.map((img, i) => (
                                            <img key={i} src={img} className="w-100 rounded" alt="upload-preview" />
                                        ))}
                                    </div>
                                )}

                                <div className="d-flex justify-content-between align-items-center border rounded-3 p-3 mt-3">
                                    <span className="fw-bold">Add to your post</span>
                                    <label htmlFor="modal-upload" style={{ cursor: 'pointer' }}>
                                        <i className="ri-multi-image-fill fs-3 text-success"></i>
                                        <input type="file" id="modal-upload" className="d-none" multiple accept="image/*" onChange={handleFileChange} />
                                    </label>
                                </div>
                            </div>
                            <div className="p-3">
                                <button type="button" className="btn btn-primary w-100 fw-bold">Post</button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* STORIES SWIPER */}
                <Swiper
                    modules={[Navigation, Pagination, Autoplay]}
                    className="col-12 d-none"
                    spaceBetween={5}
                    slidesPerView={4}
                    breakpoints={{
                        640: { slidesPerView: 2 },
                        1024: { slidesPerView: 3 }
                    }}
                    navigation
                    pagination={{ clickable: true }}
                >
                    {[
                        { name: "samuel", img: "/assets/image/biz_illustration.png" },
                        { name: "john", img: "/assets/image/community_illustration.png" },
                        { name: "doe", img: "/assets/image/biz_illustration.png" },
                    ].map((item, i) => (
                        <SwiperSlide key={i} className="w-100">
                            <div className="w-100 d-flex flex-column align-items-center">
                                <img className="rounded-circle" style={{ width: "100%", height: "200px" }} src={item.img} alt={item.name} />
                                <span className="fw-medium">{item.name}</span>
                            </div>
                        </SwiperSlide>
                    ))}
                </Swiper>

                {/* FEED POST */}
                <div className=" bg-white rounded my-3 rounded-3 shadow p-2">
                    <div className="d-flex gap-2 align-items-center p-2 ">
                        <div className="">
                            <img style={{ width: "60px", height: "60px" }} src="/assets/image/biz_illustration.png" alt="" />
                        </div>
                        <div className="w-100 d-flex justify-content-between align-items-center">
                            <div className="">
                                <div className="fw-bolder text-capitalize">samuel</div>
                                <div className="text-muted small">7 minutes ago</div>
                            </div>
                            <div className="d-flex gap-2 align-items-center">
                                <a href="#" style={{ width: "40px", height: "40px" }} className="btn btn-light d-flex justify-content-center align-items-center rounded-circle fs-4"><i className="ri-more-fill"></i></a>
                                <a href="#" style={{ width: "40px", height: "40px" }} className="btn btn-light d-flex justify-content-center align-items-center rounded-circle fs-4"><i className="ri-close-large-fill"></i></a>
                            </div>
                        </div>
                    </div>
                    <div className="p-2 fw-medium">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Optio, neque!</div>
                    
                    {/* DYNAMIC GRID SYSTEM */}
                    <div className="container d-grid gap-1 px-1" 
                         style={{ gridTemplateColumns: feedImages.length > 1 ? "repeat(2, 1fr)" : "1fr" }}>
                        {feedImages.map((item, i) => (
                            <img key={i} src={item} className="w-100 rounded" alt="" />
                        ))}
                    </div>

                    <div className="text-end p-2 text-muted small">
                        likes <span>120</span> • comments <span>45</span> • shares <span>10</span>
                    </div>
                    <hr className="my-1 opacity-25" />
                    <div className="d-flex align-items-center p-2 gap-3">
                        <a href="#" className="text-decoration-none text-secondary"><i className="ri-heart-line"></i> Like</a>
                        <Link to="/member/home/comment?u=31" className="text-decoration-none text-secondary"><i className="ri-message-2-line"></i> Comment</Link>
                        <a href="#" className="text-decoration-none text-secondary"><i className="ri-share-line"></i> Share</a>
                    </div>
                </div>
            </div>
            
            {/* RIGHT SIDE OUTLET */}
            <div className="col-5 px-3 d-none d-lg-block"> 
                <Outlet />
            </div>
        </div>
    );
}

export function HomeSidemenu() {
    return (
        <div className="p-3 pt-0 rounded mt-3 w-100">
            <h5 className="fw-medium text-capitalize">Sponsored</h5>
            <Link to="#" className="d-flex text-decoration-none text-dark gap-2">
                <div className="col-5"><img src="/assets/image/h.webp" className="rounded" style={{ width: "100%" }} alt="" /></div>
                <div>
                    <h2 className="fs-4">samuel </h2>
                    <div className="line-clamp-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Est, adipisci.</div>
                </div>
            </Link>
            <hr className="border-2" />
            <h5 className="text-capitalize text-secondary">contacts</h5>
            <div>
                <Link to="#" className="d-flex gap-2 text-muted align-items-center text-decoration-none">
                    <div><img src="/assets/image/h.webp" className="rounded-circle" style={{ width: "60px", height: "60px" }} alt="" /></div>
                    <div className="fw-bold ">Lorem, ipsum.</div>
                </Link>
            </div>
        </div>
    );
}