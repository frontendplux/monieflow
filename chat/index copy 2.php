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
            overflow: hidden;
            background-color: #fff;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .chat-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Nav */
        .chat-nav {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            padding: 0 15px;
            flex-shrink: 0;
        }

        .chat-main {
            flex: 1;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        /* Sidebar - Chat List */
        .chat-sidebar {
            width: 350px;
            background: #fff;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 5;
        }

        /* Conversation Area */
        .conversation-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
            transition: all 0.3s ease;
        }

        /* Mobile Responsive Logic */
        @media (max-width: 768px) {
            .chat-sidebar {
                width: 100%;
                position: absolute;
                height: 100%;
                left: 0;
            }
            .conversation-area {
                width: 100%;
                position: absolute;
                height: 100%;
                left: 100%; /* Hidden to the right */
            }
            /* When a chat is active on mobile */
            .chat-wrapper.show-chat .chat-sidebar {
                left: -100%;
            }
            .chat-wrapper.show-chat .conversation-area {
                left: 0;
            }
        }

        /* Chat Components */
        .contact-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f5f5f5;
        }
        .contact-item:hover { background: #f8f9fa; }
        .contact-item.active { background: #ebf5ff; }

        .conv-body {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: #f7f9fb;
        }

        .msg-bubble {
            max-width: 80%;
            padding: 8px 14px;
            border-radius: 18px;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        .msg-received { background: #fff; align-self: flex-start; border: 1px solid #dee2e6; border-bottom-left-radius: 4px; }
        .msg-sent { background: var(--mflow-blue); color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }

        .avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        
        .back-btn { display: none; margin-right: 10px; cursor: pointer; }
        @media (max-width: 768px) {
            .back-btn { display: block; }
        }
    </style>
</head>
<body class="">

<div class="chat-wrapper" id="chatWrapper">
    <nav class="chat-nav">
        <h5 class="fw-bold mb-0">monieFlow</h5>
        <div class="ms-auto">
            <i class="ri-user-add-line fs-5 me-3"></i>
            <i class="ri-settings-line fs-5"></i>
        </div>
    </nav>

    <div class="chat-main">
        <aside class="chat-sidebar">
            <div class="p-3">
                <div class="bg-light rounded-pill px-3 py-2 d-flex align-items-center">
                    <i class="ri-search-line text-muted me-2"></i>
                    <input type="text" class="form-control border-0 bg-transparent p-0 small" placeholder="Search chats...">
                </div>
            </div>
            <div class="overflow-auto">
                <div class="contact-item" onclick="openChat()">
                    <img src="https://i.pravatar.cc/150?u=1" class="avatar">
                    <div class="ms-3 overflow-hidden">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold small">Compliance Officer</span>
                            <span class="text-muted small">10m</span>
                        </div>
                        <div class="text-muted small text-truncate">Please check your email for the code.</div>
                    </div>
                </div>
                <div class="contact-item" onclick="openChat()">
                    <img src="https://i.pravatar.cc/150?u=2" class="avatar">
                    <div class="ms-3 overflow-hidden">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold small">Sarah Jenkins (VIP)</span>
                            <span class="text-muted small">2h</span>
                        </div>
                        <div class="text-dark small fw-bold text-truncate">Sent a $20 Tip! ❤️</div>
                    </div>
                </div>
            </div>
        </aside>

        <section class="conversation-area">
            <div class="p-2 border-bottom d-flex align-items-center bg-white">
                <i class="ri-arrow-left-s-line fs-3 back-btn" onclick="closeChat()"></i>
                <img src="https://i.pravatar.cc/150?u=1" class="avatar" style="width: 35px; height: 35px;">
                <div class="ms-2">
                    <h6 class="mb-0 fw-bold small">Compliance Officer</h6>
                    <small class="text-success" style="font-size: 0.6rem;">Online</small>
                </div>
            </div>

            <div class="conv-body d-flex flex-column">
                <div class="msg-bubble msg-received">Hi there! How can I help?</div>
                <div class="msg-bubble msg-sent">I'm having trouble with my agent registration.</div>
            </div>

            <div class="p-3 border-top bg-white">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-add-circle-line fs-4 text-primary"></i>
                    <input type="text" class="form-control rounded-pill border-0 bg-light px-3" placeholder="Aa">
                    <i class="ri-send-plane-2-fill text-primary fs-4"></i>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    const wrapper = document.getElementById('chatWrapper');

    function openChat() {
        wrapper.classList.add('show-chat');
    }

    function closeChat() {
        wrapper.classList.remove('show-chat');
    }
</script>

</body>
</html>