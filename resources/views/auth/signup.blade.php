<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>CareerCompass - Sign Up</title>
  <meta name="description" content="Create your CareerCompass account">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/signup.css') }}">
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
                <h2 class="mb-4">Start Your Career Journey</h2>
                <p class="mb-4">Join thousands of students and professionals who've found their path with CareerCompass.</p>
                
                <ul class="feature-list">
                  <li><i class="bi bi-check"></i> AI-powered career assessment</li>
                  <li><i class="bi bi-check"></i> Personalized roadmap generator</li>
                  <li><i class="bi bi-check"></i> Skill gap analysis</li>
                  <li><i class="bi bi-check"></i> Resume optimization tools</li>
                  <li><i class="bi bi-check"></i> Interview preparation simulator</li>
                </ul>
                
                <div class="mt-4">
                  <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle p-2 me-3">
                      <i class="bi bi-quote text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                      <p class="mb-0 fst-italic">"The career quiz helped me discover options I never considered!"</p>
                      <small>- Michael, Marketing Manager</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Right Panel - Signup Form -->
            <div class="col-lg-6 auth-right">
              <div class="text-center mb-4">
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Sign up to start your career journey</p>
              </div>
              
              
              <form method="POST" action="{{ route('register') }}">
                @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

                @csrf
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" placeholder="Enter your first name" name="firstName" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" placeholder="Enter your last name" name="lastName" required>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="email" placeholder="Enter your email" name="email" required>
                </div>
                
                <div class="mb-3 position-relative">
                  <label for="password" class="form-label">Password</label>
                
                  <input type="password"
                         class="form-control"
                         id="password"
                         placeholder="Create a password"
                         name="password"
                         required>
                
                  <!-- Eye icon -->
                  <i class="bi bi-eye-slash toggle-password"
                     data-target="password"
                     style="
                       position:absolute;
                       top: 42px;
                       right: 15px;
                       cursor: pointer;
                       color: #6c757d;
                     "></i>
                
                  <div class="progress-bar mt-2">
                    <div class="progress-bar-inner" id="passwordStrength"></div>
                  </div>
                  <div class="password-strength text-muted" id="passwordStrengthText">
                    Password strength
                  </div>
                </div>
                
                
                <div class="mb-3 position-relative">
                  <label for="confirmPassword" class="form-label">Confirm Password</label>
                
                  <input type="password"
                         class="form-control"
                         id="confirmPassword"
                         name="password_confirmation"
                         placeholder="Confirm your password"
                         required>
                
                  <!-- Eye icon -->
                  <i class="bi bi-eye-slash toggle-password"
                     data-target="confirmPassword"
                     style="
                       position:absolute;
                       top: 42px;
                       right: 15px;
                       cursor: pointer;
                       color: #6c757d;
                     "></i>
                </div>
                
                
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="terms" required>
                  <label class="form-check-label" for="terms">I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a></label>
                </div>
                
                <button type="submit" class="btn btn-auth mb-3">Create Account</button>
                

                
                <div class="auth-switch">
                  Already have an account? <a href={{ route("login") }}>Sign in here</a>
                </div>
                <p class="text-muted small mt-2">
    After registration, we’ll send a verification code to your email to activate your account.
</p>

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
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    passwordInput.addEventListener('input', function() {
      const password = passwordInput.value;
      let strength = 0;
      let message = '';
      
      if (password.length > 0) {
        // Check password strength
        if (password.length < 6) {
          strength = 25;
          message = 'Too short';
        } else {
          strength = 25;
          
          // Check for lowercase
          if (password.match(/[a-z]/)) strength += 25;
          
          // Check for uppercase
          if (password.match(/[A-Z]/)) strength += 25;
          
          // Check for numbers
          if (password.match(/[0-9]/)) strength += 15;
          
          // Check for special characters
          if (password.match(/[^a-zA-Z0-9]/)) strength += 10;
        }
        
        // Set strength text
        if (strength < 50) {
          message = 'Weak';
          strengthText.className = 'password-strength text-danger';
        } else if (strength < 80) {
          message = 'Medium';
          strengthText.className = 'password-strength text-warning';
        } else {
          message = 'Strong';
          strengthText.className = 'password-strength text-success';
        }
        
        strengthBar.style.width = strength + '%';
        strengthText.textContent = message;
      } else {
        strengthBar.style.width = '0%';
        strengthText.textContent = 'Password strength';
        strengthText.className = 'password-strength text-muted';
      }
    });
  // Toggle password visibility
  document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function () {

      const input = document.getElementById(this.dataset.target);

      if (input.type === 'password') {
        input.type = 'text';
        this.classList.remove('bi-eye-slash');
        this.classList.add('bi-eye');
      } else {
        input.type = 'password';
        this.classList.remove('bi-eye');
        this.classList.add('bi-eye-slash');
      }
    });
  });

  </script>
</body>
</html>