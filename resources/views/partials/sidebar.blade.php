@if(Auth::check())

@php
    $type = strtolower(Auth::user()->user_type ?? 'undecided');
@endphp

<button class="btn btn-outline-primary d-lg-none position-fixed top-0 start-0 m-3"
        id="sidebarToggle" style="z-index:1200;">
    <i class="bi bi-list fs-4"></i>
</button>

<aside class="sidebar" id="mainSidebar">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    {{-- BRAND --}}
    <div class="sidebar-brand text-center py-3 border-bottom">
        <h4 class="m-0 fw-bold text-primary">
            <i class="bi bi-compass me-1"></i> CareerCompass
        </h4>
    </div>
    @php

    $user = Auth::user();
    $profile = $user?->profile;

    // Initials logic (safe)
    $initials = 'U';
    if ($profile && $profile->full_name) {
        $words = explode(' ', trim($profile->full_name));
        $initials = strtoupper(
            substr($words[0], 0, 1) .
            (isset($words[1]) ? substr($words[1], 0, 1) : '')
        );
    }
@endphp


{{-- USER PROFILE --}}
<div class="user-profile text-center py-4 border-bottom">

    {{-- AVATAR WRAPPER --}}
    <div class="position-relative d-inline-block mb-2">

        {{-- AVATAR --}}
        @if($profile && $profile->profile_photo)
        <img src="{{ asset('storage/' . $profile->profile_photo) }}"
        class="rounded-circle shadow-sm cursor-pointer"
        width="70" height="70"
        style="object-fit: cover;"
        data-bs-toggle="modal"
        data-bs-target="#profilePhotoPreview">   
        @else
            <div class="rounded-circle bg-secondary text-white d-flex
                        align-items-center justify-content-center shadow-sm"
                 style="width:70px;height:70px;font-size:22px;font-weight:600;">
                {{ $initials }}
            </div>
        @endif

        {{-- CAMERA BUTTON --}}
        <button type="button"
                class="btn btn-light rounded-circle shadow-sm position-absolute"
                style="bottom:0; right:0; width:28px; height:28px; padding:0;"
                onclick="document.getElementById('sidebarPhotoInput').click()">
            <i class="bi bi-camera-fill" style="font-size:14px;"></i>
        </button>

        {{-- HIDDEN FILE INPUT --}}
        <form id="sidebarPhotoForm"
              method="POST"
              action="{{ route('profile.photo.update') }}"
              enctype="multipart/form-data"
              class="d-none">
            @csrf
            <input type="file"
                   id="sidebarPhotoInput"
                   name="profile_photo"
                   accept="image/*"
                   onchange="document.getElementById('sidebarPhotoForm').submit()">
        </form>

    </div>

    {{-- NAME --}}
    <div class="fw-semibold text-dark">
        {{ $profile->full_name ?? $user->email }}
    </div>

    {{-- USER TYPE --}}
    <small class="text-muted text-capitalize">
        {{ str_replace('_', ' ', $type) }}
    </small>

