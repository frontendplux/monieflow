<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Data Transition Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary-flow: #6366f1;
            --bg-dark: #0f172a;
        }

        body { 
            background-color: var(--bg-dark); 
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        /* Split Screen Container */
        .split-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* LEFT: Data Entry Panel (Glassmorphism) */
        .entry-panel {
            width: 400px;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255,255,255,0.1);
            padding: 40px;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* RIGHT: Visualization Panel */
        .display-panel {
            flex-grow: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        }

        /* Transition Animations */
        .data-card {
            background: #fff;
            color: #000;
            border-radius: 24px;
            padding: 30px;
            width: 350px;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .data-card.active {
            transform: translateY(0);
            opacity: 1;
        }

        /* Input Styling */
        .flow-input {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white !important;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }

        .flow-input:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--primary-flow);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        .btn-flow {
            background: var(--primary-flow);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }

        .btn-flow:hover {
            transform: scale(1.02);
            filter: brightness(1.1);
        }
    </style>
</head>
<body>

<div class="split-wrapper">
    <div class="entry-panel">
        <div class="mb-5">
            <h2 class="fw-bold mb-1">Enter Data</h2>
            <p class="text-muted small">Watch it transition to the live view.</p>
        </div>

        <form id="transitionForm">
            <label class="small fw-bold text-uppercase mb-2 opacity-50">Transaction Title</label>
            <input type="text" id="inputTitle" class="form-control flow-input" placeholder="e.g. BTC Purchase">

            <label class="small fw-bold text-uppercase mb-2 opacity-50">Amount (MC)</label>
            <input type="number" id="inputAmount" class="form-control flow-input" placeholder="0.00">

            <label class="small fw-bold text-uppercase mb-2 opacity-50">Category</label>
            <select id="inputCat" class="form-select flow-input">
                <option value="Trading">Trading</option>
                <option value="The Barn">The Barn</option>
                <option value="Arena">Arena</option>
            </select>

            <button type="button" onclick="triggerTransition()" class="btn btn-primary btn-flow w-100 mt-3 shadow-lg">
                Execute Transition <i class="ri-arrow-right-line ms-2"></i>
            </button>
        </form>
    </div>

    <div class="display-panel">
        <div class="position-absolute w-100 h-100 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div id="displayCard" class="data-card shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span id="cardCat" class="badge bg-primary-subtle text-primary rounded-pill px-3">Category</span>
                <i class="ri-verified-badge-fill text-primary fs-3"></i>
            </div>
            <h6 class="text-muted small mb-1">Confirmed Entry</h6>
            <h3 id="cardTitle" class="fw-bold mb-4">Your Title</h3>
            <div class="p-3 bg-light rounded-4 d-flex justify-content-between align-items-center">
                <span class="fw-bold">Amount</span>
                <span id="cardAmount" class="h4 fw-black mb-0 text-primary">0.00 MC</span>
            </div>
            <div class="mt-4 text-center">
                <small class="text-muted"><i class="ri-time-line"></i> Validated just now</small>
            </div>
        </div>
    </div>
</div>

<script>
    function triggerTransition() {
        // 1. Get Data
        const title = document.getElementById('inputTitle').value || "Unnamed Entry";
        const amount = document.getElementById('inputAmount').value || "0.00";
        const cat = document.getElementById('inputCat').value;

        // 2. Reset Animation
        const card = document.getElementById('displayCard');
        card.classList.remove('active');

        // 3. Update Content with slight delay
        setTimeout(() => {
            document.getElementById('cardTitle').innerText = title;
            document.getElementById('cardAmount').innerText = amount + " MC";
            document.getElementById('cardCat').innerText = cat;
            
            // 4. Trigger Entry Animation
            card.classList.add('active');
        }, 300);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>