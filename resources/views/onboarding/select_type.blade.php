@extends('layouts.app')

@section('content')

{{-- ============================================================
   HIDE SIDEBAR + NAVBAR + ADD BLUR OVERLAY
=============================================================== --}}
<style>
    #sidebar, .sidebar, .app-sidebar,
    #navbar, .navbar, .app-header {
        display: none !important;
    }

    .onboard-overlay {
        position: fixed;
        top:0; left:0;
        width:100%; height:100%;
        backdrop-filter: blur(4px);
        background: rgba(0,0,0,0.40);
        z-index: 500;
    }

    .onboard-card {
        z-index: 9999;
        position: relative;
    }

    body {
        overflow: hidden !important;
    }

    .type-box {
        border: 2px solid transparent;
        border-radius: 14px;
        transition: .25s;
        cursor: pointer;
        font-size: 17px;
        padding: 22px;
    }
    .type-box:hover {
        border-color: #0d6efd;
        background: rgba(13,110,253,.10);
    }
    input[type=radio]:checked + .type-box {
        border-color:#0d6efd;
        background:rgba(13,110,253,.15);
    }
</style>

<div class="onboard-overlay"></div>

{{-- ============================================================
   CENTERED ONBOARDING CARD
=============================================================== --}}
<div class="d-flex justify-content-center align-items-center onboard-card"
     style="height:100vh; width:100vw; position:fixed; top:0; left:0;">

    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 p-4">

            <h3 class="fw-bold text-center">Welcome to CareerCompass 👋</h3>
            <p class="text-muted text-center mb-3">
                Select your current status to personalize your dashboard.
            </p>

            <form method="POST" action="{{ route('onboarding.store') }}">
                @csrf

                <div class="row g-3">

                    <label class="col-6">
                        <input type="radio" class="d-none" name="user_type" value="high_school">
                        <div class="type-box text-center">🎓<br>High School</div>
                    </label>

                    <label class="col-6">
                        <input type="radio" class="d-none" name="user_type" value="university">
                        <div class="type-box text-center">🏛️<br>University</div>
                    </label>

                    <label class="col-6">
                        <input type="radio" class="d-none" name="user_type" value="undecided">
                        <div class="type-box text-center">❔<br>Undecided</div>
                    </label>

                    <label class="col-6">
                        <input type="radio" class="d-none" name="user_type" value="fresh_grad">
                        <div class="type-box text-center">🎉<br>Fresh Graduate</div>
                    </label>

                    <label class="col-12">
                        <input type="radio" class="d-none" name="user_type" value="switcher">
                        <div class="type-box text-center">🔄<br>Career Switcher</div>
                    </label>

                </div>

                <button class="btn btn-primary w-100 mt-4 py-2">
                    Continue →
                </button>

            </form>
        </div>
    </div>

</div>

@endsection
