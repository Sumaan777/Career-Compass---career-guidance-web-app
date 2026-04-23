@extends('layouts.app')

@section('content')

{{-- ============================================================
   OTP PAGE — HIDE SIDEBAR + BLUR BACKGROUND
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
   CENTERED OTP CARD
=============================================================== --}}
<div class="d-flex justify-content-center align-items-center otp-active-card"
     style="height: 100vh; width:100vw; position:fixed; top:0; left:0;">

    <div class="col-md-5">

        <div class="card shadow-lg p-4 border-0">
            
            <h3 class="fw-bold text-center mb-3">Verify Your Email</h3>

            <p class="text-muted text-center">
                We've sent a 6-digit verification code to your email.<br>
                Enter it below to continue.
            </p>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- OTP FORM --}}
            <form method="POST" action="{{ route('otp.verify') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Enter OTP</label>
                    <input type="text" maxlength="6" name="otp"
                           class="form-control form-control-lg text-center"
                           placeholder="123456" required>
                </div>

                <button class="btn btn-primary w-100 py-2 mb-2">
                    Verify Email →
                </button>
            </form>

            <form method="POST" action="{{ route('otp.resend') }}">
                @csrf
                <button class="btn btn-outline-secondary w-100 py-2">
                    Resend Code
                </button>
            </form>

        </div>
    </div>
</div>

@endsection
