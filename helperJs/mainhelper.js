



export const toggleNav = (color = '#1877f2', title = 'Notification', message = 'New update available') => {
    // 1. Create/Ensure a container for stacking exists
    let stackContainer = document.getElementById('mflow-toast-stack');
    if (!stackContainer) {
        stackContainer = document.createElement('div');
        stackContainer.id = 'mflow-toast-stack';
        // Fixed at top, centered, and Z-indexed to the moon
        stackContainer.className = "position-fixed top-0 start-0 end-0 p-4 d-flex flex-column align-items-center justify-content-center gap-3";
        stackContainer.style.zIndex = "9999";
        stackContainer.style.pointerEvents = "none";
        document.body.appendChild(stackContainer);
    }

    // 2. Create the unique Toast element
    const toast = document.createElement('div');
    toast.className = "w-100 mw-sm translate-middle-y opacity-0";
    toast.style.transition = "all 0.5s cubic-bezier(0.23,1,0.32,1)";
    toast.style.pointerEvents = "auto";
    
    // Internal HTML with monieFlow Glassmorphism
    toast.innerHTML = `
        <div class="bg-white col-12 col-lg-5 bg-opacity-90 border border-white shadow-lg rounded-4 p-4 d-flex align-items-center gap-4 border-start mx-auto" style="backdrop-filter: blur(20px); border-left: 4px solid ${color};">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-3 shadow-sm" style="width: 40px; height: 40px; background-color: ${color}20;">
                <div class="rounded-circle" style="width: 12px; height: 12px; background-color: ${color}; animation: pulse 1.5s infinite;"></div>
            </div>
            <div class="flex-grow-1">
                <h6 class="text-uppercase fw-bold text-muted mb-1" style="font-size: 11px; letter-spacing: 1px;">${title}</h6>
                <p class="fw-light text-dark mb-0 text-wrap" style="font-size: 14px;">${message}</p>

            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="btn btn-link text-muted p-0" style="transition: color 0.3s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;

    // 3. Add to stack
    stackContainer.prepend(toast);

    // 4. Animate "Drop"
    setTimeout(() => {
        toast.classList.remove('translate-middle-y', 'opacity-0');
        toast.style.transform = "translateY(0)";
        toast.style.opacity = "1";
    }, 10);

    // 5. Automatic "Return" (Remove) after 4 seconds
    setTimeout(() => {
        toast.style.transform = "translateY(-10px)";
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 500);
    }, 4000);
};

// Extra CSS for pulse animation
const style = document.createElement('style');
style.innerHTML = `
@keyframes pulse {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.3); opacity: 0.6; }
  100% { transform: scale(1); opacity: 1; }
}
`;
document.head.appendChild(style);


export function timeAgo(datetime) {
    const now = new Date();
    const past = new Date(datetime.replace(" ", "T")); // fix format
    const seconds = Math.floor((now - past) / 1000);

    const intervals = [
        { label: "year", seconds: 31536000 },
        { label: "month", seconds: 2592000 },
        { label: "day", seconds: 86400 },
        { label: "hour", seconds: 3600 },
        { label: "minute", seconds: 60 },
        { label: "second", seconds: 1 }
    ];

    for (let i of intervals) {
        const count = Math.floor(seconds / i.seconds);
        if (count > 0) {
            return count + " " + i.label + (count > 1 ? "s" : "") + " ago";
        }
    }

    return "just now";
}