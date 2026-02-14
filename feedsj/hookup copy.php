<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect | Discovery Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .profile-card {
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
        }
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid #fff;
        }
        .online { background-color: #2ecc71; }
        .avatar-img {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .btn-connect {
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="ri-heart-pulse-line me-2"></i>LinkUp</a>
        <div class="ms-auto text-white">
            <i class="ri-notification-3-line fs-5 me-3"></i>
            <i class="ri-user-smile-line fs-5"></i>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">Discover New People</h2>
            <p class="text-muted">Browse members active in your area right now.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <button class="btn btn-outline-dark dropdown-toggle" type="button">Filter: Nearby</button>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card profile-card h-100 shadow-sm">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=400" class="avatar-img" alt="User">
                    <span class="position-absolute top-0 end-0 m-3 badge bg-dark opacity-75">2.4 km</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0 fw-bold">Sarah, 24</h5>
                        <span class="status-indicator online"></span>
                    </div>
                    <p class="card-text text-muted small">Loves hiking, tech, and deep conversations. Let's grab coffee!</p>
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-primary btn-connect" data-bs-toggle="modal" data-bs-target="#bookingModal">
                            <i class="ri-calendar-check-line me-2"></i>Book Meeting
                        </button>
                        <button class="btn btn-outline-secondary btn-connect">
                            <i class="ri-message-3-line me-2"></i>Message
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card profile-card h-100 shadow-sm">
                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=400" class="avatar-img" alt="User">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0 fw-bold">Marcus, 28</h5>
                        <span class="status-indicator online"></span>
                    </div>
                    <p class="card-text text-muted small">Freelance designer looking for creative collaborators.</p>
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-primary btn-connect">Book Meeting</button>
                        <button class="btn btn-outline-secondary btn-connect">Message</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Request a Connection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Choose Date</label>
                    <input type="date" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Meeting Type</label>
                    <select class="form-select">
                        <option>Casual Coffee</option>
                        <option>Professional Lunch</option>
                        <option>Online Video Call</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Intro Message</label>
                    <textarea class="form-control" rows="3" placeholder="Hey, I'd love to connect because..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4">Send Request</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>