import { Feed } from "../helperJs/helperfeeds.js";
import { Footer } from "./feeds.js";

// export default async function Trading() {
//     return /* html */ `
//     <div class="container px-lg-5">
//     <div class="row g-4">
        
//         <aside class="col-lg-3">
//             <div class="sticky-top" style="top: 85px;">
//                 <div class="card border-0 shadow-sm rounded-3 mb-3">
//                     <div class="card-body">
//                         <div class="d-flex align-items-center gap-3 mb-3">
//                             <span class="fs-1">₿</span>
//                             <div>
//                                 <h4 class="mb-0 fw-bold">Bitcoin</h4>
//                                 <span class="badge bg-secondary-subtle text-dark border">BTC</span>
//                             </div>
//                         </div>
//                         <h2 class="fw-bold mb-1">$64,120.50</h2>
//                         <div class="text-success small fw-bold">▲ 2.45% (24h)</div>
//                     </div>
//                 </div>

//                 <div class="card border-0 shadow-sm rounded-3">
//                     <div class="card-body p-0">
//                         <div class="p-3 border-bottom fw-bold small text-uppercase text-muted">Market Stats</div>
//                         <div class="p-3">
//                             <div class="d-flex justify-content-between mb-2 small">
//                                 <span class="text-muted">Market Cap</span>
//                                 <span class="fw-bold">$1.2T</span>
//                             </div>
//                             <div class="d-flex justify-content-between mb-2 small">
//                                 <span class="text-muted">Volume (24h)</span>
//                                 <span class="fw-bold">$35.2B</span>
//                             </div>
//                             <div class="d-flex justify-content-between small">
//                                 <span class="text-muted">Circulating Supply</span>
//                                 <span class="fw-bold">19.6M BTC</span>
//                             </div>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </aside>

//         <main class="col-lg-6">
//             <div class="card border-0 shadow-sm rounded-3 mb-4">
//                 <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
//                     <h6 class="fw-bold mb-0">Price History (USD)</h6>
//                     <div class="btn-group btn-group-sm">
//                         <button class="btn btn-outline-secondary px-3">1H</button>
//                         <button class="btn btn-outline-secondary px-3 active">1D</button>
//                         <button class="btn btn-outline-secondary px-3">1W</button>
//                         <button class="btn btn-outline-secondary px-3">1M</button>
//                     </div>
//                 </div>
//                 <div class="card-body">
//                     <canvas id="cryptoChart" style="width: 100%; height: 350px;"></canvas>
//                 </div>
//             </div>

//             <div class="card border-0 shadow-sm rounded-3">
//                 <div class="card-body">
//                     <h6 class="fw-bold mb-3">About Bitcoin</h6>
//                     <p class="small text-muted" style="line-height: 1.6;">
//                         Bitcoin is a decentralized digital currency, without a central bank or single administrator, that can be sent from user to user on the peer-to-peer bitcoin network without the need for intermediaries.
//                     </p>
//                     <a href="#" class="text-primary small text-decoration-none">Read more on Amazon Learn ›</a>
//                 </div>
//             </div>
//         </main>

//         <aside class="col-lg-3">
//             <div class="sticky-top" style="top: 85px;">
//                 <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
//                     <div class="card-header bg-dark text-white fw-bold py-3">Buy Bitcoin</div>
//                     <div class="card-body">
//                         <label class="form-label small fw-bold">Spend</label>
//                         <div class="input-group mb-3">
//                             <span class="input-group-text bg-white border-end-0">$</span>
//                             <input type="number" class="form-control border-start-0" placeholder="0.00">
//                         </div>
                        
//                         <label class="form-label small fw-bold">Receive</label>
//                         <div class="input-group mb-4">
//                             <input type="text" class="form-control border-end-0" placeholder="0.00" disabled>
//                             <span class="input-group-text bg-white border-start-0">BTC</span>
//                         </div>

//                         <button class="btn btn-warning w-100 fw-bold py-2 rounded-pill shadow-sm mb-2">Buy Now</button>
//                         <button class="btn btn-outline-dark w-100 fw-bold py-2 rounded-pill">Sell BTC</button>
                        
//                         <p class="text-center text-muted mt-3" style="font-size: 11px;">
//                             Transaction Fee: <span class="fw-bold text-dark">$0.99</span>
//                         </p>
//                     </div>
//                 </div>
//             </div>
//         </aside>

//     </div>
// </div>
// ${Footer({page:"trade"})}
// `
// }


// export function cartjsfunction(){
//     // CHART.JS INITIALIZATION
//     const ctx = document.getElementById('cryptoChart').getContext('2d');
    
//     // Create a gradient for the flow
//     const gradient = ctx.createLinearGradient(0, 0, 0, 400);
//     gradient.addColorStop(0, 'rgba(255, 153, 0, 0.2)');
//     gradient.addColorStop(1, 'rgba(255, 153, 0, 0)');

