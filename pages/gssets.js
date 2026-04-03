import { Feed } from "../helperJs/helperfeeds.js";
import { Footer } from "./feeds.js";

export default async function Assets() {
    // 1. ASSET STATE
    const state = {
        totalValue: 12450.65,
        dailyChange: "+4.25%",
        holdings: [
            { id: 1, name: "Aqua Aura Quartz", symbol: "AAQ", balance: "1.24", value: 3450.00, change: "+12%", color: "#00d4ff" },
            { id: 2, name: "Solar Citrine", symbol: "SCIT", balance: "45.00", value: 1200.50, change: "-2.1%", color: "#ffb700" },
            { id: 3, name: "Void Obsidian", symbol: "VOD", balance: "0.50", value: 5600.15, change: "+24.8%", color: "#4e4e4e" },
            { id: 4, name: "Rose Quartz", symbol: "ROSE", balance: "120.0", value: 2200.00, change: "+0.5%", color: "#ff85a1" }
        ]
    };

    // 2. UI FRAGMENTS
    const AssetRow = (asset) => `
        <div class="card border-0 mb-3 shadow-sm hover-scale cursor-pointer" style="border-radius: 20px; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-sm" 
                         style="width: 50px; height: 50px; background: ${asset.color}22; color: ${asset.color}">
                        <i class="bi bi-gem fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-black mb-0 text-dark">${asset.name}</h6>
                        <small class="text-muted fw-bold">${asset.balance} ${asset.symbol}</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-black text-dark">$${asset.value.toLocaleString()}</div>
                    <small class="${asset.change.startsWith('+') ? 'text-success' : 'text-danger'} fw-bold" style="font-size: 0.75rem;">
                        ${asset.change} <i class="bi ${asset.change.startsWith('+') ? 'bi-caret-up-fill' : 'bi-caret-down-fill'}"></i>
                    </small>
                </div>
            </div>
        </div>
    `;

    // 3. MAIN TEMPLATE
    return /* html */ `
    <div class="container py-4 px-md-5" style="max-width: 900px; min-height: 100vh;">
        
        <div class="card border-0 rounded-5 mb-5 shadow-lg overflow-hidden" style="background: linear-gradient(135deg, #1a1a1a 0%, #3a3a3a 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-4 mb-md-0 text-center text-md-start">
                        <span class="text-white-50 small fw-bold text-uppercase ls-1">Total Vault Value</span>
                        <h1 class="display-5 text-white fw-black mb-2 mt-1">$${state.totalValue.toLocaleString()}</h1>
                        <div class="badge bg-success rounded-pill px-3 py-2 fw-bold" style="font-size: 0.8rem;">
                            <i class="bi bi-graph-up-arrow me-1"></i> ${state.dailyChange} Today
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-end justify-content-around gap-2" style="height: 100px;">
                            ${[40, 60, 45, 80, 55, 90, 100].map((h, i) => `
                                <div class="bg-primary opacity-${i === 6 ? '100' : '25'} rounded-pill" 
                                     style="width: 12%; height: ${h}%; transition: height 1s ease;"></div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white bg-opacity-10 py-3 border-top border-white border-opacity-10">
                <div class="d-flex justify-content-around">
                    <button class="btn btn-link text-white text-decoration-none fw-bold small"><i class="bi bi-plus-circle me-2"></i>Deposit</button>
                    <button class="btn btn-link text-white text-decoration-none fw-bold small"><i class="bi bi-arrow-up-right-circle me-2"></i>Withdraw</button>
                    <button class="btn btn-link text-white text-decoration-none fw-bold small"><i class="bi bi-arrow-left-right me-2"></i>Swap</button>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-black mb-0">Your Assets</h4>
            <div class="dropdown">
                <button class="btn btn-white bg-white shadow-sm rounded-pill btn-sm px-3 dropdown-toggle fw-bold text-muted border-0" data-bs-toggle="dropdown">
                    High Value
                </button>
            </div>
        </div>

        <div id="assets-list">
            ${state.holdings.map(AssetRow).join('')}
        </div>

        <div class="card border-0 shadow-sm p-4 mt-5" style="border-radius: 30px;">
            <h6 class="fw-black mb-4">Allocation Distribution</h6>
            <div class="progress rounded-pill" style="height: 12px;">
                ${state.holdings.map(a => `
                    <div class="progress-bar" role="progressbar" 
                         style="width: ${Math.random() * 25 + 10}%; background-color: ${a.color};" 
                         title="${a.name}"></div>
                `).join('')}
            </div>
            <div class="d-flex flex-wrap gap-3 mt-3">
                ${state.holdings.map(a => `
                    <div class="d-flex align-items-center gap-1">
                        <span class="rounded-circle" style="width: 8px; height: 8px; background: ${a.color}"></span>
                        <small class="text-muted fw-bold" style="font-size: 0.65rem;">${a.symbol}</small>
                    </div>
                `).join('')}
            </div>
        </div>
    </div>

    <style>
        .fw-black { font-weight: 900; }
        .ls-1 { letter-spacing: 1px; }
        .cursor-pointer { cursor: pointer; }
        
        .hover-scale {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .hover-scale:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
            background: #ffffff !important;
        }

        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
        
        ::-webkit-scrollbar { display: none; }
    </style>
    ${Footer({page: 'assets'})}
    `;
}