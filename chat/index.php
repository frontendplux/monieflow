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
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* Container to keep it within the dashboard feel */
        .chat-page-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .chat-card {
            height: 100%;
            background: #fff;
            border-radius: 0px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            display: flex;
            overflow: hidden;
        }

        /* Sidebar - Chat List */
        .chat-sidebar {
            width: 350px;
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
            transition: 0.3s ease;
        }

        /* Conversation Area */
        .conversation-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
            transition: 0.3s ease;
        }

        /* MOBILE RESPONSIVENESS */
        @media (max-width: 768px) {
            .chat-page-container { padding: 0; }
            .chat-card { border-radius: 0; }
            
            .chat-sidebar {
                width: 100%;
                flex: none;
            }
            .conversation-area {
                position: fixed;
                top: 0;
                left: 100%; /* Off-screen */
                width: 100%;
                height: 100%;
                z-index: 1000;
            }
            /* Class to trigger on mobile */
            .show-messages .conversation-area {
                left: 0;
            }
        }

        /* Chat Components */
        .contact-item {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: 0.2s;
        }
        .contact-item:hover { background: #f0f7ff; }
        .contact-item.active { background: #e7f3ff; border-left: 4px solid var(--mflow-blue); }

        .conv-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: #f9fbff;
        }

        .msg-bubble {
            max-width: 75%;
            padding: 12px 16px;
            border-radius: 20px;
            margin-bottom: 10px;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .received { background: #fff; border: 1px solid #eee; align-self: flex-start; border-bottom-left-radius: 4px; }
        .sent { background: var(--mflow-blue); color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }

        .back-btn { display: none; cursor: pointer; margin-right: 15px; }
        @media (max-width: 768px) { .back-btn { display: block; } }

        .avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
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

    </style>
</head>
<body>

<div class="chat-page-container chat-wrapper" id="mainWrapper">
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
    <div class="chat-card">
        
        <aside class="chat-sidebar">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Chats</h4>
                <i class="ri-edit-circle-line fs-4 text-primary"></i>
            </div>
            <div class="p-3">
                <div class="input-group bg-light rounded-pill px-3">
                    <span class="input-group-text bg-transparent border-0"><i class="ri-search-line"></i></span>
                    <input type="text" class="form-control bg-transparent border-0" placeholder="Search messenger">
                </div>
            </div>
            <div class="overflow-auto">
                <div class="contact-item active d-flex align-items-center" onclick="toggleMobileChat()">
                    <img src="https://i.pravatar.cc/150?u=a1" class="avatar">
                    <div class="ms-3 overflow-hidden">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 fw-bold small">Sarah Jenkins</h6>
                            <span class="text-muted small">12:45 PM</span>
                        </div>
                        <p class="text-muted small mb-0 text-truncate">I just sent the proof of payment!</p>
                    </div>
                </div>
                <div class="contact-item d-flex align-items-center" onclick="toggleMobileChat()">
                    <img src="https://i.pravatar.cc/150?u=a2" class="avatar">
                    <div class="ms-3 overflow-hidden">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 fw-bold small">Compliance Desk</h6>
                            <span class="text-muted small">2m</span>
                        </div>
                        <p class="text-dark fw-bold small mb-0 text-truncate">Your agent status is active!</p>
                    </div>
                </div>
            </div>
        </aside>

        <section class="conversation-area">
            <div class="p-3 border-bottom d-flex align-items-center bg-white sticky-top">
                <i class="ri-arrow-left-line fs-4 back-btn" onclick="toggleMobileChat()"></i>
                <img src="https://i.pravatar.cc/150?u=a1" class="avatar" style="width: 40px; height: 40px;">
                <div class="ms-3">
                    <h6 class="mb-0 fw-bold">Sarah Jenkins</h6>
                    <small class="text-success" style="font-size: 0.7rem;">Online</small>
                </div>
                <div class="ms-auto d-flex gap-3 text-primary">
                    <i class="ri-phone-fill fs-5"></i>
                    <i class="ri-vidicon-fill fs-5"></i>
                </div>
            </div>

            <div class="conv-body d-flex flex-column" id="chatBox">
                <div class="msg-bubble received shadow-sm">
                    Hey! I saw your new subscription tier. Can I get a custom video if I join Gold?
                </div>
                <div class="msg-bubble sent shadow-sm">
                    Absolutely! Gold members get one custom request per month. 💎
                </div>
                <div class="msg-bubble received shadow-sm">
                    Perfect! Sending the payment now.
                </div>
                <div class="msg-bubble received shadow-sm p-2">
                    <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&q=80&w=400" class="img-fluid rounded-3">
                </div>
            </div>

            <div class="p-3 border-top bg-white">
                <form class="d-flex align-items-center gap-2">
                    <i class="ri-add-circle-fill fs-4 text-primary"></i>
                    <div class="flex-grow-1">
                        <input type="text" class="form-control border-0 bg-light rounded-pill px-4 py-2" placeholder="Aa">
                    </div>
                    <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: var(--mflow-blue);">
                        <i class="ri-send-plane-2-fill"></i>
                    </button>
                </form>
            </div>
        </section>

    </div>
</div>

<script>
    // Scroll to bottom on load
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;

    // Mobile view toggle
    function toggleMobileChat() {
        document.getElementById('mainWrapper').classList.toggle('show-messages');
    }
</script>

</body>
</html>