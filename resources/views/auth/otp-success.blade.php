@extends('layouts.app')

@section('content')

<style>
    #sidebar, .sidebar, .app-sidebar,
    #navbar, .navbar, .app-header {
        display: none !important;
    }

    body {
        overflow: hidden !important;
    }

    .success-overlay {
        position: fixed;
        top:0; left:0;
        width:100%; height:100%;
        backdrop-filter: blur(5px);
        background: rgba(0,0,0,0.4);
        z-index: 500;
    }

    .success-card {
        z-index: 9999;
        position: relative;
    }

    /* Checkmark animation */
    .checkmark-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: inline-block;
        border: 6px solid #28a745;
        position: relative;
        box-sizing: content-box;
        margin-bottom: 10px;
        animation: pop 0.4s ease-out;
    }

    .checkmark-circle::after {
        content: '';
        position: absolute;
        left: 32px;
        top: 12px;
        width: 35px;
        height: 70px;
        border: solid #28a745;
        border-width: 0 6px 6px 0;
        transform: rotate(45deg);
        animation: check 0.6s ease-out 0.2s both;
    }

    @keyframes pop {
        0% { transform: scale(0.5); opacity:0; }
        100% { transform: scale(1); opacity:1; }
    }

    @keyframes check {
        0% { height:0; width:0; opacity:0; }
        100% { height:70px; width:35px; opacity:1; }
    }
</style>

<div class="success-overlay"></div>

<div class="d-flex justify-content-center align-items-center success-card"
     style="height:100vh; width:100vw; position:fixed; top:0; left:0;">

    <div class="col-md-5">
        <div class="card p-4 text-center shadow-lg border-0">

            <div class="d-flex justify-content-center">
                <div class="checkmark-circle"></div>
            </div>

            <h3 class="fw-bold mt-2">Email Verified!</h3>
            <p class="text-muted">
                Redirecting you to your onboarding in <b>3 seconds…</b>
            </p>

            <div class="spinner-border text-success mt-2" role="status"></div>

        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        window.location.href = "{{ route('onboarding.show') }}";
    }, 3000);
</script>

@endsection
