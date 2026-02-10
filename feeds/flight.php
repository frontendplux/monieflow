<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Flight Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        
        /* 1. Header Search Bar */
        .flight-search-header {
            background: #1877f2;
            padding: 30px 0 60px 0;
            margin-bottom: -30px;
        }

        .search-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* 2. Flight Ticket Component */
        .ticket-card {
            border: none;
            border-radius: 16px;
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .ticket-card:hover {
            transform: scale(1.01);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
        }

        /* Flight Timeline Line */
        .flight-timeline {
            position: relative;
            height: 2px;
            background: #dee2e6;
            width: 100%;
            margin: 10px 0;
        }

        .flight-timeline::before, .flight-timeline::after {
            content: '';
            position: absolute;
            top: -4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #1877f2;
        }
        .flight-timeline::after { right: 0; }

        .plane-icon {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 10px;
            color: #1877f2;
        }

        /* 3. Filter Sidebar */
        .filter-section-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<section class="flight-search-header">
    <div class="container">
        <div class="search-card">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0"><i class="ri-map-pin-2-fill text-primary"></i></span>
                        <input type="text" class="form-control border-0 fw-bold" value="Lagos (LOS)">
                    </div>
                </div>
                <div class="col-md-1 text-center d-none d-md-block">
                    <i class="ri-arrow-left-right-line text-muted"></i>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0"><i class="ri-map-pin-2-line text-primary"></i></span>
                        <input type="text" class="form-control border-0 fw-bold" placeholder="Destination">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group border-start ps-3">
                        <span class="input-group-text bg-transparent border-0"><i class="ri-calendar-event-line text-primary"></i></span>
                        <input type="text" class="form-control border-0 fw-bold" value="Feb 12, 2026">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-dark w-100 rounded-pill py-2 fw-bold">Modify</button>
                </div>
            </div>
        </div>
    </div>
</section>

<main class="container py-5 mt-4">
    <div class="row g-4">
        
        <aside class="col-lg-3">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                <div class="filter-section-title">Stops</div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="direct" checked>
                    <label class="form-check-label d-flex justify-content-between w-100" for="direct">
                        Direct <span class="text-muted small">₦85k</span>
                    </label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="onestop">
                    <label class="form-check-label d-flex justify-content-between w-100" for="onestop">
                        1 Stop <span class="text-muted small">₦62k</span>
                    </label>
                </div>

                <div class="filter-section-title">Airlines</div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="airpeace" checked>
                    <label class="form-check-label small" for="airpeace">Air Peace</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="ibom">
                    <label class="form-check-label small" for="ibom">Ibom Air</label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="arik">
                    <label class="form-check-label small" for="arik">Arik Air</label>
                </div>

                <div class="filter-section-title">Price Range</div>
                <input type="range" class="form-range" min="50000" max="300000">
                <div class="d-flex justify-content-between small fw-bold mt-2">
                    <span>₦50k</span>
                    <span>₦300k+</span>
                </div>
            </div>
        </aside>

        <div class="col-lg-9">
            
            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                <p class="mb-0 text-muted fw-semibold">8 Flights found</p>
                <div class="btn-group shadow-sm bg-white rounded-pill p-1">
                    <button class="btn btn-sm btn-dark rounded-pill px-3">Cheapest</button>
                    <button class="btn btn-sm btn-white rounded-pill px-3">Fastest</button>
                </div>
            </div>

            <div class="card ticket-card shadow-sm p-4 mb-4">
                <div class="row align-items-center text-center text-md-start">
                    <div class="col-md-2 mb-3 mb-md-0">
                        <img src="https://via.placeholder.com/50x50/1877f2/ffffff?text=AP" class="rounded mb-2" alt="Air Peace">
                        <div class="small fw-bold">Air Peace</div>
                    </div>

                    <div class="col-md-2">
                        <div class="h5 fw-bold mb-0">08:30</div>
                        <div class="text-muted small">LOS</div>
                    </div>

                    <div class="col-md-3 px-4">
                        <div class="small text-muted text-center mb-1">1h 15m (Direct)</div>
                        <div class="flight-timeline">
                            <i class="ri-plane-fill plane-icon"></i>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="h5 fw-bold mb-0">09:45</div>
                        <div class="text-muted small">ABV</div>
                    </div>

                    <div class="col-md-3 text-md-end border-start ps-md-4 mt-3 mt-md-0">
                        <div class="text-muted small mb-1">Price per adult</div>
                        <div class="h4 fw-bold text-primary mb-3">₦78,400</div>
                        <button class="btn btn-primary rounded-pill w-100 fw-bold">View Deal</button>
                    </div>
                </div>
            </div>

            <div class="card ticket-card shadow-sm p-4 mb-4">
                <div class="row align-items-center text-center text-md-start">
                    <div class="col-md-2 mb-3 mb-md-0">
                        <img src="https://via.placeholder.com/50x50/00d2ff/ffffff?text=IB" class="rounded mb-2" alt="Ibom Air">
                        <div class="small fw-bold">Ibom Air</div>
                    </div>
                    <div class="col-md-2">
                        <div class="h5 fw-bold mb-0">11:15</div>
                        <div class="text-muted small">LOS</div>
                    </div>
                    <div class="col-md-3 px-4">
                        <div class="small text-muted text-center mb-1">1h 10m (Direct)</div>
                        <div class="flight-timeline">
                            <i class="ri-plane-fill plane-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="h5 fw-bold mb-0">12:25</div>
                        <div class="text-muted small">ABV</div>
                    </div>
                    <div class="col-md-3 text-md-end border-start ps-md-4 mt-3 mt-md-0">
                        <div class="text-muted small mb-1">Price per adult</div>
                        <div class="h4 fw-bold text-primary mb-3">₦82,100</div>
                        <button class="btn btn-primary rounded-pill w-100 fw-bold">View Deal</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>