(function(){

    // Inject CSS programmatically
    const css = `
        .palert-overlay {
            position: fixed;
            top:0; left:0;
            width:100%; height:100%;
            background: rgba(0,0,0,0.55);
            display:flex;
            justify-content:center;
            align-items:center;
            z-index: 999999;
            opacity:0;
            transition: opacity .25s ease;
        }
        .palert-overlay.show { opacity:1; }

        .palert-box {
            background:white;
            padding:25px;
            border-radius:15px;
            width:85%;
            max-width:350px;
            text-align:center;
            box-shadow:0 5px 20px rgba(0,0,0,0.25);
            animation: palert-pop .25s ease;
            font-family: Arial, sans-serif;
        }

        @keyframes palert-pop {
            0% { transform: scale(0.7); opacity:0; }
            100% { transform: scale(1); opacity:1; }
        }

        .palert-title {
            font-size:20px;
            font-weight:700;
            margin-bottom:10px;
        }
        .palert-msg {
            font-size:15px;
            margin-bottom:20px;
            color:#555;
        }

        .palert-btn {
            padding:10px 18px;
            border:none;
            border-radius:8px;
            color:white;
            cursor:pointer;
            font-weight:bold;
            width:110px;
        }

        .palert-success { background:#22c55e; }
        .palert-error   { background:#ef4444; }
        .palert-warning { background:#f59e0b; }
    `;

    const styleTag = document.createElement("style");
    styleTag.textContent = css;
    document.head.appendChild(styleTag);

    // Main function
    window.showAlert = function(type, title, message, onClose=null){
        const overlay = document.createElement("div");
        overlay.className = "palert-overlay";

        const box = document.createElement("div");
        box.className = "palert-box";

        box.innerHTML = `
            <div class="palert-title">${title}</div>
            <div class="palert-msg">${message}</div>
            <button class="palert-btn palert-${type}">OK</button>
        `;

        overlay.appendChild(box);
        document.body.appendChild(overlay);

        // Fade-in
        setTimeout(()=> overlay.classList.add("show"), 20);

        // Button action
        box.querySelector("button").onclick = () => {
            overlay.classList.remove("show");
            setTimeout(()=> overlay.remove(), 250);
            if(onClose) onClose();
        };
    };

})();

class BottomSheet {
    constructor(title, content, onReady = null) {
        this.id = "sheet-" + Math.random().toString(36).substring(2);
        this.onReady = onReady;
        this.render(title, content);
        this.enableDrag();
    }

    render(title, content) {
        const root = document.getElementById("popup-root");

        // Overlay
        this.overlay = document.createElement("div");
        this.overlay.className = "sheet-overlay show";

        // Sheet
        this.sheet = document.createElement("div");
        this.sheet.className = "bottom-sheet show";
        this.sheet.innerHTML = `
            <div class="drag-handle"></div>
            <h5>${title}</h5>
            <div class="sheet-body">${content}</div>
        `;

        // Close when overlay clicked
        this.overlay.addEventListener("click", () => this.close());

        // Append elements to DOM
        root.appendChild(this.overlay);
        root.appendChild(this.sheet);

        // 🔥 Call callback when DOM is ready
        if (typeof this.onReady === "function") {
            this.onReady(this.sheet);
        }
    }

    close() {
        this.sheet.classList.remove("show");
        this.overlay.classList.remove("show");

        setTimeout(() => {
            this.sheet.remove();
            this.overlay.remove();
        }, 300);
    }

