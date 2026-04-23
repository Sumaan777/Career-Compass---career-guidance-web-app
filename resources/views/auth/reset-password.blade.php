@extends('layouts.app')

@section('content')

{{-- ============================================================
   RESET PASSWORD PAGE — BLUR BACKGROUND (OTP STYLE)
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
   CENTERED RESET PASSWORD CARD
=============================================================== --}}
<div class="d-flex justify-content-center align-items-center otp-active-card"
     style="height:100vh; width:100vw; position:fixed; top:0; left:0;">

    <div class="col-md-5">

        <div class="card shadow-lg p-4 border-0">

            <h3 class="fw-bold text-center mb-3">
                Reset Password
            </h3>

            <p class="text-muted text-center">
                Create a strong new password for your account.
            </p>

            {{-- SUCCESS MESSAGE --}}
            @if(session('status'))
                <div class="alert alert-success text-center">
                    {{ session('status') }}
                </div>
            @endif

            {{-- FORM (LOGIC UNCHANGED) --}}
            <form method="POST" action="{{ route('password.reset.submit') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password"
                           name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           required>

                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password"
                           name="password_confirmation"
                           class="form-control form-control-lg"
                           required>
                </div>

                <button class="btn btn-primary w-100 py-2">
                    Update Password →
                </button>
            </form>

        </div>
    </div>
</div>

@endsection
