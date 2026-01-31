export default function Comment(){
    return(
        <div className="d-flex justify-content-end py-1">
            <div className="col-10">
                <div className="bg-white p-2 fw-medium">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quo, voluptatum?</div>
                <div className="d-flex gap-3">
                    <span className="ri-heart-fill"> <span>.20</span> <span className="text-dark fw-bold">like</span></span>
                    <span className="ri-message-3-fill"> <span>.20</span> <span className="text-dark fw-bold">comment</span></span>
                </div>
            </div>
        </div>
    )
}