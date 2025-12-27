<?php include __DIR__."/headers/header.php"; ?>
<div class="d-flex position-fixed start-0 top-0 end-0 py-3 justify-content-between" style="background: #ffff;">
    <h5 class="m-0"><i onclick="history.back()" class="ri-arrow-left-s-line me-2 fs-2" style="cursor: pointer"></i>FX Trade</h5>
    <!-- <a href="/transactions.php" class="text-capitalize">see all</a> -->
</div>
<div class="container p-0 mt-5 mb-5 bg-dark">
    <div class="chart-wrapper position-relative mb-4">
        <canvas id="priceChart"></canvas>
    </div>
</div>
<!-- RECENT ACTIVITY (CARD LIST) -->
<div class="container mb-5">
  <h5 class="mb-3">Recent Activity</h5>

  <div class="card bg-whitesmoke mb-2">
    <div class="card-body d-flex justify-content-between">
      <div>
        <i class="ri-send-plane-fill text-success"></i> Transfer
        <div class="small text-muted">To 0x92A...F3E</div>
      </div>
      <div class="text-end">
        <strong>-120 CC</strong>
        <div class="small text-success">Completed</div>
      </div>
    </div>
  </div>

  <div class="card bo bg-whitesmoke mb-2">
    <div class="card-body d-flex justify-content-between">
      <div>
        <i class="ri-bank-card-fill text-warning"></i> Withdraw
        <div class="small text-muted">Bank payout</div>
      </div>
      <div class="text-end">
        <strong>-300 CC</strong>
        <div class="small text-warning">Pending</div>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__."/headers/footer.php"; ?>

<style>
.chart-wrapper {
    width: 100%;
    height: 300px; 
    position: relative;
}
#priceChart {
    width: 100% !important;
    height: 100% !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('priceChart').getContext('2d');

// Gradient fill
let gradient = ctx.createLinearGradient(0,0,0,300);
gradient.addColorStop(0, 'rgba(34,197,94,0.4)');
gradient.addColorStop(1, 'rgba(34,197,94,0.05)');

const data = {
    labels: [200,300,400,500,600,700,800],
    datasets: [{
        label: 'BTC/USDT',
        data: [30,45,67,89,400,200,300],
        borderColor: '#22c55e',
        backgroundColor: gradient,
        borderWidth: 0.2,
        tension: 0.4,
        pointRadius: 0,
        fill: true
    }]
};

// Plugin to draw floating price on the right side
const floatingPricePlugin = {
    id: 'floatingPrice',
    afterDatasetsDraw(chart) {
        const ctx = chart.ctx;
        const dataset = chart.data.datasets[0];
        const meta = chart.getDatasetMeta(0);
        if(dataset.data.length === 0) return;

        const lastPoint = meta.data[meta.data.length - 1];
        const value = dataset.data[dataset.data.length - 1];
        const label = '$' + value.toFixed(2);

        // Clamp inside chart
        let y = lastPoint.y;
        const top = chart.chartArea.top + 5;
        const bottom = chart.chartArea.bottom - 5;
        if(y < top) y = top;
        if(y > bottom) y = bottom;

        const x = chart.chartArea.right - 10;

        ctx.save();
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        // Draw background
        const padding = 6;
        const textWidth = ctx.measureText(label).width;
        ctx.fillStyle = 'rgba(0,0,0,0.8)';
        ctx.fillRect(x - textWidth - padding, y - 10, textWidth + padding*2, 20);

        // Green/red based on price movement
        let color = '#22c55e';
        if(dataset.data.length > 1){
            const prev = dataset.data[dataset.data.length - 2];
            color = value >= prev ? '#22c55e' : '#ef4444';
        }
        ctx.fillStyle = color;
        ctx.fillText(label, x, y);

        ctx.restore();
    }
};


const priceChart = new Chart(ctx, {
    type: 'line',
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#020617',
                titleColor: '#e5e7eb',
                bodyColor: '#e5e7eb',
                displayColors: false
            }
        },
        scales: {
    x: { 
        grid: { display: false, drawTicks: false, drawBorder: false }, 
        ticks: { display: false } 
    },
    y: { 
        grid: { display: false, drawTicks: false, drawBorder: false }, 
        ticks: { display: false } 
    }
}
    },
    plugins: [floatingPricePlugin]
});



function addData(chart, label, value){
    chart.data.labels.push(label);
    chart.data.datasets[0].data.push(value);

    // Keep last 20 points visible
    if(chart.data.labels.length > 20){
        chart.data.labels.shift();
        chart.data.datasets[0].data.shift();
    }

    // Dynamic Y-axis based on visible points
    const visibleData = chart.data.datasets[0].data;
    const min = Math.min(...visibleData);
    const max = Math.max(...visibleData);
    chart.options.scales.y.min = min - (max-min)*0.1;
    chart.options.scales.y.max = max + (max-min)*0.1;

    chart.update('none'); // no animation for smooth scrolling
}

// Auto-update every second
setInterval(()=>{
    const now = new Date();
    const timeLabel = now.getHours()+':'+String(now.getMinutes()).padStart(2,'0')+':'+String(now.getSeconds()).padStart(2,'0');

    let lastPrice = priceChart.data.datasets[0].data.slice(-1)[0] || 42500;
    let change = (Math.random()-0.5)*50; 
    let newPrice = Math.max(0, lastPrice+change);

    addData(priceChart, timeLabel, newPrice);
},1000);
</script>
