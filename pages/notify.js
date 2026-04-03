import { Feed } from "../helperJs/helperfeeds.js";
import { Footer } from "./feeds.js";

export default async function Notifications() {
    // 1. DATA STATE
    const state = {
        notifications: [
            { 
                id: 1, type: 'transaction', title: 'Payment Received', 
                desc: 'You received 50.00 SF from @luna_crypto', 
                time: '2m ago', unread: true, icon: 'bi-cash-stack', color: '#2ecc71' 
            },
            { 
                id: 2, type: 'social', title: 'New Follower', 
                desc: '@crypto_king started following your flows', 
                time: '1h ago', unread: true, icon: 'bi-person-plus-fill', color: '#3498db' 
            },
            { 
                id: 3, type: 'system', title: 'Security Alert', 
                desc: 'New login detected from a Chrome browser on Windows.', 
                time: '3h ago', unread: false, icon: 'bi-shield-lock-fill', color: '#e74c3c' 
            },
            { 
                id: 4, type: 'market', title: 'Price Drop!', 
                desc: 'Void Obsidian is down 5% in the last hour. Buy the dip?', 
                time: '5h ago', unread: false, icon: 'bi-graph-down-arrow', color: '#f1c40f' 
            }
        ]
    };

    // 2. LOGIC
    window.markAllRead = () => {
        state.notifications.forEach(n => n.unread = false);
        renderNotifications();
    };

    window.deleteNotification = (id) => {
        const index = state.notifications.findIndex(n => n.id === id);
        if (index > -1) {
            state.notifications.splice(index, 1);
            renderNotifications();
        }
    };

    // 3. UI FRAGMENTS
    const NotificationItem = (item) => `
        <div class="card border-0 mb-2 position-relative transition-all notification-card ${item.unread ? 'unread-glow' : ''}" 
             style="border-radius: 20px; background: ${item.unread ? 'rgba(255, 255, 255, 1)' : 'rgba(255, 255, 255, 0.6)'};">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                     style="width: 50px; height: 50px; flex-shrink: 0; background: ${item.color}15; color: ${item.color};">
                    <i class="bi ${item.icon} fs-4"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="fw-black mb-0 ${item.unread ? 'text-dark' : 'text-muted'}">${item.title}</h6>
                        <small class="text-muted" style="font-size: 0.7rem;">${item.time}</small>
                    </div>
                    <p class="mb-0 text-secondary small text-truncate" style="max-width: 250px;">${item.desc}</p>
                </div>

                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4">
                        <li><a class="dropdown-item small fw-bold" href="javascript:void(0)" onclick="deleteNotification(${item.id})">Delete</a></li>
                        <li><a class="dropdown-item small fw-bold" href="#">Mute User</a></li>
                    </ul>
                </div>
            </div>
            ${item.unread ? '<span class="position-absolute top-50 start-0 translate-middle p-1 bg-primary border border-light rounded-circle" style="margin-left: 10px;"></span>' : ''}
        </div>
    `;

    // 4. LIVE RENDER
    const renderNotifications = () => {
        const list = document.getElementById('notification-list');
        const unreadCount = state.notifications.filter(n => n.unread).length;
        if (list) list.innerHTML = state.notifications.map(NotificationItem).join('');
        
        const badge = document.getElementById('unread-badge');
        if (badge) badge.innerText = unreadCount;
    };

    // 5. MAIN TEMPLATE
    return /* html */ `
    <div class="container py-4 px-md-5" style="max-width: 700px; min-height: 100vh;">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-black mb-0">Notifications</h2>
                <p class="text-muted small mb-0">You have <span id="unread-badge" class="fw-bold text-primary">${state.notifications.filter(n => n.unread).length}</span> unread alerts</p>
            </div>
            <button onclick="markAllRead()" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-primary border-0 shadow-sm">
                Mark all as read
            </button>
        </div>

        <div class="d-flex gap-2 mb-4 overflow-auto pb-2" style="scrollbar-width: none;">
            <button class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">All</button>
            <button class="btn btn-white bg-white shadow-sm rounded-pill px-4 btn-sm fw-bold text-muted">Transactions</button>
            <button class="btn btn-white bg-white shadow-sm rounded-pill px-4 btn-sm fw-bold text-muted">Social</button>
            <button class="btn btn-white bg-white shadow-sm rounded-pill px-4 btn-sm fw-bold text-muted">Market</button>
        </div>

        <div id="notification-list">
            ${state.notifications.map(NotificationItem).join('')}
        </div>

        ${state.notifications.length === 0 ? `
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted opacity-25"></i>
                <p class="text-muted mt-3">All caught up!</p>
            </div>
        ` : ''}
    </div>

    <style>
        .notification-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .notification-card:hover {
            transform: scale(1.01);
            background: #fff !important;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }
        .unread-glow {
            border-left: 4px solid #3498db !important;
        }
        .fw-black { font-weight: 900; }
        ::-webkit-scrollbar { display: none; }
    </style>
    ${Footer({page: 'alerts'})}
    `;
}