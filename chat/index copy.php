<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | monieFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --mflow-blue: #1877f2;
            --bg-light: #f0f2f5;
        }

        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden; /* Prevents double scrollbars */
            background-color: var(--bg-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .chat-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .chat-nav {
            height: 65px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 10;
        }

        .chat-main {
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        /* Left Sidebar: Contact List */
        .chat-sidebar {
            width: 360px;
            background: #fff;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        .search-area {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .contact-list {
            flex: 1;
            overflow-y: auto;
        }

        .contact-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f9f9f9;
        }

        .contact-item:hover { background: #f5f5f5; }
        .contact-item.active { background: #eef5ff; border-right: 3px solid var(--mflow-blue); }

        /* Right Side: Conversation Area */
        .conversation-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .conv-header {
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .conv-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: #f7f9fb;
            background-image: radial-gradient(#d1d1d1 0.5px, transparent 0.5px);
            background-size: 20px 20px;
        }

        .conv-footer {
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            background: #fff;
        }

        /* Bubbles */
        .msg-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 18px;
            margin-bottom: 10px;
            font-size: 0.95rem;
            position: relative;
        }

        .msg-received {
            background: #fff;
            color: #000;
            align-self: flex-start;
            border: 1px solid #eee;
            border-bottom-left-radius: 4px;
        }

        .msg-sent {
            background: var(--mflow-blue);
            color: #fff;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .avatar-img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .online-dot {
            width: 12px;
            height: 12px;
            background: #31a24c;
            border: 2px solid #fff;
            border-radius: 50%;
            position: absolute;
            bottom: 2px;
            right: 2px;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }

        @media (max-width: 768px) {
            .chat-sidebar { width: 100%; }
            .conversation-area { display: none; } /* On mobile, you'd toggle these */
        }
    </style>
</head>
<body>

<div class="chat-wrapper">
    <nav class="chat-nav shadow-sm">
        <a href="/" class="text-dark me-3 text-decoration-none">
            <i class="ri-arrow-left-line fs-4"></i>
        </a>
        <h5 class="fw-bold mb-0">monieFlow Messages</h5>
        <div class="ms-auto d-flex gap-3">
            <i class="ri-video-add-line fs-4 text-muted"></i>
            <i class="ri-settings-4-line fs-4 text-muted"></i>
        </div>
    </nav>

    <div class="chat-main">
        <aside class="chat-sidebar">
            <div class="search-area">
                <div class="input-group bg-light rounded-pill border px-2">
                    <span class="input-group-text bg-transparent border-0"><i class="ri-search-line text-muted"></i></span>
                    <input type="text" class="form-control bg-transparent border-0 small" placeholder="Search Messenger">
                </div>
            </div>
            
            <div class="contact-list">
                <a href="#" class="contact-item active">
                    <div class="position-relative">
                        <img src="https://i.pravatar.cc/150?u=agency" class="avatar-img">
                        <div class="online-dot"></div>
                    </div>
                    <div class="ms-3 flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold small">Compliance Officer</span>
                            <span class="text-muted" style="font-size: 0.7rem;">10:30 AM</span>
                        </div>
                        <div class="small text-muted text-truncate">Please upload your ID for review.</div>
                    </div>
                </a>

                <a href="#" class="contact-item">
                    <img src="https://i.pravatar.cc/150?u=fan" class="avatar-img">
                    <div class="ms-3 flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold small">Top Fan #01</span>
                            <span class="text-muted" style="font-size: 0.7rem;">Yesterday</span>
                        </div>
                        <div class="small text-dark fw-bold text-truncate">Sent a $50 Tip! 💎</div>
                    </div>
                </a>
            </div>
        </aside>

        <section class="conversation-area">
            <div class="conv-header">
                <div class="d-flex align-items-center">
                    <img src="https://i.pravatar.cc/150?u=agency" class="avatar-img" style="width: 40px; height: 40px;">
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold">Compliance Officer</h6>
                        <small class="text-success" style="font-size: 0.7rem;">Active Now</small>
                    </div>
                </div>
                <div class="text-muted d-flex gap-3">
                    <i class="ri-phone-fill fs-5"></i>
                    <i class="ri-information-fill fs-5"></i>
                </div>
            </div>

            <div class="conv-body d-flex flex-column" id="chatBox">
                <div class="text-center my-4">
                    <span class="badge bg-light text-muted fw-normal rounded-pill px-3 py-2 border">FEBRUARY 12, 2026</span>
                </div>

                <div class="msg-bubble msg-received shadow-sm">
                    Hello! Welcome to monieFlow Agency Support. How can I assist your registration today?
                </div>

                <div class="msg-bubble msg-sent shadow-sm">
                    Hi, I just submitted my business license. How long does the review take?
                </div>

                <div class="msg-bubble msg-received shadow-sm">
                    Typically it takes 24-48 hours. I see your document is clear, so it might be faster!
                </div>

                <div class="msg-bubble msg-sent shadow-sm">
                    That sounds great, thanks!
                </div>
            </div>

            <div class="conv-footer">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex gap-2 text-primary me-2">
                        <i class="ri-add-circle-fill fs-4"></i>
                        <i class="ri-image-2-fill fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <input type="text" class="form-control border-0 bg-light rounded-pill px-4" placeholder="Type a message...">
                    </div>
                    <button class="btn text-primary"><i class="ri-thumb-up-fill fs-4"></i></button>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    // Keeps chat scrolled to bottom
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>