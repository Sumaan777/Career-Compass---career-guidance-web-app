<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title', 'CareerCompass - Dashboard')</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Cropper.js CSS -->
  <link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  
  @stack('styles')
</head>

<body>

<div class="dashboard-container">

    {{-- ============================================================
        SHOW SIDEBAR ONLY IF USER IS LOGGED IN
    ============================================================= --}}
    @auth
        @include('partials.sidebar')
    @endauth


    <!-- Main Content -->
    <div class="main-content">

      {{-- ============================================================
          SHOW HEADER ONLY IF USER LOGGED IN
      ============================================================= --}}
      @auth
      <header class="header d-flex justify-content-between align-items-center">
        
        {{-- SEARCH BOX --}}
        <div class="search-box position-relative">
          <i class="bi bi-search"></i>
          <input type="text" id="globalSearchInput" placeholder="Search anything..." autocomplete="off">
          <div id="globalSearchResults" class="search-results shadow-lg"></div>
        </div>

        <div class="header-actions d-flex align-items-center">


          {{-- ============================================================
              NOTIFICATION DROPDOWN — ONLY IF LOGGED IN
          ============================================================= --}}
          @php
              $unreadCount = Auth::user()->unreadNotifications()->count();
              $recentNotifications = Auth::user()->notifications()
                  ->orderBy('created_at', 'desc')
                  ->take(5)
                  ->get();
          @endphp

          <div class="dropdown me-3">
              <button class="btn position-relative" data-bs-toggle="dropdown">
                  <i class="bi bi-bell fs-5"></i>

                  @if($unreadCount > 0)
                      <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                          {{ $unreadCount }}
                      </span>
                  @endif
              </button>

              <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:320px; max-height:350px; overflow-y:auto;">

                  <li class="px-3 py-2 border-bottom d-flex justify-content-between">
                      <strong>Notifications</strong>

                      @if($unreadCount > 0)
                      <form method="POST" action="{{ route('notifications.markAllRead') }}">
                          @csrf
                          <button class="btn btn-sm btn-link p-0">Mark all read</button>
                      </form>
                      @endif
                  </li>

                  @forelse($recentNotifications as $note)
                      @php $data = $note->data; @endphp

                      <li class="px-3 py-2 border-bottom small {{ is_null($note->read_at) ? 'bg-light' : '' }}">
                          <div class="d-flex">
                              <i class="bi {{ $data['icon'] ?? 'bi-bell' }} me-2 fs-5"></i>

                              <div class="flex-grow-1">
                                  <div class="fw-bold">{{ $data['title'] }}</div>

                                  @if(!empty($data['message']))
                                      <div class="text-muted">{{ $data['message'] }}</div>
                                  @endif

                                  <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                              </div>
                          </div>
                      </li>

                  @empty
                      <li class="px-3 py-3 text-center text-muted">No notifications yet.</li>
                  @endforelse

                  <li>
                      <a href="{{ route('notifications.index') }}" class="dropdown-item text-center small">
                          View All
                      </a>
                  </li>

              </ul>
          </div>


          {{-- ============================================================
                USER DROPDOWN — ONLY IF LOGGED IN
          ============================================================= --}}
          <div class="dropdown user-dropdown">
            <button class="btn border-0 d-flex align-items-center" data-bs-toggle="dropdown">
                @php
                $profile = Auth::user()->profile;
            
                $initials = 'U';
                if ($profile && $profile->full_name) {
                    $words = explode(' ', trim($profile->full_name));
                    $initials = strtoupper(
                        substr($words[0], 0, 1) .
                        (isset($words[1]) ? substr($words[1], 0, 1) : '')
                    );
                }
            @endphp
            
            @if($profile && $profile->profile_photo)
                <img src="{{ asset('storage/' . $profile->profile_photo) }}"
                     class="rounded-circle"
                     width="40"
                     height="40"
                     style="object-fit:cover;">
            @else
                <div class="text-white d-flex align-items-center justify-content-center"
                     style="width:40px; height:40px; border-radius:50%; background:#4f46e5; font-size:14px; font-weight:600;">
                    {{ $initials }}
                </div>
            @endif
            
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow">

                <li class="px-3 py-2 border-bottom">
                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </li>

                <li>
                    <a class="dropdown-item" href="{{ route('settings') }}">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>

            </ul>
          </div>

        </div>
      </header>
      @endauth


      <!-- Dynamic Page Content -->
      <div class="content">
        @yield('content')
      </div>

    </div>
</div>
{{-- GLOBAL CAREERCOMPASS TOAST --}}
<div id="cc-toast" class="cc-toast d-none">
    <div class="cc-toast-icon">
        <i id="cc-toast-icon" class="bi"></i>
    </div>
    <div class="cc-toast-message" id="cc-toast-message"></div>
</div>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Cropper.js JS -->
<script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>

<!-- Custom JS -->
<script src="{{ asset('js/layout.js') }}"></script>

@stack('scripts')

</body>
</html>