//     new Chart(ctx, {
//         type: 'line',
//         data: {
//             labels: ['12am', '4am', '8am', '12pm', '4pm', '8pm', '11pm'],
//             datasets: [{
//                 label: 'Price',
//                 data: [62500, 63100, 62800, 64200, 63800, 64500, 64120],
//                 borderColor: '#FF9900', // Amazon Orange
//                 borderWidth: 3,
//                 fill: true,
//                 backgroundColor: gradient,
//                 tension: 0.4, // Smooth curve
//                 pointRadius: 0,
//                 pointHoverRadius: 6,
//                 pointHoverBackgroundColor: '#FF9900'
//             }]
//         },
//         options: {
//             responsive: true,
//             maintainAspectRatio: false,
//             plugins: {
//                 legend: { display: false }
//             },
//             scales: {
//                 x: { grid: { display: false } },
//                 y: { 
//                     grid: { color: '#f0f0f0' },
//                     ticks: { callback: (value) => '$' + value.toLocaleString() }
//                 }
//             }
//         }
//     });
// }


export default function Trading() {
  return `
<div class="container-fluid py-5 bg-info" style=" min-height: 100vh;">
  <div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-dark border-opacity-10 pb-4">
      <h2 class="fw-light m-0">Market <span class="fw-bold">Pulse</span></h2>
      <div class="text-end">
        <small class="text-white-50 d-block">Global Cap</small>
        <span class="fw-bold">$2.48T <span class="text-success" style="font-size: 0.8rem;">+1.2%</span></span>
      </div>
    </div>

    <div class="row g-0">
      <div class="col-12">
        <div class="table-responsive">
          <table class="table table-dark table-hover align-middle mb-0" style="--bs-table-bg: transparent;">
            <thead>
              <tr class="text-white-50 border-bottom border-white border-opacity-10" style="font-size: 0.75rem; letter-spacing: 2px;">
                <th class="py-3 px-4">ASSET</th>
                <th class="py-3 text-end">PRICE</th>
                <th class="py-3 text-end">CHANGE</th>
                <th class="py-3 text-center">7D CHART</th>
                <th class="py-3 text-end px-4">ACTION</th>
              </tr>
            </thead>
            <tbody>
              
              <tr class="border-bottom border-white border-opacity-5">
                <td class="py-4 px-4">
                  <div class="d-flex align-items-center">
                    <div class="border border-white border-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">B</div>
                    <div>
                      <div class="fw-bold">Bitcoin</div>
                      <small class="text-white-50">BTC</small>
                    </div>
                  </div>
                </td>
                <td class="text-end fw-bold text-white">$68,432.10</td>
                <td class="text-end"><span class="text-success">+3.24%</span></td>
                <td class="text-center" style="width: 180px;">
                  <canvas id="chart-btc" height="40"></canvas>
                </td>
                <td class="text-end px-4">
                  <button class="btn btn-sm btn-white bg-white text-black rounded-0 px-3 fw-bold">TRADE</button>
                </td>
              </tr>

              <tr class="border-bottom border-white border-opacity-5">
                <td class="py-4 px-4">
                  <div class="d-flex align-items-center">
                    <div class="border border-white border-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">E</div>
                    <div>
                      <div class="fw-bold">Ethereum</div>
                      <small class="text-white-50">ETH</small>
                    </div>
                  </div>
                </td>
                <td class="text-end fw-bold text-white">$3,842.15</td>
                <td class="text-end"><span class="text-danger">-1.12%</span></td>
                <td class="text-center">
                  <canvas id="chart-eth" height="40"></canvas>
                </td>
                <td class="text-end px-4">
                  <button class="btn btn-sm btn-outline-light rounded-0 px-3 fw-bold">TRADE</button>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
`;
}

export function cartjsfunction() {
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { enabled: false } },
    scales: { x: { display: false }, y: { display: false } },
    elements: { line: { tension: 0.4 }, point: { radius: 0 } }
  };

  setTimeout(() => {
    const btcCtx = document.getElementById('chart-btc')?.getContext('2d');
    if(btcCtx) new Chart(btcCtx, {
      type: 'line',
      data: {
        labels: [1, 2, 3, 4, 5, 6, 7],
        datasets: [{ data: [65, 62, 67, 63, 68, 66, 70], borderColor: '#fff', borderWidth: 1.5, fill: false }]
      },
      options: chartOptions
    });

    const ethCtx = document.getElementById('chart-eth')?.getContext('2d');
    if(ethCtx) new Chart(ethCtx, {
      type: 'line',
      data: {
        labels: [1, 2, 3, 4, 5, 6, 7],
        datasets: [{ data: [40, 38, 39, 37, 38, 39, 38], borderColor: '#ffffff55', borderWidth: 1.5, fill: false }]
      },
      options: chartOptions
    });
  }, 100);
}
