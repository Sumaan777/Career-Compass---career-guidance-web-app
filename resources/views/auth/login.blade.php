<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>CareerCompass - Login</title>
  <meta name="description" content="Login to your CareerCompass account">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="auth-container">
          <div class="row g-0">
            <!-- Left Panel - Branding -->
            <div class="col-lg-6 auth-left">
              <div class="auth-left-content">
                <a href="index.html" class="auth-logo">CareerCompass</a>
                <h2 class="mb-4">Find Your Path to Success</h2>
                <p class="mb-4">AI-powered career guidance to help you discover, plan, and achieve your professional goals.</p>
                
                <ul class="feature-list">
                  <li><i class="bi bi-check"></i> Personalized career recommendations</li>
                  <li><i class="bi bi-check"></i> Skill gap analysis</li>
                  <li><i class="bi bi-check"></i> Career roadmap generator</li>
                  <li><i class="bi bi-check"></i> Interview preparation tools</li>
                  <li><i class="bi bi-check"></i> Job matching algorithm</li>
                </ul>
                
                <div class="mt-4">
                  <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle p-2 me-3">
                      <i class="bi bi-quote text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                      <p class="mb-0 fst-italic">"CareerCompass helped me discover my perfect career path!"</p>
                      <small>- Sarah, Software Developer</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Right Panel - Login Form -->
            <div class="col-lg-6 auth-right">
              <div class="text-center mb-4">
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to continue your career journey</p>
              </div>
              
              <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>

                </div>
                
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <div class="position-relative">
                    <input type="password"
                           class="form-control"
                           id="password"
                           name="password"
                           placeholder="Enter your password"
                           required>
                  
                    <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3"
                       id="togglePassword"
                       style="cursor:pointer;"></i>
                  </div>
                  

                  <div class="d-flex justify-content-end mt-2">
                    <a href="{{ route('password.forgot') }}" class="text-sm text-decoration-none">Forgot password?</a>
                  </div>
                </div>
                
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="remember" name="remember">
                  <label class="form-check-label" for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="btn btn-auth mb-3">Sign In</button>
                
                <div class="auth-switch">
                  Don't have an account? <a href={{ route("signup") }}>Sign up now</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    
      const togglePassword = document.getElementById('togglePassword');
      const passwordField = document.getElementById('password');
    
      if (!togglePassword || !passwordField) return;
    
      togglePassword.addEventListener('click', function () {
    
        const isPassword = passwordField.type === 'password';
        passwordField.type = isPassword ? 'text' : 'password';
    
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
      });
    
    });
    </script>
    
</body>
</html>