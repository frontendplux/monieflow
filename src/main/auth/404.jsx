export default function NotFound(){
    return(   <div className="w-100 my-5 mx-auto" style={{maxWidth:"490px"}}>
                                <div className="text-center my-2">
                                <svg viewBox="0 0 112 112" width="112" height="112" className="x14rh7hd x1lliihq x1tzjh5l x1k90msu x2h7rmj x1qfuztq" style={{"--x-color": "var(--primary-icon)"}}>
                                    <defs>
                                        <clipPath id="a">
                                            <path d="M107.12 90.42a12.77 12.77 0 0 1-17.82 2.93L41.2 58.82l14.89-20.75 48.1 34.53a12.77 12.77 0 0 1 2.93 17.82z" fill="#bcc0c4"></path>
                                        </clipPath>
                                        <clipPath id="b">
                                            <rect x="75.1" y="44.29" width="7.5" height="13.78" rx="3.48" fill="none"></rect>
                                            </clipPath>
                                    </defs>
                                    <path d="m86.78 102.54-12.44.09 7.73-19.87 10.77 4.19-6.06 15.59z" fill="#90c3ff"></path>
                                    <path d="m88.37 107.16-15.85.1 1.82-4.63 12.44-.09-.61 1.75 2.2 2.87zm-14.03-6.62-15.85.1L60.32 96l12.43-.08-.61 1.75 2.2 2.87z" fill="#64676b"></path>
                                    <path d="m72.64 96.16-12.44.08 7.73-19.87 10.77 4.19-6.06 15.6z"></path>
                                    <path d="M107.12 90.42a12.77 12.77 0 0 1-17.82 2.93L41.2 58.82l14.89-20.75 48.1 34.53a12.77 12.77 0 0 1 2.93 17.82z" fill="#bcc0c4"></path>
                                    <g clip-path="url(#a)">
                                        <path d="M107.12 90.42a12.77 12.77 0 0 1-17.82 2.93L41.2 58.82l14.89-20.75 48.1 34.53a12.77 12.77 0 0 1 2.93 17.82z" fill="#a4a7ab"></path>
                                    </g>
                                    <path d="m68.76 58.96-9.13 3.09 1.89 9.59" stroke="#1876f2" stroke-linecap="round" stroke-linejoin="round" stroke-width="9.48px" fill="none"></path>
                                    <path d="m87.69 76.55-26.25-7.26 3.07-11.12a6.56 6.56 0 0 1 8.07-4.56l13.62 3.77a6.54 6.54 0 0 1 4.56 8.06z" fill="#fff"></path>
                                    <path d="M35.35 7.61A29.17 29.17 0 0 0 21.05 10a1.24 1.24 0 0 0-.21 2.13L41.39 26.9a5.6 5.6 0 0 1 1.28 7.82l-8.35 11.64a5.62 5.62 0 0 1-7.83 1.29L5.77 32.77a1.23 1.23 0 0 0-1.94.84c0 .39-.08.78-.1 1.17A29.26 29.26 0 1 0 35.35 7.61z" fill="#a4a7ab"></path>
                                    <path d="m91.66 90.06-15.27-.31A16 16 0 0 1 61.76 68l26.45 7.9a7.7 7.7 0 0 1 5 10.14z" fill="#90c3ff"></path><path d="m34.76 24.31-10.31-7.4-11.57 5.23-1.25 12.63 10.31 7.4 11.56-5.23 1.26-12.63z" fill="#64676b"></path>
                                    <path d="m86.65 63.16-4.38 14.9 6.82 11.84" stroke="#1876f2" stroke-linecap="round" stroke-linejoin="round" stroke-width="9.48px" fill="none"></path>
                                    <g clip-path="url(#b)"><rect x="75.1" y="44.29" width="7.5" height="13.78" rx="3.48" fill="#1876f2"></rect>
                                    <ellipse cx="74.83" cy="46.44" rx="7.58" ry="5.93"></ellipse></g><path d="M75 48.92a4.24 4.24 0 0 1 8.31-1.64" fill="#64676b"></path>
                                    <circle cx="81.1" cy="49.83" r="1.49" fill="#1876f2"></circle><circle cx="73.05" cy="52.22" r="3.19"></circle>
                                    <path transform="rotate(-11.08 84.387 46.274)" d="M81.18 45.53h6.41v1.52h-6.41z" fill="#64676b"></path>
                                    <rect x="81.33" y="49.37" width="1.9" height="0.92" rx="0.46" transform="rotate(-13.22 82.251 49.804)" fill="#1876f2"></rect>
                                </svg>
                            </div>
                            <div className="fw-bold text-center text-muted">This page isn't available right now</div>
                            <div className="text-center my-2">This may be because of a technical error that we're working to get fixed. Try reloading this page.</div>
                            <p className="text-center"><button onClick={()=>{window.location.href="/"}} className="btn px-4 btn-primary">Reload Page</button></p>
                            </div>);
}