</div>



    {{-- NAVIGATION --}}
    <div class="sidebar-body flex-grow-1 overflow-auto">
        <nav class="sidebar-nav px-3 py-3">

            {{-- -------------------- DASHBOARD -------------------- --}}
            <div class="nav-category small fw-semibold text-muted mb-2">Dashboard</div>

            <a href="{{ route('dashboard') }}" 
               class="nav-link @if(request()->routeIs('dashboard')) active @endif">
               <i class="bi bi-speedometer2"></i> Overview
            </a>

            {{-- -------------------- UNIVERSAL TOOLS -------------------- --}}
            <div class="nav-category mt-4 small fw-semibold text-muted mb-2">General Tools</div>

            <a href="{{ route('chat.index') }}" class="nav-link">
                <i class="bi bi-robot"></i> AI Career Assistant
            </a>

            <a href="{{ route('career.trends') }}" class="nav-link">
                <i class="bi bi-bar-chart"></i> Career Trends
            </a>

            <a href="{{ route('learning.resources') }}" class="nav-link">
                <i class="bi bi-journal-code"></i> Learning Resources
            </a>

            <a href="{{ route('career.report') }}" class="nav-link">
                <i class="bi bi-filetype-pdf"></i> Career Report (PDF)
            </a>
            

            {{-- -------------------- HIGH SCHOOL / STUDENT -------------------- --}}
            @if($type === 'high_school' || $type === 'student')
            <div class="nav-category mt-4 small fw-semibold text-muted mb-2">Student Tools</div>

            <a href="{{ route('ai.quiz.start') }}" class="nav-link">
                <i class="bi bi-question-circle"></i> Career Quiz
            </a>
            <a href="{{ route('academic.path') }}" class="nav-link">
                <i class="bi bi-mortarboard"></i> Academic Path Validator
            </a>

            <a href="{{ route('career.suggestions') }}" class="nav-link">
                <i class="bi bi-lightbulb"></i> Career Suggestions
            </a>

            <a href="{{ route('career.roadmap') }}" class="nav-link">
                <i class="bi bi-map"></i> Roadmap Generator
            </a>
            <a href="{{ route('career.tracker') }}" class="nav-link">
              <i class="bi bi-graph-up-arrow"></i> Progress Tracker
            </a>
            <a href="{{ route('interview.page') }}" class="nav-link">
                <i class="bi bi-chat-square-text"></i> Interview Simulator
            </a>
            @endif


            {{-- -------------------- UNIVERSITY USERS -------------------- --}}
            @if($type === 'university')
            <div class="nav-category mt-4 small fw-semibold text-muted mb-2">University Tools</div>

            <a href="{{ route('skill-gap.index') }}" class="nav-link">
                <i class="bi bi-diagram-2"></i> Skill Gap Analyzer
            </a>
            <a href="{{ route('academic.path') }}" class="nav-link">
                <i class="bi bi-mortarboard"></i> Academic Path Validator
            </a>

            <a href="{{ route('career.roadmap') }}" class="nav-link">
                <i class="bi bi-map"></i> Career Roadmap
            </a>

            <a href="{{ route('jobs.recommender') }}" class="nav-link">
                <i class="bi bi-search"></i> Job Recommender
            </a>
            <a href="{{ route('jobs.recommend.history') }}" class="nav-link">
              <i class="bi bi-clock-history"></i> Job History
          </a>
            @endif


            {{-- -------------------- UNDECIDED USERS -------------------- --}}
            @if($type === 'undecided')
            <div class="nav-category mt-4 small fw-semibold text-muted mb-2">Discovery Tools</div>

            <a href="{{ route('ai.quiz.start') }}" class="nav-link">
                <i class="bi bi-question-circle"></i> Career Quiz
            </a>

            <a href="{{ route('career.suggestions') }}" class="nav-link">
                <i class="bi bi-lightbulb"></i> Suggestions
            </a>
            <a href="{{ route('career.roadmap') }}" class="nav-link">
                <i class="bi bi-map"></i> Roadmap Generator
            </a>
            
            <a href="{{ route('career.tracker') }}" class="nav-link">
              <i class="bi bi-graph-up-arrow"></i> Progress Tracker
            </a>
            @endif


            {{-- -------------------- FRESH GRAD -------------------- --}}
            @if($type === 'fresh_grad')
            <div class="nav-category mt-4 small fw-semibold text-muted mb-2">Fresh Graduate Tools</div>

            <a href="{{ route('career.resume') }}" class="nav-link">
                <i class="bi bi-file-earmark-text"></i> Resume Review
            </a>

            <a href="{{ route('interview.page') }}" class="nav-link">
                <i class="bi bi-chat-square-text"></i> Interview Simulator
            </a>

            <a href="{{ route('career.tracker') }}" class="nav-link">
              <i class="bi bi-graph-up-arrow"></i> Progress Tracker
            </a>

            <a href="{{ route('jobs.recommender') }}" class="nav-link">
                <i class="bi bi-search-heart"></i> Job Recommender
            </a>

            <a href="{{ route('jobs.recommend.history') }}" class="nav-link">
                <i class="bi bi-clock-history"></i> Job History
            </a>
            @endif


            {{-- -------------------- GRADUATE -------------------- --}}
            @if($type === 'graduate')
            <div class="nav-category mt-4 small fw-semibold text-muted mb-2">Graduate Tools</div>

            <a href="{{ route('career.resume') }}" class="nav-link">
                <i class="bi bi-file-earmark-person"></i> Resume Analyzer
            </a>

            <a href="{{ route('career.tracker') }}" class="nav-link">
                <i class="bi bi-graph-up-arrow"></i> Progress Tracker
            </a>

            <a href="{{ route('career.interview') }}" class="nav-link">
                <i class="bi bi-chat-square-text"></i> Interview Simulator
            </a>
            @endif


            {{-- -------------------- SWITCHER -------------------- --}}
            @if($type === 'switcher')
            <div class="nav-category mt-4 small fw-semibold text-muted mb-2">Career Switch Toolkit</div>

            <a href="{{ route('career.tracker') }}" class="nav-link">
                <i class="bi bi-graph-up-arrow"></i> Progress Tracker
            </a>

            <a href="{{ route('skill-gap.index') }}" class="nav-link">
                <i class="bi bi-diagram-2"></i> Skill Gap Analyzer
            </a>

            <a href="{{ route('academic.path') }}" class="nav-link">
                <i class="bi bi-mortarboard"></i> Academic Path Validator
            </a>

            <a href="{{ route('learning.resources') }}" class="nav-link">
                <i class="bi bi-journal-code"></i> Learning Resources
            </a>

            <a href="{{ route('career.roadmap') }}" class="nav-link">
                <i class="bi bi-diagram-3"></i> Career Roadmap
            </a>

            <a href="{{ route('jobs.recommender') }}" class="nav-link">
                <i class="bi bi-search"></i> Job Recommender
            </a>
            @endif


            {{-- -------------------- ACCOUNT -------------------- --}}
            <div class="nav-category mt-4 small fw-semibold text-muted mb-2">Account</div>

            <a href="{{ route('settings') }}" 
               class="nav-link @if(request()->routeIs('settings')) active @endif">
                <i class="bi bi-person-gear"></i> Settings
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link btn btn-link w-100 text-start text-danger px-3 py-2">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>

        </nav>
    </div>

    {{-- PROFILE PHOTO FULL VIEW MODAL --}}
@if($profile && $profile->profile_photo)
<div class="modal fade" id="profilePhotoPreview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 bg-transparent">

      <div class="modal-body text-center p-0">
        <img src="{{ asset('storage/' . $profile->profile_photo) }}"
             class="img-fluid rounded-3 shadow-lg"
             alt="Profile Photo">
      </div>

      <button type="button"
              class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
              data-bs-dismiss="modal"></button>

    </div>
  </div>
</div>
@endif
</aside>

<script>
    const sidebar = document.getElementById('mainSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
</script>

@endif
