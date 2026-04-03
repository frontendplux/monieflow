import { Feed } from "../helperJs/helperfeeds.js";
import { Footer } from "./feeds.js";

export default async function Chat() {
    // 1. CHAT STATE
    const state = {
        contacts: [
            { id: 1, name: "Luna Star", lastMsg: "Did you see the Rose Quartz price?", time: "2m", online: true, unread: 2, avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Luna" },
            { id: 2, name: "Crypto King", lastMsg: "Trade confirmed! 🚀", time: "1h", online: false, unread: 0, avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=King" },
            { id: 3, name: "Sarah J.", lastMsg: "Check out my new flow.", time: "3h", online: true, unread: 0, avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah" }
        ],
        messages: [
            { id: 1, sender: 'them', text: "Hey Felix! How's the market today?", time: "10:05 AM" },
            { id: 2, sender: 'me', text: "Pretty bullish on Citrine right now! 💎", time: "10:06 AM" },
            { id: 3, sender: 'them', text: "Nice, I just added some to my vault.", time: "10:07 AM" }
        ]
    };

    // 2. UI FRAGMENTS
    const ContactItem = (c) => `
        <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded-4 contact-card cursor-pointer transition-all ${c.unread > 0 ? 'bg-white shadow-sm' : 'opacity-75'}" onclick="loadChat(${c.id})">
            <div class="position-relative">
                <img src="${c.avatar}" class="rounded-circle border border-2 border-white" width="50" height="50">
                ${c.online ? '<span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>' : ''}
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between">
                    <h6 class="fw-black mb-0 text-truncate">${c.name}</h6>
                    <small class="text-muted" style="font-size: 0.7rem;">${c.time}</small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted text-truncate w-75">${c.lastMsg}</small>
                    ${c.unread > 0 ? `<span class="badge bg-primary rounded-circle p-1" style="width:18px; height:18px; font-size:0.6rem;">${c.unread}</span>` : ''}
                </div>
            </div>
        </div>
    `;

    const MessageBubble = (m) => `
        <div class="d-flex ${m.sender === 'me' ? 'justify-content-end' : 'justify-content-start'} mb-3">
            <div class="px-3 py-2 ${m.sender === 'me' ? 'bg-primary text-white bubble-me' : 'bg-white text-dark shadow-sm bubble-them'}" 
                 style="max-width: 75%; border-radius: 20px;">
                <p class="mb-1 small fw-bold">${m.text}</p>
                <div class="text-end" style="font-size: 0.6rem; opacity: 0.7;">${m.time} ${m.sender === 'me' ? '<i class="bi bi-check2-all"></i>' : ''}</div>
            </div>
        </div>
    `;

    // 3. MAIN TEMPLATE
    return /* html */ `
    <div class="container-fluid p-0" style="height: calc(100vh - 70px); background: #f0f2f5; overflow: hidden;">
        <div class="row g-0 h-100">
            
            <aside class="col-12 col-md-4 col-lg-3 border-end bg-light h-100 d-flex flex-column">
                <div class="p-4 bg-white border-bottom">
                    <h4 class="fw-black mb-3">Messages</h4>
                    <div class="input-group bg-light rounded-pill px-3 py-1 border-0">
                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-transparent border-0 shadow-none small" placeholder="Search chats...">
                    </div>
                </div>
                <div class="flex-grow-1 overflow-auto p-3 h-100">
                    ${state.contacts.map(ContactItem).join('')}
                </div>
            </aside>

            <main class="col-12 col-md-8 col-lg-9 d-flex flex-column h-100 bg-white">
                
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between shadow-sm position-relative" style="z-index: 10;">
                    <div class="d-flex align-items-center gap-3">
                        <img src="${state.contacts[0].avatar}" class="rounded-circle shadow-sm" width="45">
                        <div>
                            <h6 class="fw-black mb-0">${state.contacts[0].name}</h6>
                            <small class="text-success fw-bold" style="font-size: 0.7rem;">● Online</small>
                        </div>
                    </div>
                    <div class="d-flex gap-3 text-muted fs-5">
                        <i class="bi bi-telephone cursor-pointer"></i>
                        <i class="bi bi-camera-video cursor-pointer"></i>
                        <i class="bi bi-info-circle cursor-pointer"></i>
                    </div>
                </div>

                <div class="flex-grow-1 overflow-auto p-4" style="background: #f8f9fa;" id="chat-feed">
                    <div class="text-center mb-4">
                        <span class="badge bg-white text-muted border rounded-pill px-3">Today</span>
                    </div>
                    ${state.messages.map(MessageBubble).join('')}
                </div>

                <div class="p-3 border-top bg-white">
                    <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-1">
                        <button class="btn btn-link text-muted p-0 fs-5"><i class="bi bi-plus-circle"></i></button>
                        <input type="text" class="form-control bg-transparent border-0 shadow-none" placeholder="Type a message or share a flow...">
                        <button class="btn btn-link text-muted p-0 fs-5"><i class="bi bi-emoji-smile"></i></button>
                        <button class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                            <i class="bi bi-send-fill text-dark"></i>
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <style>
        .fw-black { font-weight: 900; }
        .contact-card:hover { background: #fff !important; transform: translateX(5px); }
        .bubble-me { 
            background: linear-gradient(135deg, #FF9900, #FFB700) !important; 
            border-bottom-right-radius: 5px !important;
            color: #000 !important;
        }
        .bubble-them { 
            border-bottom-left-radius: 5px !important; 
        }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
        .cursor-pointer { cursor: pointer; }
    </style>
    ${Footer({page: 'chat'})}
    `;
}