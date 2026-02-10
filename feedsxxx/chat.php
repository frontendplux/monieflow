<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__."/../main-function.php"; ?>
    <?php 
        $main = new main($conn);
        if($main->isLoggedIn() === false) header('location:/');
        $userData = $main->getUserData()['data'];
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | monieFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --mflow-blue: #1877f2;
            --chat-bg: #f0f2f5;
            --msg-in: #ffffff;
            --msg-out: #0084ff;
        }

        body { background-color: #fff; height: 100vh; overflow: hidden; display: flex; flex-direction: column; }

        /* Layout */
        .chat-container { display: flex; flex: 1; overflow: hidden; }
        
        /* Sidebar */
        .chat-sidebar { width: 360px; border-right: 1px solid #e5e5e5; display: flex; flex-direction: column; }
        .contact-item { padding: 12px 16px; display: flex; align-items: center; cursor: pointer; transition: 0.2s; border-radius: 8px; margin: 0 8px; }
        .contact-item:hover { background: #f2f2f2; }
        .contact-item.active { background: #ebf5ff; }

        /* Main Chat Area */
        .chat-main { flex: 1; display: flex; flex-direction: column; background: var(--chat-bg); }
        .chat-header { background: #fff; padding: 10px 20px; border-bottom: 1px solid #e5e5e5; display: flex; align-items: center; justify-content: space-between; }
        
        .messages-list { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; }
        
        /* Message Bubbles */
        .msg { max-width: 70%; padding: 10px 16px; border-radius: 18px; font-size: 15px; position: relative; }
        .msg.received { background: var(--msg-in); align-self: flex-start; border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .msg.sent { background: var(--msg-out); color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }

        /* Money Message */
        .msg-payment { background: #fff; border: 1px solid #e5e5e5; border-radius: 12px; padding: 15px; width: 260px; align-self: flex-start; }
        .pay-btn { background: var(--mflow-blue); color: #fff; border: none; width: 100%; border-radius: 8px; padding: 8px; font-weight: 600; margin-top: 10px; }

        /* Input Area */
        .chat-input-area { background: #fff; padding: 15px 20px; display: flex; align-items: center; gap: 12px; border-top: 1px solid #e5e5e5; }
        .input-box { background: #f0f2f5; border: none; border-radius: 20px; padding: 10px 20px; flex: 1; }
        .action-icon { font-size: 22px; color: var(--mflow-blue); cursor: pointer; transition: 0.2s; }
        .action-icon:hover { transform: scale(1.1); }

        @media (max-width: 768px) {
            .chat-sidebar { display: none; }
        }
    </style>
</head>
<body>

<div class="chat-container containerg">
    <div class="chat-sidebar">
        <div class="p-3">
            <h4 class="fw-bold mb-3">Chats</h4>
            <div class="bg-light rounded-pill px-3 py-2 d-flex align-items-center">
                <i class="ri-search-line text-muted me-2"></i>
                <input type="text" class="form-control border-0 bg-transparent p-0 shadow-none" placeholder="Search monieFlow">
            </div>
        </div>
        <div class="overflow-auto">
            <div class="contact-item active">
                <img src="https://i.pravatar.cc/150?u=jack" class="rounded-circle me-3" width="48">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Jack Dorsey</span>
                        <small class="text-muted">12:45 PM</small>
                    </div>
                    <div class="small text-truncate text-muted">You: Sent 500 MC 🚀</div>
                </div>
            </div>
            <div class="contact-item">
                <img src="https://i.pravatar.cc/150?u=sarah" class="rounded-circle me-3" width="48">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Sarah S.</span>
                        <small class="text-muted">Yesterday</small>
                    </div>
                    <div class="small text-truncate text-muted">The task is completed!</div>
                </div>
            </div>
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <div class="d-flex align-items-center">
                <i class="ri-arrow-left-line d-md-none me-3 fs-4"></i>
                <div class="position-relative">
                    <img src="https://i.pravatar.cc/150?u=jack" class="rounded-circle me-3" width="40">
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 12px; height: 12px; transform: translate(-10px, -2px);"></span>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">Jack Dorsey</h6>
                    <small class="text-success">Online</small>
                </div>
            </div>
            <div class="d-flex gap-3">
                <i class="ri-phone-line action-icon"></i>
                <i class="ri-vidicon-line action-icon"></i>
                <i class="ri-information-line action-icon"></i>
            </div>
        </div>

        <div class="messages-list" id="msgList">
            <div class="text-center my-3"><span class="badge bg-light text-muted fw-normal">Today</span></div>
            
            <div class="msg received">Hey! Are you still available for the UI fix?</div>
            
            <div class="msg sent">Yes, I am. I can start right now.</div>

            <div class="msg-payment shadow-sm">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                        <i class="ri-wallet-3-line text-primary"></i>
                    </div>
                    <span class="fw-bold small">Payment Request</span>
                </div>
                <div class="fs-4 fw-bold mb-1">500.00 MC</div>
                <div class="text-muted small">For "Swiper Component Fix"</div>
                <button class="pay-btn" onclick="location.href='/passkey'">Pay Now</button>
            </div>

            <div class="msg sent">Just sent the coins! Check your wallet. 🚀</div>
        </div>

        <div class="chat-input-area">
            <i class="ri-add-circle-fill fs-3 text-primary cursor-pointer" data-bs-toggle="dropdown"></i>
            <ul class="dropdown-menu shadow border-0 mb-3">
                <li><a class="dropdown-item py-2" href="/send"><i class="ri-coin-line me-2 text-warning"></i> Send monieCoins</a></li>
                <li><a class="dropdown-item py-2" href="#"><i class="ri-file-list-3-line me-2 text-info"></i> Create Invoice</a></li>
                <li><a class="dropdown-item py-2" href="#"><i class="ri-image-line me-2 text-success"></i> Photo & Video</a></li>
            </ul>
            
            <input type="text" class="input-box" placeholder="Type a message..." id="msgInput">
            
            <i class="ri-heart-fill action-icon text-danger"></i>
            <i class="ri-send-plane-2-fill action-icon" onclick="sendMessage()"></i>
        </div>
    </div>
</div>

<script>
    function sendMessage() {
        const input = document.getElementById('msgInput');
        const list = document.getElementById('msgList');
        if(input.value.trim() === "") return;

        const msgDiv = document.createElement('div');
        msgDiv.className = 'msg sent';
        msgDiv.innerText = input.value;
        list.appendChild(msgDiv);
        
        input.value = "";
        list.scrollTop = list.scrollHeight;
    }

    // Allow Enter key to send
    document.getElementById('msgInput').addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            sendMessage();
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>