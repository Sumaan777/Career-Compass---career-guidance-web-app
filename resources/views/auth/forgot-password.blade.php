@extends('layouts.app')

@section('content')

{{-- ============================================================
   FORGOT PASSWORD PAGE — BLUR BACKGROUND (OTP STYLE)
=============================================================== --}}
<style>
    /* Hide sidebar + navbar */
    #sidebar, .sidebar, .app-sidebar,
    #navbar, .navbar, .app-header {
        display: none !important;
    }

    /* Blur + dark overlay */
    .otp-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(4px);
        background: rgba(0,0,0,0.45);
        z-index: 500;
    }

    .otp-active-card {
        z-index: 9999 !important;
        position: relative;
    }

    body {
        overflow: hidden !important;
    }
</style>

<div class="otp-overlay"></div>

{{-- ============================================================
   CENTERED FORGOT PASSWORD CARD
=============================================================== --}}
<div class="d-flex justify-content-center align-items-center otp-active-card"
     style="height:100vh; width:100vw; position:fixed; top:0; left:0;">

    <div class="col-md-5">

        <div class="card shadow-lg p-4 border-0">

            <h3 class="fw-bold text-center mb-3">
                Forgot Password
            </h3>

            <p class="text-muted text-center">
                Enter your registered email address.<br>
                We will send you a 6-digit verification code.
            </p>

            {{-- SUCCESS MESSAGE --}}
            @if(session('status'))
                <div class="alert alert-success text-center">
                    {{ session('status') }}
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if($errors->any())
                <div class="alert alert-danger text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- EMAIL FORM (LOGIC UNCHANGED) --}}
            <form method="POST" action="{{ route('password.sendOtp') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email"
                           name="email"
                           class="form-control form-control-lg text-center"
                           placeholder="you@example.com"
                           required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-2">
                    Send OTP →
                </button>
            </form>

            <div class="text-center">
                <a href="{{ route('login') }}"
                   class="text-decoration-none text-muted">
                    Back to Login
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
