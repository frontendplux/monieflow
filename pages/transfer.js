import { Feed } from "../helperJs/helperfeeds.js";
import { Footer } from "./feeds.js";

export default async function Transfer() {
    // 1. TRANSACTION STATE
    const state = {
        amount: "0",
        balance: 2540.50,
        selectedContact: null,
        contacts: [
            { id: 1, name: "Luna", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Luna" },
            { id: 2, name: "King", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=King" },
            { id: 3, name: "Sarah", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah" },
            { id: 4, name: "Felix", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" },
            { id: 5, name: "Aria", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Aria" }
        ]
    };

    // 2. LOGIC
    window.updateAmount = (num) => {
        const display = document.getElementById('transfer-amount');
        if (num === 'back') {
            state.amount = state.amount.length > 1 ? state.amount.slice(0, -1) : "0";
        } else if (num === '.' && state.amount.includes('.')) {
            return;
        } else {
            state.amount = state.amount === "0" ? num.toString() : state.amount + num;
        }
        display.innerText = state.amount;
        
        // Error handling: checking if balance exceeded
        display.classList.toggle('text-danger', parseFloat(state.amount) > state.balance);
    };

    window.selectContact = (id) => {
        state.selectedContact = id;
        document.querySelectorAll('.contact-pill').forEach(el => el.classList.remove('active-contact'));
        document.getElementById(`contact-${id}`).classList.add('active-contact');
    };

    // 3. UI FRAGMENTS
    const KeypadBtn = (val) => `
        <div class="col-4 p-2">
            <button onclick="updateAmount('${val}')" class="btn btn-light w-100 py-3 rounded-4 fw-black fs-4 shadow-sm border-0">
                ${val === 'back' ? '<i class="bi bi-backspace"></i>' : val}
            </button>
        </div>
    `;

    // 4. MAIN TEMPLATE
    return /* html */ `
    <div class="container py-4 px-md-5" style="max-width: 500px; min-height: 100vh;">
        <div class="text-center mb-5">
            <h4 class="fw-black">Send Money</h4>
            <p class="text-muted small">Instant transfer to your monieFlow squad</p>
        </div>

        <div class="mb-5">
            <h6 class="fw-bold mb-3 px-2">Recent Contacts</h6>
            <div class="d-flex gap-3 overflow-auto pb-3 px-2" style="scrollbar-width: none;">
                ${state.contacts.map(c => `
                    <div id="contact-${c.id}" onclick="selectContact(${c.id})" class="contact-pill text-center cursor-pointer transition-all">
                        <img src="${c.avatar}" class="rounded-circle border border-3 border-white shadow-sm mb-2" width="65" height="65">
                        <div class="small fw-bold text-muted">${c.name}</div>
                    </div>
                `).join('')}
                <div class="text-center">
                    <div class="rounded-circle border-2 border-dashed border-secondary d-flex align-items-center justify-content-center mb-2" style="width: 65px; height: 65px;">
                        <i class="bi bi-plus fs-2 text-muted"></i>
                    </div>
                    <div class="small fw-bold text-muted">New</div>
                </div>
            </div>
        </div>

        <div class="text-center mb-5">
            <div class="display-4 fw-black mb-1 d-flex justify-content-center align-items-center">
                <span class="fs-2 text-muted me-1">$</span>
                <span id="transfer-amount">${state.amount}</span>
            </div>
            <div class="text-muted small fw-bold">Available Balance: $${state.balance.toFixed(2)}</div>
        </div>

        <div class="bg-white p-3 rounded-5 shadow-lg border border-light">
            <div class="row g-0">
                ${[1, 2, 3, 4, 5, 6, 7, 8, 9, '.', 0, 'back'].map(KeypadBtn).join('')}
            </div>
            
            <div class="mt-4 px-2">
                <button class="btn btn-warning w-100 py-3 rounded-pill fw-black shadow text-uppercase ls-1" 
                        onclick="confirmTransfer()">
                    Confirm & Send
                </button>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted small opacity-50"><i class="bi bi-shield-check me-1"></i> Secured by monieFlow Protocol</p>
        </div>
    </div>

    <style>
        .fw-black { font-weight: 900; }
        .ls-1 { letter-spacing: 1.5px; }
        .cursor-pointer { cursor: pointer; }
        
        .contact-pill.active-contact img {
            border-color: #FF9900 !important;
            transform: scale(1.1);
        }
        .contact-pill.active-contact div {
            color: #FF9900 !important;
        }
        
        .transition-all { transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .border-dashed { border-style: dashed !important; }
        
        .btn-light:active {
            transform: scale(0.95);
            background-color: #eee !important;
        }
    </style>
    ${Footer({page: 'transfer'})}
    `;
}

window.confirmTransfer = () => {
    const amount = document.getElementById('transfer-amount').innerText;
    if (parseFloat(amount) <= 0) return alert("Please enter an amount.");
    alert(`Transfer of $${amount} successful!`);

};







export  function TransactionStatus(status = 'failed', data = {}) {
    // 1. DYNAMIC STATE DATA
    const config = {
        success: {
            title: "Transfer Successful!",
            subtitle: "Your crystals have been sent to the vault.",
            icon: "bi-check-all",
            color: "#2ecc71",
            bg: "rgba(46, 204, 113, 0.1)",
            actionText: "View Receipt",
            buttonClass: "btn-success"
        },
        failed: {
            title: "Transfer Failed",
            subtitle: "Insufficient gas fees or network congestion.",
            icon: "bi-exclame-octagon-fill",
            color: "#e74c3c",
            bg: "rgba(231, 76, 60, 0.1)",
            actionText: "Try Again",
            buttonClass: "btn-danger"
        }
    }[status];

    const amount = data.amount || "0.00";
    const recipient = data.recipient || "Unknown User";

    // 2. MAIN TEMPLATE
    return /* html */ `
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="card border-0 shadow-lg text-center p-5 position-relative overflow-hidden" 
             style="border-radius: 40px; max-width: 450px; width: 100%; background: #ffffff;">
            
            <div class="position-absolute top-0 start-50 translate-middle-x" 
                 style="width: 200px; height: 200px; background: ${config.bg}; filter: blur(60px); border-radius: 50%; z-index: 0; margin-top: -50px;"></div>

            <div class="status-icon-container mb-4 position-relative" style="z-index: 1;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm animate-pop" 
                     style="width: 100px; height: 100px; background: ${config.color}; color: white;">
                    <i class="bi ${config.icon}" style="font-size: 3.5rem;"></i>
                </div>
            </div>

            <div class="position-relative" style="z-index: 1;">
                <h2 class="fw-black mb-2" style="color: #232f3e;">${config.title}</h2>
                <p class="text-muted mb-4 px-3">${config.subtitle}</p>

                <div class="bg-light rounded-4 p-3 mb-4 border border-white shadow-inner">
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted fw-bold">Amount</small>
                        <span class="fw-black text-dark">$${amount}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted fw-bold">To</small>
                        <span class="fw-bold text-primary">${recipient}</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn ${config.buttonClass} py-3 rounded-pill fw-black shadow-sm text-uppercase ls-1">
                        ${config.actionText}
                    </button>
                    <button onclick="router('/home')" class="btn btn-link text-muted fw-bold text-decoration-none py-2">
                        Back to Home
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fw-black { font-weight: 900; }
        .ls-1 { letter-spacing: 1px; }
        
        /* Entrance Animations */
        .animate-pop {
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .shadow-inner {
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Responsive Tweak */
        @media (max-width: 576px) {
            .card { border-radius: 0; height: 100vh; display: flex; align-items: center; justify-content: center; }
        }
    </style>
    ${Footer({page: 'transfer'})}
    `;
}