<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>CareerCompass - AI-Powered Career Guidance Platform</title>
  <meta name="description" content="Discover your ideal career path with AI-powered guidance, skill analysis, and personalized roadmaps">
  <meta name="keywords" content="career guidance, AI career advisor, skill analysis, career roadmap, interview simulator, job matching">

  {{-- CSRF for AI demo --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  
  {{-- external css --}}
  <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
  
  <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
</head>
<body>
  <!-- Header -->
  <header class="fixed-top bg-white shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light py-3">
      <div class="container">
        <a class="navbar-brand" href="#hero">CareerCompass</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" href="#hero">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#about">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#how-it-works">How it works</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#features">Features</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#contact">Contact</a>
            </li>
          </ul>
          
          <div class="ms-lg-3 mt-3 mt-lg-0 d-flex gap-2">
            <a class="btn btn-outline-primary rounded-pill" href="{{ route('login') }}">Login</a>
            <a class="btn btn-primary rounded-pill" href="{{ route('signup') }}">Get Started</a>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <!-- Hero Section -->
  <section id="hero" class="hero">
    <div class="container">
      <div class="row align-items-center min-vh-75">
        <div class="col-lg-6">
          <span class="badge rounded-pill bg-soft-primary text-primary mb-3">
            <i class="bi bi-stars me-1"></i> AI-Powered Career Co-pilot
          </span>
          <h1>Navigate Your Future With AI</h1>
          <p class="lead">CareerCompass analyzes your skills, goals, and market trends to guide you toward a career that actually fits you.</p>
          <div class="d-flex flex-wrap align-items-center gap-3">
            <a href="{{ route('signup') }}" class="btn btn-hero">Start Your Journey</a>
            <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center" id="scrollToDemo">
              <i class="bi bi-robot me-2"></i> Try AI Demo
            </button>
          </div>
          <p class="text-muted small mt-3 mb-0">
            No credit card required. Get a free AI-powered preview of your career direction.
          </p>
        </div>
        <div class="col-lg-6 d-none d-lg-block">
          <div class="hero-visual card border-0 shadow-lg rounded-4 p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="badge bg-success-subtle text-success mb-1">
                  <i class="bi bi-bar-chart-line me-1"></i> Live Insights
                </span>
                <h5 class="fw-bold mb-0">Your Career Snapshot</h5>
              </div>
              <i class="bi bi-grid-3x3-gap fs-4 text-primary"></i>
            </div>
            <ul class="list-unstyled small mb-3">
              <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i> AI Career Quiz completed</li>
              <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i> Resume analyzed</li>
              <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i> Skill gaps identified</li>
              <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i> Roadmap generated</li>
            </ul>
            <div class="progress mb-2" style="height:10px;">
              <div class="progress-bar" role="progressbar" style="width: 72%;"></div>
            </div>
            <p class="small text-muted mb-1">Career readiness based on your skills and goals.</p>
            <p class="small mb-0"><strong>72%</strong> towards your next career milestone 🚀</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- AI Demo Section (compact card) -->
  <section id="ai-demo" class="ai-demo">
    <div class="container">
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-5">
          <div class="section-title">
            <h2>Experience the AI before signing up</h2>
            <p class="lead">Ask CareerCompass AI one question and see how it thinks about your career path.</p>
          </div>
          <ul class="list-unstyled small">
            <li class="mb-2"><i class="bi bi-magic text-primary me-2"></i> Get a quick career direction preview</li>
            <li class="mb-2"><i class="bi bi-lightning-charge text-warning me-2"></i> Understand how AI analyzes your profile</li>
            <li class="mb-2"><i class="bi bi-shield-check text-success me-2"></i> No login needed for the demo</li>
          </ul>
          <p class="text-muted small mb-0">
            You’ll get <strong>one free conversation</strong>. For deeper, personalized guidance, create a free account.
          </p>
        </div>

        <div class="col-lg-7">
          <div class="ai-demo-card shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center">
                <div class="demo-avatar me-2">
                  <i class="bi bi-stars"></i>
                </div>
                <div>
                  <div class="fw-semibold">CareerCompass AI</div>
                  <small class="text-muted">Preview mode · 1 free reply</small>
                </div>
              </div>
              <span class="badge bg-soft-primary text-primary">
                <i class="bi bi-robot"></i> Live Demo
              </span>
            </div>

            <div id="demoMessages" class="demo-messages">
              <div class="demo-bubble demo-bot">
                👋 Hi! I’m your AI career mentor. Ask me something like:
                <br><br>
                <span class="small text-muted">
                  “I like coding & design, what career should I explore?”
                </span>
              </div>
            </div>

            <form id="demoForm" class="demo-input">
              <div class="input-group">
                <input type="text" id="demoInput" class="form-control" placeholder="Ask one career question..." autocomplete="off">
                <button class="btn btn-primary" type="submit">
                  <i class="bi bi-send-fill"></i>
                </button>
              </div>
              <small class="text-muted d-block mt-1" id="demoHint">
                You can try once. For full access, create a free account.
              </small>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="about" class="about-section">
    <div class="container">

        <div class="row align-items-center">

            {{-- LEFT: TEXT CONTENT --}}
            <div class="col-lg-6 mb-4 mb-lg-0">
                <span class="about-badge">
                    <i class="bi bi-stars me-1"></i> About CareerCompass
                </span>

                <h2 class="fw-bold mb-3 about-title">
                    From <span class="gradient-text">confusion</span> to a clear, guided career path.
                </h2>

                <p class="about-text">
                    CareerCompass turns guesswork into a structured journey.
                    Instead of random decisions, you follow a <strong>step-by-step path</strong> —
                    combining AI insights, real market data, and your own strengths.
                </p>

                <p class="about-text">
                    Whether you’re a student, fresh graduate, or switching careers,
                    CareerCompass helps you understand <strong>where you are today</strong>,
                    <strong>what’s missing</strong>, and <strong>how to move forward</strong> with confidence.
                </p>

                <ul class="list-unstyled mt-3">
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        AI-powered guidance tailored to your profile.
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Clear milestones instead of generic advice.
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Designed for Pakistani students & professionals.
                    </li>
                </ul>
            </div>

            {{-- RIGHT: CAREER PATH MAP --}}
            <div class="col-lg-6">
                <div class="career-map-card">
                    <div class="career-map-header d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Your guided journey</small>
                            <h5 class="fw-semibold mb-0">Career Path Map</h5>
                        </div>
                        <span class="badge bg-soft-primary text-primary">
                            <i class="bi bi-compass"></i> AI Guided
                        </span>
                    </div>

                    <div class="career-map-body">
                        <div class="career-map-line"></div>

                        {{-- Step 1 --}}
                        <div class="career-step">
                            <div class="step-dot"></div>
                            <div class="step-content">
                                <div class="step-label">
                                    <span class="step-tag">Step 1</span>
                                    <span class="step-title">
                                        <i class="bi bi-person-lines-fill me-1"></i> Know Yourself
                                    </span>
                                </div>
                                <p class="step-text">
                                    Create your profile and let AI understand your background, interests, and goals.
                                </p>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="career-step">
                            <div class="step-dot"></div>
                            <div class="step-content">
                                <div class="step-label">
                                    <span class="step-tag">Step 2</span>
                                    <span class="step-title">
                                        <i class="bi bi-ui-checks-grid me-1"></i> Take the Career Quiz
                                    </span>
                                </div>
                                <p class="step-text">
                                    Run an AI-driven quiz to discover careers that truly match your personality & strengths.
                                </p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="career-step">
                            <div class="step-dot"></div>
                            <div class="step-content">
                                <div class="step-label">
                                    <span class="step-tag">Step 3</span>
                                    <span class="step-title">
                                        <i class="bi bi-bar-chart-steps me-1"></i> Analyze Skills & Gaps
                                    </span>
                                </div>
                                <p class="step-text">
                                    Compare your current skills with your target role and see exactly what’s missing.
                                </p>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="career-step">
                            <div class="step-dot"></div>
                            <div class="step-content">
                                <div class="step-label">
                                    <span class="step-tag">Step 4</span>
                                    <span class="step-title">
                                        <i class="bi bi-map me-1"></i> Follow Your Roadmap
                                    </span>
                                </div>
                                <p class="step-text">
                                    Use a clear, AI-generated roadmap with learning goals, projects, and practice tasks.
                                </p>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="career-step">
                            <div class="step-dot"></div>
                            <div class="step-content">
                                <div class="step-label">
                                    <span class="step-tag">Step 5</span>
                                    <span class="step-title">
                                        <i class="bi bi-briefcase-fill me-1"></i> Apply with Confidence
                                    </span>
                                </div>
                                <p class="step-text">
                                    Strengthen your resume, practice interviews, and start applying for roles you’re
                                    actually ready for.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="career-map-footer">
                        <small class="text-muted">
                            This is a visual overview. Inside the platform, each step becomes an interactive tool.
                        </small>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>


  <section id="features" class="features-section">
    <div class="container">

        <div class="section-title text-center mb-5">
            <h2 class="fw-bold">Powerful AI Features</h2>
            <p class="lead text-muted">Everything you need to discover, prepare, and grow your career — all in one intelligent platform.</p>
        </div>

        <div class="row g-4">

            <!-- Feature Card -->
            <div class="col-lg-4 col-md-6">
                <div class="neo-card">
                    <div class="neo-icon">
                        <i class="bi bi-question-circle"></i>
                    </div>
                    <h4 class="fw-bold">AI Career Quiz</h4>
                    <p class="text-muted small">
                        Understand your strengths, personality traits, and interests with a smart AI-driven assessment.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="neo-card">
                    <div class="neo-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h4 class="fw-bold">Resume Analyzer</h4>
                    <p class="text-muted small">
                        Get instant feedback on your resume quality, missing keywords, strengths, and improvements.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="neo-card">
                    <div class="neo-icon">
                        <i class="bi bi-bar-chart-steps"></i>
                    </div>
                    <h4 class="fw-bold">Skill Gap Analyzer</h4>
                    <p class="text-muted small">
                        Compare your current skills to real industry requirements and find exact areas to improve.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="neo-card">
                    <div class="neo-icon">
                        <i class="bi bi-map"></i>
                    </div>
                    <h4 class="fw-bold">Career Roadmap</h4>
                    <p class="text-muted small">
                        Get a personalized, step-by-step roadmap showing exactly what to learn and achieve next.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="neo-card">
                    <div class="neo-icon">
                        <i class="bi bi-mic"></i>
                    </div>
                    <h4 class="fw-bold">Interview Simulator</h4>
                    <p class="text-muted small">
                        Practice real job interviews with an AI interviewer tailored to your career goals.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="neo-card">
                    <div class="neo-icon">
                        <i class="bi bi-briefcase"></i>
                    </div>
                    <h4 class="fw-bold">Job Insights & Matches</h4>
                    <p class="text-muted small">
                        Discover job opportunities, demand trends, and recommended roles matched with your profile.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="how-it-works" class="hiw-section">
  <div class="container">

      <div class="section-title text-center mb-5">
          <h2 class="fw-bold">How CareerCompass Guides You</h2>
          <p class="lead text-muted">A smooth and personalized journey — from discovering your strengths to landing your dream role.</p>
      </div>

      <div class="timeline-container">

          <!-- Step 1 -->
          <div class="timeline-item">
              <div class="timeline-icon">
                  <i class="bi bi-person-check"></i>
              </div>
              <div class="timeline-content">
                  <h4 class="fw-bold">1. Create Your Profile</h4>
                  <p class="text-muted small">Tell us about your education, interests, and strengths — we build your initial career baseline.</p>
              </div>
          </div>

          <!-- Step 2 -->
          <div class="timeline-item">
              <div class="timeline-icon">
                  <i class="bi bi-clipboard-check"></i>
              </div>
              <div class="timeline-content">
                  <h4 class="fw-bold">2. Take the AI Career Quiz</h4>
                  <p class="text-muted small">Our AI analyzes your personality and cognitive tendencies to recommend strong-fit career paths.</p>
              </div>
          </div>

          <!-- Step 3 -->
          <div class="timeline-item">
              <div class="timeline-icon">
                  <i class="bi bi-graph-up-arrow"></i>
              </div>
              <div class="timeline-content">
                  <h4 class="fw-bold">3. Analyze Skills & Resume</h4>
                  <p class="text-muted small">Measure your readiness, find gaps, and understand how you compare with real industry expectations.</p>
              </div>
          </div>

          <!-- Step 4 -->
          <div class="timeline-item">
              <div class="timeline-icon">
                  <i class="bi bi-journal-text"></i>
              </div>
              <div class="timeline-content">
                  <h4 class="fw-bold">4. Generate a Career Roadmap</h4>
                  <p class="text-muted small">Receive a customized roadmap outlining what to learn, build, and improve next.</p>
              </div>
          </div>

          <!-- Step 5 -->
          <div class="timeline-item">
              <div class="timeline-icon">
                  <i class="bi bi-lightning-charge"></i>
              </div>
              <div class="timeline-content">
                  <h4 class="fw-bold">5. Practice with AI Interviews</h4>
                  <p class="text-muted small">Simulate real job interviews tailored to your role and level — and get actionable feedback.</p>
              </div>
          </div>

          <!-- Step 6 -->
          <div class="timeline-item">
              <div class="timeline-icon">
                  <i class="bi bi-briefcase-fill"></i>
              </div>
              <div class="timeline-content">
                  <h4 class="fw-bold">6. Explore Jobs & Apply Confidently</h4>
                  <p class="text-muted small">Get job insights, recommended roles, and career matches based on your entire journey.</p>
              </div>
          </div>

      </div>
  </div>
</section>

  <!-- Call To Action Section -->
  <section class="cta-section">
    <div class="container text-center py-5">
      <h3 class="display-5 fw-bold mb-3">Ready to Shape Your Career?</h3>
      <p class="lead mb-4">Join students and professionals discovering their path with CareerCompass.</p>
      <a href="{{ route('signup') }}" class="btn btn-light btn-lg rounded-pill px-4 fw-bold">Get Started Free</a>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="contact">
    <div class="container">
      <div class="section-title text-center">
        <h2>Contact</h2>
        <p class="lead">Have questions about your career journey? Reach out — we're here to help you navigate your future.</p>
      </div>
      
      <div class="row mb-5">
        <div class="col-12">
          <div class="ratio ratio-21x9 rounded-3 overflow-hidden shadow">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3313.4538782217705!2d73.77086257460564!3d33.852191928268944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38e06bc2e50b3c01%3A0xccf5d9b9c5e55374!2s%E2%80%8EDepartment%20Of%20CS%20And%20IT%20University%20Of%20Poonch!5e0!3m2!1sen!2s!4v1764962025202!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-4">
          <div class="contact-info d-flex mb-4">
            <div class="contact-icon">
              <i class="bi bi-geo-alt"></i>
            </div>
            <div>
              <h5>Address</h5>
              <p class="text-muted mb-0">CareerCompass, Department of CS & IT, UPR</p>
            </div>
          </div>
          
          <div class="contact-info d-flex mb-4">
            <div class="contact-icon">
              <i class="bi bi-telephone"></i>
            </div>
            <div>
              <h5>Call Us</h5>
              <p class="text-muted mb-0">+92 312 3456789</p>
            </div>
          </div>
          
          <div class="contact-info d-flex mb-4">
            <div class="contact-icon">
              <i class="bi bi-envelope"></i>
            </div>
            <div>
              <h5>Email Us</h5>
              <p class="text-muted mb-0">support@careercompass.ai</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-8">
          <div class="careercompass-contact">
            <div id="formSuccess" class="alert alert-success d-none"></div>
            <div id="formError" class="alert alert-danger d-none"></div>
          
            <form id="contactForm" class="row g-4">
              @csrf
          
              <div class="col-md-6">
                <label>Your Name</label>
                <input type="text" name="name" required>
              </div>
          
              <div class="col-md-6">
                <label>Your Email</label>
                <input type="email" name="email" required>
              </div>
          
              <div class="col-12">
                <label>Subject</label>
                <input type="text" name="subject" required>
              </div>
          
              <div class="col-12">
                <label>Message</label>
                <textarea name="message" rows="5" required></textarea>
              </div>
          
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary px-4">
                  Send Message
                </button>
              </div>
            </form>
          </div>          
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-5">
          <h4 class="fw-bold mb-3">CareerCompass</h4>
          <p class="mb-4">CareerCompass is your AI-powered companion to discover, plan, and achieve the perfect career path. Navigate your future with clarity and confidence.</p>
          <div class="social-links">
            <a href="#"><i class="bi bi-twitter"></i></a>
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>
        
        <div class="col-lg-2 col-md-4">
          <h5 class="footer-links">Useful Links</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#hero" class="text-light text-decoration-none">Home</a></li>
            <li class="mb-2"><a href="#about" class="text-light text-decoration-none">About us</a></li>
            <li class="mb-2"><a href="#features" class="text-light text-decoration-none">Features</a></li>
            <li class="mb-2"><a href="#" class="text-light text-decoration-none">Terms of Service</a></li>
            <li class="mb-2"><a href="#" class="text-light text-decoration-none">Privacy Policy</a></li>
          </ul>
        </div>
        
        <div class="col-lg-2 col-md-4">
          <h5 class="footer-links">Our Features</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#features" class="text-light text-decoration-none">Career Discovery Quiz</a></li>
            <li class="mb-2"><a href="#features" class="text-light text-decoration-none">Roadmap Generator</a></li>
            <li class="mb-2"><a href="#features" class="text-light text-decoration-none">Resume Analyzer</a></li>
            <li class="mb-2"><a href="#features" class="text-light text-decoration-none">Interview Simulator</a></li>
            <li class="mb-2"><a href="#features" class="text-light text-decoration-none">Skill Gap Analysis</a></li>
          </ul>
        </div>
        
        <div class="col-lg-3 col-md-4">
          <h5 class="footer-links">Contact Us</h5>
          <p class="mb-2">CareerCompass Office</p>
          <p class="mb-2">Univeristy Of Poonch Rawalakot, Department Of CS&IT</p>
          <p class="mb-2">Azad Jammu And Kashmir</p>
          <p class="mb-2"><strong>Phone:</strong> +92 334 8822907</p>
          <p class="mb-0"><strong>Email:</strong> support@careercompass.ai</p>
        </div>
      </div>
      
      <hr class="my-4">
      
      <div class="text-center">
        <p class="mb-0">© 2024 CareerCompass. All Rights Reserved</p>
        <p class="text-muted small mt-2">Designed by BootstrapMade. Distributed by ThemeWagon</p>
      </div>
    </div>
  </footer>

  <!-- Floating minimal AI chat icon (scrolls to demo) -->
  <button id="floatingDemoBtn" class="floating-demo-btn" type="button">
    <i class="bi bi-robot"></i>
  </button>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <script src="{{ asset('js/landing.js') }}"></script>

  <script>
    // Scroll "Try AI Demo" button + floating icon to demo section
    document.getElementById('scrollToDemo').addEventListener('click', function () {
      document.getElementById('ai-demo').scrollIntoView({ behavior: 'smooth' });
    });
    document.getElementById('floatingDemoBtn').addEventListener('click', function () {
      document.getElementById('ai-demo').scrollIntoView({ behavior: 'smooth' });
      document.getElementById('demoInput').focus();
    });

    // AI Demo logic (1 reply only)
    let demoUsed = false;

    const demoForm = document.getElementById('demoForm');
    const demoInput = document.getElementById('demoInput');
    const demoMessages = document.getElementById('demoMessages');
    const demoHint = document.getElementById('demoHint');

    function addDemoBubble(role, text) {
      const div = document.createElement('div');
      div.classList.add('demo-bubble');
      div.classList.add(role === 'user' ? 'demo-user' : 'demo-bot');
      div.innerText = text;
      demoMessages.appendChild(div);
      demoMessages.scrollTop = demoMessages.scrollHeight;
    }

    demoForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      const message = demoInput.value.trim();
      if (!message) return;

      // If already used once → show sign up message only
      if (demoUsed) {
        addDemoBubble('bot', "This preview demo only allows one question.\n\nCreate a free account to have full conversations with CareerCompass AI.");
        demoHint.textContent = "Demo limit reached. Please sign up to continue chatting.";
        demoHint.classList.add('text-danger');
        demoInput.value = "";
        return;
      }

      demoUsed = true;
      addDemoBubble('user', message);
      demoInput.value = "";

      // Typing indicator
      const typing = document.createElement('div');
      typing.classList.add('demo-bubble', 'demo-bot');
      typing.innerText = "Thinking...";
      demoMessages.appendChild(typing);
      demoMessages.scrollTop = demoMessages.scrollHeight;

      try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const res = await fetch("{{ route('ai.demo.chat') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token
          },
          body: JSON.stringify({ message: message })
        });

        const data = await res.json();
        typing.remove();

        if (data && data.reply) {
          addDemoBubble('bot', data.reply + "\n\n✨ For a complete, personalized roadmap, sign up and unlock full CareerCompass.");
          demoHint.textContent = "Want more? Create a free account to continue.";
        } else {
          addDemoBubble('bot', "I wasn't able to respond right now. Please try again in a moment.");
        }

      } catch (error) {
        typing.remove();
        addDemoBubble('bot', "Something went wrong while talking to the AI. Please try again later.");
      }
    });
  </script>

  //contact form script
  <script>
    document.getElementById('contactForm').addEventListener('submit', function (e) {
        e.preventDefault(); // ⛔ page reload stop
    
        const form = this;
        const formData = new FormData(form);
    
        // UI reset
        document.getElementById('formSuccess').classList.add('d-none');
        document.getElementById('formError').classList.add('d-none');
    
        fetch("{{ route('contact.submit') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('formSuccess').innerText = data.message;
                document.getElementById('formSuccess').classList.remove('d-none');
                form.reset();
            } else {
                document.getElementById('formError').innerText = 'Something went wrong.';
                document.getElementById('formError').classList.remove('d-none');
            }
        })
        .catch(err => {
            document.getElementById('formError').innerText = 'Validation error. Please check inputs.';
            document.getElementById('formError').classList.remove('d-none');
        });
    });
    </script>
    
</body>
</html>
