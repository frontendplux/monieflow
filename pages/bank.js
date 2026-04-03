import { Feed } from "../helperJs/helperfeeds.js";
import { Footer } from "./feeds.js";

export default async function Bank() {
    // 1. BANKING STATE
    const state = {
        totalBalance: 4520.00,
        linkedCards: [
            { id: 1, bank: "JPMorgan Chase", last4: "8824", type: "Visa", color: "linear-gradient(135deg, #0046be, #002b75)" },
            { id: 2, bank: "Goldman Sachs", last4: "1102", type: "Mastercard", color: "linear-gradient(135deg, #1d1d1f, #434345)" }
        ],
        recentTransactions: [
            { id: 101, title: "Withdraw to Chase", amount: -500.00, date: "Mar 24", status: "Completed" },
            { id: 102, title: "Deposit from Mastercard", amount: 1200.00, date: "Mar 22", status: "Completed" },
            { id: 103, title: "Crystal Sale #882", amount: 45.50, date: "Mar 21", status: "Pending" }
        ]
    };

    // 2. UI FRAGMENTS
    const CreditCard = (card) => `
        <div class="col-10 col-md-6">
            <div class="p-4 rounded-5 text-white shadow-lg position-relative overflow-hidden mb-3" 
                 style="background: ${card.color}; min-height: 180px; transition: transform 0.3s ease;">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <i class="bi bi-chip fs-2 text-warning"></i>
                    <span class="fw-black italic" style="font-size: 1.2rem;">${card.type}</span>
                </div>
                <div class="mt-auto">
                    <div class="fs-4 fw-bold mb-1">**** **** **** ${card.last4}</div>
                    <small class="opacity-75 text-uppercase ls-1">${card.bank}</small>
                </div>
                <div class="position-absolute end-0 bottom-0 p-3 opacity-25">
                    <i class="bi bi-wifi fs-1 rotate-90"></i>
                </div>
            </div>
        </div>
    `;

    // 3. MAIN TEMPLATE
    return /* html */ `
    <div class="container py-4 px-md-5" style="max-width: 900px;">
        
        <div class="text-center mb-5 mt-3">
            <span class="text-muted fw-bold text-uppercase ls-1 small">Total Fiat Balance</span>
            <h1 class="display-4 fw-black text-dark mb-3 mt-1">$${state.totalBalance.toLocaleString()}</h1>
            <div class="d-flex justify-content-center gap-2">
                <button onclick="router('/deposit')" class="btn btn-warning rounded-pill px-4 fw-black shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Add Funds
                </button>
                <button onclick="router('/withdraw')" class="btn btn-white bg-white border-0 shadow-sm rounded-pill px-4 fw-bold text-dark">
                    Withdraw
                </button>
            </div>
        </div>

        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                <h5 class="fw-black mb-0">Linked Methods</h5>
                <button class="btn btn-link text-primary fw-bold text-decoration-none p-0 small">+ Add New</button>
            </div>
            
            <div class="row flex-nowrap overflow-auto pb-3 px-2" style="scrollbar-width: none;">
                ${state.linkedCards.map(CreditCard).join('')}
            </div>
        </div>

        <div class="card border-0 shadow-lg p-4" style="border-radius: 35px;">
            <h5 class="fw-black mb-4">Recent Activity</h5>
            
            <div class="list-group list-group-flush">
                ${state.recentTransactions.map(t => `
                    <div class="list-group-item border-0 bg-transparent px-0 py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi ${t.amount > 0 ? 'bi-arrow-down-left text-success' : 'bi-arrow-up-right text-primary'} fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">${t.title}</h6>
                                <small class="text-muted">${t.date} • <span class="${t.status === 'Pending' ? 'text-warning' : ''}">${t.status}</span></small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-black ${t.amount > 0 ? 'text-success' : 'text-dark'}">
                                ${t.amount > 0 ? '+' : ''}$${Math.abs(t.amount).toFixed(2)}
                            </span>
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <button class="btn btn-light w-100 mt-3 rounded-pill fw-bold text-muted py-2 border-0">View Full Statement</button>
        </div>

        <div class="text-center mt-5 opacity-50">
            <div class="d-flex align-items-center justify-content-center gap-2 small fw-bold">
                <i class="bi bi-shield-lock-fill text-success"></i>
                BANK-GRADE ENCRYPTION ACTIVE
            </div>
        </div>
    </div>

    <style>
        .fw-black { font-weight: 900; }
        .ls-1 { letter-spacing: 1px; }
        .rotate-90 { transform: rotate(90deg); display: inline-block; }
        
        .list-group-item:hover {
            background-color: #f8f9fa !important;
            border-radius: 15px;
        }

        /* Card Hover Effect */
        .col-10:hover .p-4 {
            transform: translateY(-5px) scale(1.02);
            cursor: pointer;
        }

        ::-webkit-scrollbar { display: none; }
    </style>
    ${Footer({page: 'bank'})}
    `;
}