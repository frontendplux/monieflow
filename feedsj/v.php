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
            --chat-bg: #f0f2f5;
            --glass: rgba(255, 255, 255, 0.9);
        }

        body { 
            background-color: #fff; 
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
        }

        /* --- GLOBAL HEADER --- */
        header.global-header {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e5e5e5;
            padding: 8px 16px;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .balance-pill {
            background: #fff9e6;
            border: 1px solid #ffeeba;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 700;
            color: #856404;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: 0.2s;
        }
        .balance-pill:hover { background: #fff3cd; color: #856404; }

        /* --- LAYOUT --- */
        .chat-container { display: flex; flex: 1; overflow: hidden; }
        
        /* Sidebar (Contact List) */
        .chat-sidebar { 
            width: 360px; 
            border-right: 1px solid #e5e5e5; 
            display: flex; 
            flex-direction: column; 
            background: #fff;
        }

        /* Main Chat Window */
        .chat-main { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            background: var(--chat-bg); 
        }

        .conversation-header {
            background: #fff;
            padding: 10px 20px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .messages-list { 
            flex: 1; 
            overflow-y: auto; 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
        }

        /* Message Bubbles */
        .msg { max-width: 70%; padding: 10px 16px; border-radius: 18px; font-size: 15px; }
        .msg.sent { background: var(--mflow-blue); color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }
        .msg.received { background: #fff; align-self: flex-start; border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); color: #000; }

        /* Input Bar */
        .chat-input-area { 
            background: #fff; 
            padding: 15px 20px; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            border-top: 1px solid #e5e5e5; 
        }

        .input-box { background: #f0f2f5; border: none; border-radius: 20px; padding: 10px 20px; flex: 1; }

        @media (max-width: 768px) {
            .chat-sidebar { display: none; } /* On mobile, only show conversation */
        }
    </style>
</head>
<body>

<header class="global-header">
    <div class="d-flex align-items-center">
        <h4 class="mb-0 fw-bold text-primary me-4">monieFlow</h4>
        <nav class="d-none d-md-flex gap-3">
            <a href="#" class="text-dark text-decoration-none fw-semibold">Feed</a>
            <a href="#" class="text-primary text-decoration-none fw-semibold">Messages</a>
            <a href="#" class="text-dark text-decoration-none fw-semibold">Reels</a>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-3">
        <a href="/wallet" class="balance-pill">
            <i class="ri-copper-coin-fill text-warning"></i>
            <span>12,450 MC</span>
        </a>
        <img src="https://i.pravatar.cc/150?u=me" class="rounded-circle border" width="35" height="35">
    </div>
</header>

<div class="chat-container">
    <div class="chat-sidebar">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Chats</h5>
                <i class="ri-edit-box-line fs-5 text-muted"></i>
            </div>
            <div class="bg-light rounded-pill px-3 py-2">
                <i class="ri-search-line text-muted small"></i>
                <input type="text" class="border-0 bg-transparent ms-2 small" placeholder="Search messages..." style="outline: none;">
            </div>
        </div>
        <div class="overflow-auto">
            <div class="d-flex align-items-center p-3" style="background: #ebf5ff; border-left: 4px solid var(--mflow-blue);">
                <img src="https://i.pravatar.cc/150?u=jack" class="rounded-circle me-3" width="45">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold small">Jack Dorsey</span>
                        <small class="text-muted">12:45 PM</small>
                    </div>
                    <div class="small text-truncate text-muted">You: The 5,000 MC trophy looks sick!</div>
                </div>
            </div>
        </div>
    </div>

    <div class="chat-main">
        <div class="conversation-header">
            <div class="d-flex align-items-center">
                <img src="https://i.pravatar.cc/150?u=jack" class="rounded-circle me-3" width="40">
                <div>
                    <h6 class="mb-0 fw-bold">Jack Dorsey</h6>
                    <small class="text-success">Active now</small>
                </div>
            </div>
            <div class="d-flex gap-3 text-primary fs-4">
                <i class="ri-phone-fill" style="cursor: pointer;"></i>
                <i class="ri-vidicon-fill" style="cursor: pointer;"></i>
                <i class="ri-information-fill" style="cursor: pointer;"></i>
            </div>
        </div>

        <div class="messages-list" id="msgList">
            <div class="msg received">Yo! Did you see the random drop today?</div>
            <div class="msg sent">Yeah! I just won a random 5,000 MC from a gift box.</div>
            <div class="msg received">No way! Send a screenshot of the trophy 🏆</div>
        </div>

        <div class="chat-input-area">
            <i class="ri-add-circle-fill fs-3 text-primary"></i>
            <i class="ri-image-2-fill fs-4 text-muted"></i>
            <input type="text" class="input-box" placeholder="Aa" id="msgInput">
            <i class="ri-thumb-up-fill fs-4 text-primary" id="sendBtn" onclick="sendMsg()"></i>
        </div>
    </div>
</div>

<script>
    function sendMsg() {
        const input = document.getElementById('msgInput');
        const list = document.getElementById('msgList');
        if(!input.value.trim()) return;

        const div = document.createElement('div');
        div.className = 'msg sent';
        div.innerText = input.value;
        list.appendChild(div);
        
        input.value = '';
        list.scrollTop = list.scrollHeight;
    }
</script>

</body>
</html>