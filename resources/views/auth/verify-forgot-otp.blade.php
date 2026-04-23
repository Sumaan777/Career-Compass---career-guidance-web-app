@extends('layouts.app')

@section('content')

{{-- ============================================================
   FORGOT PASSWORD — VERIFY OTP (BLUR OVERLAY STYLE)
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
   CENTERED OTP VERIFY CARD
=============================================================== --}}
<div class="d-flex justify-content-center align-items-center otp-active-card"
     style="height:100vh; width:100vw; position:fixed; top:0; left:0;">

    <div class="col-md-5">

        <div class="card shadow-lg p-4 border-0">

            <h3 class="fw-bold text-center mb-3">
                Verify OTP
            </h3>

            <p class="text-muted text-center">
                Enter the 6-digit verification code sent to your email.
            </p>

            {{-- SUCCESS --}}
            @if(session('status'))
                <div class="alert alert-success text-center">
                    {{ session('status') }}
                </div>
            @endif

            {{-- ERROR --}}
            @if(session('error'))
                <div class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            {{-- OTP FORM (LOGIC UNCHANGED) --}}
            <form method="POST" action="{{ route('password.otp.verify') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        6-Digit OTP
                    </label>
                    <input type="text"
                           name="otp"
                           maxlength="6"
                           class="form-control form-control-lg text-center"
                           placeholder="123456"
                           required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-2">
                    Verify OTP →
                </button>
            </form>

            {{-- RESEND (LOGIC SAME, DESIGN MATCHED) --}}
            <div class="text-center">
                <form method="POST" action="{{ route('password.otp.verify') }}">
                    @csrf
                    <button type="submit"
                            class="btn btn-link text-decoration-none text-muted">
                        Resend OTP
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection
