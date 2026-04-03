import { Feed } from "../helperJs/helperfeeds.js";

export default async function Trading() {
    // 1. APP STATE
    const state = {
        balance: 2540.50,
        mode: 'buy', // 'buy' or 'sell'
        cart: [],
        items: [
            { id: 1, name: "Aqua Aura", price: 150.25, change: "+5.4%", stock: 8, color: "#00d4ff" },
            { id: 2, name: "Solar Citrine", price: 85.00, change: "-2.1%", stock: 15, color: "#ffb700" },
            { id: 3, name: "Void Obsidian", price: 320.10, change: "+12.8%", stock: 3, color: "#4e4e4e" },
            { id: 4, name: "Rose Quartz", price: 45.00, change: "+0.5%", stock: 25, color: "#ff85a1" }
        ]
    };

    // 2. CORE LOGIC (Cart & Transactions)
    window.handleTrade = (id) => {
        const product = state.items.find(i => i.id === id);
        const cartItem = state.cart.find(i => i.id === id);

        if (state.mode === 'buy') {
            if (product.stock <= 0) return alert("Out of Stock!");
            if (cartItem) {
                cartItem.qty++;
            } else {
                state.cart.push({ ...product, qty: 1 });
            }
            product.stock--;
        }
        updateUI();
    };

    window.removeFromCart = (id) => {
        const index = state.cart.findIndex(i => i.id === id);
        if (index > -1) {
            const product = state.items.find(i => i.id === id);
            product.stock += state.cart[index].qty;
            state.cart.splice(index, 1);
        }
        updateUI();
    };

    // 3. UI FRAGMENTS
    const renderMarket = () => state.items.map(item => `
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3">
                            <div class="rounded-4 d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background: ${item.color}22; color: ${item.color}">
                                <i class="bi bi-gem fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">${item.name}</h6>
                                <small class="text-muted">Stock: ${item.stock}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-black text-dark">$${item.price}</div>
                            <small class="${item.change.startsWith('+') ? 'text-success' : 'text-danger'} fw-bold" style="font-size: 0.7rem;">
                                ${item.change} <i class="bi bi-graph-up"></i>
                            </small>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button onclick="handleTrade(${item.id})" class="btn btn-dark btn-sm rounded-pill flex-grow-1 fw-bold">
                            ${state.mode === 'buy' ? 'Add to Cart' : 'List Item'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    const renderCart = () => {
        const total = state.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        return `
            <div class="p-4 rounded-5 bg-white shadow-lg border border-light">
                <h5 class="fw-black mb-4 d-flex justify-content-between">
                    Your Briefcase
                    <span class="badge bg-warning text-dark rounded-pill fs-6">${state.cart.length}</span>
                </h5>
                
                <div class="cart-items mb-4" style="max-height: 300px; overflow-y: auto;">
                    ${state.cart.length === 0 ? '<p class="text-center text-muted my-5">No assets selected</p>' : 
                        state.cart.map(item => `
                        <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded-3 border-bottom border-light">
                            <div>
                                <div class="fw-bold small">${item.name}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">$${item.price} x ${item.qty}</div>
                            </div>
                            <button onclick="removeFromCart(${item.id})" class="btn btn-link text-danger p-0"><i class="bi bi-x-circle-fill"></i></button>
                        </div>
                    `).join('')}
                </div>

                <div class="bg-light p-3 rounded-4 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Subtotal</small>
                        <span class="fw-bold">$${total.toFixed(2)}</span>
                    </div>
                    <div class="d-flex justify-content-between text-primary">
                        <small class="fw-bold">Gas Fee (Fixed)</small>
                        <span class="fw-bold">$2.50</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-black fs-5">Total</span>
                        <span class="fw-black fs-5 text-dark">$${(total + 2.5).toFixed(2)}</span>
                    </div>
                </div>

                <button class="btn btn-warning w-100 py-3 rounded-pill fw-black shadow text-uppercase" 
                        style="letter-spacing: 1px" onclick="alert('Transaction Signed!')">
                    Confirm Order
                </button>
            </div>
        `;
    };

    // 4. THE LIVE RE-RENDERER
    const updateUI = () => {
        const grid = document.getElementById('market-grid');
        const cart = document.getElementById('cart-container');
        if(grid) grid.innerHTML = renderMarket();
        if(cart) cart.innerHTML = renderCart();
    };

    // 5. INITIAL TEMPLATE
    return /* html */ `
    <div class="container px-0 py-4 px-md-5" style="background: #f8f9fa; min-height: 100vh;">
        <div class="row g-4">
            <aside class="col-lg-2">
                <div class="sticky-top" style="top: 20px;">
                    <div class="mb-5 px-3">
                        <h3 class="fw-black text-primary">monie<span class="text-warning">Flow</span></h3>
                    </div>
                    <nav class="nav flex-column gap-2">
                        <a class="nav-link active bg-white shadow-sm rounded-pill fw-bold p-3 text-dark" href="#"><i class="bi bi-grid-fill me-2"></i> Marketplace</a>
                        <a class="nav-link text-muted fw-bold p-3" href="#"><i class="bi bi-pie-chart-fill me-2"></i> Portfolio</a>
                        <a class="nav-link text-muted fw-bold p-3" href="#"><i class="bi bi-arrow-left-right me-2"></i> History</a>
                        <a class="nav-link text-muted fw-bold p-3" href="#"><i class="bi bi-gear-fill me-2"></i> Settings</a>
                    </nav>
                </div>
            </aside>

            <main class="col-lg-7">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-black mb-0">Trading Floor</h2>
                        <p class="text-muted small">Live crystal market pricing and analytics</p>
                    </div>
                    <div class="bg-white p-1 rounded-pill shadow-sm border">
                        <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">Buy</button>
                        <button class="btn btn-white border-0 rounded-pill px-4 fw-bold text-muted">Sell</button>
                    </div>
                </div>

                <div class="row px-0" id="market-grid">
                    ${renderMarket()}
                </div>
            </main>

            <aside class="col-lg-3">
                <div class="sticky-top" style="top: 20px;">
                    <div class="card border-0 rounded-5 mb-4 shadow-sm" style="background: linear-gradient(135deg, #1e1e1e, #3a3a3a);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="text-white-50 small fw-bold">ESTIMATED BALANCE</span>
                                <i class="bi bi-stars text-warning fs-4"></i>
                            </div>
                            <h2 class="text-white fw-black mb-1">$${state.balance.toLocaleString()}</h2>
                            <div class="text-success small fw-bold"><i class="bi bi-caret-up-fill"></i> +12.5% ($240.20)</div>
                        </div>
                    </div>

                    <div id="cart-container">
                        ${renderCart()}
                    </div>
                </div>
            </aside>
        </div>
    </div>
    `;
}