<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Creative Identity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-accent: #2563eb;
            --bg-soft: #f8fafc;
        }

        body { background-color: #fff; font-family: 'Inter', sans-serif; color: #1e293b; }

        /* 1. Hero Section */
        .about-hero {
            padding: 80px 0;
            background: linear-gradient(to right, var(--bg-soft) 50%, #ffffff 50%);
        }

        .profile-img-wrapper {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 20px 20px 60px #d1d5db, -20px -20px 60px #ffffff;
        }

        .experience-badge {
            position: absolute;
            bottom: 30px;
            right: -20px;
            background: var(--primary-accent);
            color: white;
            padding: 20px;
            border-radius: 20px;
            transform: rotate(-5deg);
        }

        /* 2. Timeline Styling */
        .timeline {
            border-left: 2px solid #e2e8f0;
            padding-left: 30px;
            position: relative;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 40px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -41px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            border: 4px solid var(--primary-accent);
        }

        /* 3. Skill Progress */
        .skill-bar { height: 8px; border-radius: 10px; background: #e2e8f0; }
        .skill-fill { height: 100%; border-radius: 10px; background: var(--primary-accent); }

        .stat-card {
            background: var(--bg-soft);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
        }
        .stat-card:hover { border-color: var(--primary-accent); transform: translateY(-5px); }
    </style>
</head>
<body>

<section class="about-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="profile-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=600" class="w-100" alt="Profile Name">
                    <div class="experience-badge shadow-lg">
                        <h2 class="fw-black mb-0">8+</h2>
                        <small class="text-uppercase fw-bold">Years Experience</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <h6 class="text-primary fw-bold text-uppercase letter-spacing-1 mb-3">Professional Bio</h6>
                <h1 class="display-4 fw-black mb-4">I'm David, a Visual Architect & Problem Solver.</h1>
                <p class="lead text-muted mb-4">
                    Based in Lagos, I help brands bridge the gap between complex data and intuitive human experiences. My approach combines technical precision with creative curiosity.
                </p>
                
                <div class="row g-3 mb-5">
                    <div class="col-6 col-md-4">
                        <div class="stat-card">
                            <h3 class="fw-bold mb-0">120+</h3>
                            <small class="text-muted">Projects Done</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="stat-card">
                            <h3 class="fw-bold mb-0">45</h3>
                            <small class="text-muted">Happy Clients</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="stat-card">
                            <h3 class="fw-bold mb-0">12</h3>
                            <small class="text-muted">Awards Won</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button class="btn btn-primary px-4 py-3 rounded-pill fw-bold">Download CV</button>
                    <button class="btn btn-outline-dark px-4 py-3 rounded-pill fw-bold">Get In Touch</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5 mt-5">
    <div class="row g-5">
        <div class="col-lg-6">
            <h3 class="fw-bold mb-5"><i class="ri-history-line text-primary me-2"></i> Experience</h3>
            <div class="timeline">
                <div class="timeline-item">
                    <span class="badge bg-primary-subtle text-primary mb-2">2023 - Present</span>
                    <h5 class="fw-bold">Senior Product Designer</h5>
                    <p class="text-muted small">Leading the UI/UX team at monieFlow, focusing on financial dashboard transitions and real-time data visualization.</p>
                </div>
                <div class="timeline-item">
                    <span class="badge bg-light text-dark mb-2">2020 - 2023</span>
                    <h5 class="fw-bold">Lead Creative Director</h5>
                    <p class="text-muted small">Managed full-scale brand identities for Fintech startups in West Africa.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <h3 class="fw-bold mb-5"><i class="ri-shield-flash-line text-primary me-2"></i> Skills Matrix</h3>
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">UI/UX Architecture</span>
                    <span class="text-muted">95%</span>
                </div>
                <div class="skill-bar"><div class="skill-fill" style="width: 95%;"></div></div>
            </div>
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">Bootstrap & Frontend</span>
                    <span class="text-muted">88%</span>
                </div>
                <div class="skill-bar"><div class="skill-fill" style="width: 88%;"></div></div>
            </div>
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">Data Visualization</span>
                    <span class="text-muted">75%</span>
                </div>
                <div class="skill-bar"><div class="skill-fill" style="width: 75%;"></div></div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>