    enableDrag() {
        let startY = 0;
        let currentY = 0;
        let dragging = false;
        const sheet = this.sheet;

        const onStart = (e) => {
            dragging = true;
            startY = e.touches ? e.touches[0].clientY : e.clientY;
        };

        const onMove = (e) => {
            if (!dragging) return;
            currentY = e.touches ? e.touches[0].clientY : e.clientY;
            let diff = currentY - startY;
            if (diff > 0) sheet.style.transform = `translateY(${diff}px)`;
        };

        const onEnd = () => {
            if (!dragging) return;
            dragging = false;
            if (currentY - startY > 120) {
                this.close();
            } else {
                sheet.style.transform = `translateY(0)`;
            }
        };

        sheet.addEventListener("mousedown", onStart);
        sheet.addEventListener("touchstart", onStart);

        window.addEventListener("mousemove", onMove);
        window.addEventListener("touchmove", onMove);

        window.addEventListener("mouseup", onEnd);
        window.addEventListener("touchend", onEnd);
    }
}


        function showLoading() {
    // Prevent duplicate loaders
    if (document.getElementById("global_loader_overlay")) return;

    // Overlay
    const overlay = document.createElement("div");
    overlay.id = "global_loader_overlay";
    overlay.style.position = "fixed";
    overlay.style.top = "0";
    overlay.style.left = "0";
    overlay.style.right = "0";
    overlay.style.bottom = "0";
    overlay.style.display = "flex";
    overlay.style.justifyContent = "center";
    overlay.style.alignItems = "center";
    overlay.style.background = "rgba(255,255,255,0.7)";
    overlay.style.zIndex = "999999";
    overlay.style.backdropFilter = "blur(3px)";

    // Spinner
    const circle = document.createElement("div");
    circle.style.width = "100px";
    circle.style.height = "100px";
    circle.style.border = "6px solid #ccc";
    circle.style.borderTopColor = "#000";
    circle.style.borderRadius = "50%";
    circle.style.animation = "loaderSpin 0.8s linear infinite";

    // Add keyframes once
    if (!document.getElementById("loaderSpinStyle")) {
        const style = document.createElement("style");
        style.id = "loaderSpinStyle";
        style.textContent = `
            @keyframes loaderSpin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }

    overlay.appendChild(circle);
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById("global_loader_overlay");
    if (overlay) overlay.remove();
}


(function () {

    function createNotificationContainer(position) {
        let exist = document.getElementById("notify_container_" + position);
        if (exist) return exist;

        let container = document.createElement("div");
        container.id = "notify_container_" + position;
        container.style.position = "fixed";
        container.style.zIndex = "999999";
        container.style.display = "flex";
        container.style.gap = "12px";
        container.style.pointerEvents = "none";

        // Flex direction for stacking
        if (position.includes("top")) container.style.flexDirection = "column";
        else container.style.flexDirection = "column-reverse";

        if (position.includes("top")) container.style.top = "20px";
        else container.style.bottom = "20px";

        if (position.includes("right")) container.style.right = "20px";
        else if (position.includes("left")) container.style.left = "20px";
        else container.style.left = "50%";

        document.body.appendChild(container);
        return container;
    }

    window.pushNotify = function (message, position = "bottom-bottom", bg = "#00b894", color = "white") {

        const wrap = createNotificationContainer(position);

        const box = document.createElement("div");
        box.style.minWidth = "260px";
        box.style.maxWidth = "90vw"; // responsive for small screens
        box.style.padding = "14px 18px";
        box.style.borderRadius = "12px";
        box.style.boxShadow = "0 5px 14px rgba(0,0,0,0.25)";
        box.style.pointerEvents = "auto";
        box.style.position = "relative";
        box.style.lineHeight = "1.5";
        box.style.fontSize = "15px";
        box.style.background = bg;
        box.style.color = color;
        box.style.wordBreak = "break-word"; // wrap long text
        box.innerHTML = message;

        // Close button
        const closeBtn = document.createElement("div");
        closeBtn.innerHTML = "&times;";
        closeBtn.style.position = "absolute";
        closeBtn.style.top = "8px";
        closeBtn.style.right = "10px";
        closeBtn.style.fontSize = "20px";
        closeBtn.style.cursor = "pointer";
        closeBtn.style.color = color;
        closeBtn.style.pointerEvents = "auto";
        closeBtn.onclick = () => closeBox(box, position);
        box.appendChild(closeBtn);

        // Initial transform for open animation
        box.style.opacity = "0";
        box.style.transition = "all 0.5s ease";

        const vh = window.innerHeight; // viewport height

        let initialTransform;
        switch (position) {
            case "top-top":
                initialTransform = `translateY(-${vh}px)`;
                break;
            case "top-right":
                initialTransform = `translateX(120%) translateY(-${vh * 0.05}px)`;
                break;
            case "top-left":
                initialTransform = `translateX(-120%) translateY(-${vh * 0.05}px)`;
                break;
            case "bottom-bottom":
                initialTransform = `translateY(${vh}px)`;
                break;
            case "bottom-right":
                initialTransform = `translateX(120%) translateY(${vh * 0.05}px)`;
                break;
            case "bottom-left":
                initialTransform = `translateX(-120%) translateY(${vh * 0.05}px)`;
                break;
            default:
                initialTransform = `translateY(${vh}px)`;
        }

        box.style.transform = initialTransform;
        wrap.appendChild(box);

        // Animate IN
        setTimeout(() => {
            box.style.opacity = "1";
            box.style.transform = "translateX(0) translateY(0)";
        }, 20);

        // Auto close after 5s
        setTimeout(() => closeBox(box, position), 5000);

        function closeBox(el, pos) {
            el.style.opacity = "0";

            let closeTransform;
            switch (pos) {
                case "top-top":
                    closeTransform = `translateY(-${vh}px)`;
                    break;
                case "top-right":
                    closeTransform = `translateX(120%) translateY(-${vh * 0.05}px)`;
                    break;
                case "top-left":
                    closeTransform = `translateX(-120%) translateY(-${vh * 0.05}px)`;
                    break;
                case "bottom-bottom":
                    closeTransform = `translateY(${vh}px)`;
                    break;
                case "bottom-right":
                    closeTransform = `translateX(120%) translateY(${vh * 0.05}px)`;
                    break;
                case "bottom-left":
                    closeTransform = `translateX(-120%) translateY(${vh * 0.05}px)`;
                    break;
                default:
                    closeTransform = `translateY(${vh}px)`;
            }

            el.style.transform = closeTransform;
            setTimeout(() => el.remove(), 400);
        }
    }

})();


function modalCalls(
    header,
    body,
    color = 'primary',
    text = "Continue",
    funtc = () => {}
) {

    // Remove existing modal if it exists
    const existingModal = document.getElementById('defaultModalPrimary');
    if (existingModal) existingModal.remove();

    // Modal HTML (UNCHANGED DESIGN)
    const html = `
    <div class="modal fade" id="defaultModalPrimary" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${header}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">${body}</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-${color}" id="modalActionBtn">
                        ${text}
                    </button>
                </div>
            </div>
        </div>
    </div>`;

    // Insert modal
    document.body.insertAdjacentHTML('beforeend', html);

    // Bootstrap modal instance
    const modalEl = document.getElementById('defaultModalPrimary');
    const modal = new bootstrap.Modal(modalEl);

    // Attach function safely
    document.getElementById('modalActionBtn').addEventListener('click', () => {
        funtc();
        modal.hide();
    });

    modal.show();
}


async function getfileItem(path) {return await fetch(path).then(res => res.text());}

async function postfileItem(path, data) {
  return await fetch(path, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  }).then(res => res.json());
}



// pushNotify(
//   "<small class='text-success text-uppercase'>Bottom-Bottom notification</small> <br><small class='text-muted'>Sliding up from bottom</small>",
//   "bottom-right",
//   "white",
//   "black"
// );

// pushNotify(
//   "<small class='text-success text-uppercase'>Top-Left notification</small> <br><small class='text-light'>Sliding from top-left</small>",
//   "top-left",
//   "blue",
//   "white"
// );

// pushNotify(
//   "Top-Right notification <br><small>Slides in from right-top</small>",
//   "top-right",
//   "white",
//   "black"
// );


