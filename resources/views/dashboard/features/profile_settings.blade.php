@extends('layouts.app')

@section('content')
<div class="container py-4">
  
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

    {{-- Profile Completion Alert --}}
    @if(Auth::user()->profile_completed == 0)
    <div class="alert alert-warning text-center shadow-sm">
        <i class="bi bi-exclamation-circle"></i>
        <strong>Your profile is incomplete!</strong> Please complete your details to unlock AI-based career recommendations.
        <button class="btn btn-sm btn-primary ms-2" id="editProfileBtn">Complete Now</button>
    </div>
    @endif

    {{-- PROFILE PHOTO SECTION --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body d-flex align-items-center gap-4">
    
          {{-- Avatar --}}
          @php
              $profile = Auth::user()->profile;
          @endphp
    
          @if($profile->profile_photo)
              <img src="{{ asset('storage/' . $profile->profile_photo) }}"
                   class="rounded-circle"
                   width="90"
                   height="90"
                   style="object-fit: cover;">
          @else
              @php
                  $name = $profile->full_name ?: Auth::user()->name ?: 'User';
                  $words = explode(' ', trim($name));
                  $initials = strtoupper(
                      substr($words[0],0,1) .
                      (isset($words[1]) ? substr($words[1],0,1) : '')
                  );
              @endphp
    
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                   style="width:90px;height:90px;font-size:32px;font-weight:600;">
                  {{ $initials }}
              </div>
          @endif
    
          {{-- ACTIONS --}}
          <div>
              {{-- Upload --}}
              <form method="POST"
                    action="{{ route('profile.photo.update') }}"
                    enctype="multipart/form-data"
                    class="mb-2">
                  @csrf
    
                  <input type="file"
                         name="profile_photo"
                         class="form-control form-control-sm mb-1"
                         accept="image/*"
                         required>
    
                  <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                      Change Photo
                  </button>
              </form>
    
              {{-- Remove --}}
              @if($profile->profile_photo)
              <form method="POST"
                    action="{{ route('profile.photo.remove') }}"
                    onsubmit="return confirm('Are you sure you want to remove your profile photo?')">
                  @csrf
    
                  <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                      <i class="bi bi-x-circle"></i> Remove Photo
                  </button>
              </form>
              @endif
          </div>
    
      </div>
    </div>
    


    {{-- Profile Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-3">Personal Information</h5>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3"><strong>Full Name:</strong>
                {{ $profile->full_name ?: Auth::user()->name }}                
              </div>
                <div class="col-md-6 mb-3"><strong>Field of Interest:</strong> {{ $profile->field_of_interest ?? 'N/A' }}</div>
                <div class="col-md-4 mb-3"><strong>Education Level:</strong> {{ $profile->education_level ?? 'N/A' }}</div>
                <div class="col-md-4 mb-3"><strong>Current Status:</strong>
                  {{ $profile->current_status ?: Auth::user()->user_type }}
                </div>
                <div class="col-md-4 mb-3"><strong>Location:</strong> {{ $profile->location ?? 'N/A' }}</div>
            </div>
           
        </div>
    </div>

    {{-- Education Section --}}
@php
    $type = Auth::user()->user_type;
@endphp

{{-- EDUCATION SECTION --}}
@if(in_array($type, ['fresh_graduate', 'career_switcher']))
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Education</h5>
        @if($profile->educations->isNotEmpty())
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                <i class="bi bi-pencil"></i> Edit
            </button>
        @else
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                <i class="bi bi-plus"></i> Add
            </button>
        @endif
    </div>

    <div class="card-body">
        @forelse($profile->educations as $edu)
            <div class="border-bottom pb-2 mb-2">
                <strong>{{ $edu->degree }}</strong> in {{ $edu->field_of_study }} <br>
                <span class="text-muted">{{ $edu->institution }} ({{ $edu->start_date }} - {{ $edu->is_current ? 'Present' : $edu->end_date }})</span>
            </div>
        @empty
            <p class="text-muted">No education added yet.</p>
        @endforelse
    </div>
</div>
@endif


   {{-- EXPERIENCE SECTION --}}
@if(in_array($type, ['fresh_graduate', 'career_switcher']))
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Experience</h5>

        @if($profile->experiences->isNotEmpty())
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                <i class="bi bi-pencil"></i> Edit
            </button>
        @else
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                <i class="bi bi-plus"></i> Add
            </button>
        @endif
    </div>

    <div class="card-body">
        @forelse($profile->experiences as $exp)
            <div class="border-bottom pb-2 mb-2">
                <strong>{{ $exp->job_title }}</strong> at {{ $exp->company }} <br>
                <span class="text-muted">{{ $exp->description }} ({{ $exp->start_date }} - {{ $exp->is_current ? 'Present' : $exp->end_date }})</span>
            </div>
        @empty
            <p class="text-muted">No experience added yet.</p>
        @endforelse
    </div>
</div>
@endif

@include('dashboard.features.profile.skills')


{{-- USER TYPE SPECIFIC FORM --}}
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white">
      <h5 class="fw-bold">Additional Information</h5>
  </div>
  <div class="card-body">

      @php $t = Auth::user()->user_type; @endphp

      @if($t === 'high_school')
          @include('dashboard.features.profile.highschool')

      @elseif($t === 'student')
          @include('dashboard.features.profile.university')

      @elseif($t === 'fresh_graduate')
          @include('dashboard.features.profile.graduate')

      @elseif($t === 'career_switcher')
          @include('dashboard.features.profile.switcher')

      @else
          @include('dashboard.features.profile.undecided')

      @endif

  </div>
</div>


{{-- ===================== MODALS ===================== --}}

{{-- Edit Profile Modal --}}
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Profile</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="profileForm">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Full Name</label>
              <input type="text" name="full_name" class="form-control" value="{{ $profile->full_name }}">
            </div>
            <div class="col-md-6 mb-3">
              <label>Field of Interest</label>
              <input type="text" name="field_of_interest" class="form-control" value="{{ $profile->field_of_interest }}">
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label>Education Level</label>
              <input type="text" name="education_level" class="form-control" value="{{ $profile->education_level }}">
            </div>
            <div class="col-md-4 mb-3">
              <label>Current Status</label>
              <input type="text" name="current_status" class="form-control" value="{{ $profile->current_status }}">
            </div>
            <div class="col-md-4 mb-3">
              <label>Location</label>
              <input type="text" name="location" class="form-control" value="{{ $profile->location }}">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer bg-light">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveProfileBtn">Save</button>
      </div>
    </div>
  </div>
</div>

{{-- Education Modal (Add / Update) --}}
<div class="modal fade" id="addEducationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3">
      {{-- Header --}}
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title mb-0">
          {{ $profile->educations->isNotEmpty() ? 'Update Education' : 'Add Education' }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body">
        <form id="educationForm">
          @csrf
          @php
              $edu = $profile->educations->first();
          @endphp

          <div class="mb-3">
            <label class="form-label fw-semibold">Institution</label>
            <input type="text" name="institution" class="form-control" placeholder="e.g. Harvard University"
                   value="{{ $edu->institution ?? '' }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Degree</label>
            <input type="text" name="degree" class="form-control" placeholder="e.g. Bachelor of Science"
                   value="{{ $edu->degree ?? '' }}">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Field of Study</label>
            <input type="text" name="field_of_study" class="form-control" placeholder="e.g. Computer Science"
                   value="{{ $edu->field_of_study ?? '' }}">
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Start Date</label>
              <input type="date" name="start_date" class="form-control"
                     value="{{ $edu->start_date ?? '' }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">End Date</label>
              <input type="date" name="end_date" class="form-control"
                     value="{{ $edu->end_date ?? '' }}">
            </div>
          </div>

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="is_current" value="1"
                   {{ !empty($edu) && $edu->is_current ? 'checked' : '' }}>
            <label class="form-check-label">Currently Studying</label>
          </div>
        </form>
      </div>

      {{-- Footer --}}
      <div class="modal-footer bg-light">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveEducationBtn">
          {{ $profile->educations->isNotEmpty() ? 'Update' : 'Save' }}
        </button>
      </div>
    </div>
  </div>
</div>


{{-- Add / Update Experience Modal --}}
<div class="modal fade" id="addExperienceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3">
      
      {{-- Header --}}
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title mb-0">
          {{ $profile->experiences->isNotEmpty() ? 'Update Experience' : 'Add Experience' }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body p-4">
        <form id="experienceForm" class="needs-validation" novalidate>
          @csrf

          {{-- Job Title --}}
          <div class="mb-3">
            <label for="job_title" class="form-label fw-semibold">Job Title</label>
            <input type="text" name="job_title" id="job_title" class="form-control shadow-sm" 
                   placeholder="e.g., Software Engineer" value="{{ optional($profile->experiences->first())->job_title }}" required>
          </div>

          {{-- Company --}}
          <div class="mb-3">
            <label for="company" class="form-label fw-semibold">Company</label>
            <input type="text" name="company" id="company" class="form-control shadow-sm"
                   placeholder="e.g., Google" value="{{ optional($profile->experiences->first())->company }}">
          </div>

          {{-- Description --}}
          <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea name="description" id="description" class="form-control shadow-sm" rows="3" 
                      placeholder="Briefly describe your responsibilities and achievements...">{{ optional($profile->experiences->first())->description }}</textarea>
          </div>

          {{-- Dates --}}
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="start_date" class="form-label fw-semibold">Start Date</label>
              <input type="date" name="start_date" id="start_date" class="form-control shadow-sm"
                     value="{{ optional($profile->experiences->first())->start_date }}">
            </div>
            <div class="col-md-6">
              <label for="end_date" class="form-label fw-semibold">End Date</label>
              <input type="date" name="end_date" id="end_date" class="form-control shadow-sm"
                     value="{{ optional($profile->experiences->first())->end_date }}">
            </div>
          </div>

          {{-- Checkbox --}}
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="is_current" id="is_current" value="1"
              {{ optional($profile->experiences->first())->is_current ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_current">Currently Working Here</label>
          </div>

        </form>
      </div>

      {{-- Footer --}}
      <div class="modal-footer bg-light border-top-0">
        <button class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-success rounded-pill px-3" id="saveExperienceBtn">Save</button>
      </div>
    </div>
  </div>
</div>

{{-- ===================== JS ===================== --}}
<script>
  document.addEventListener("DOMContentLoaded", () => {
  
      // UNIVERSAL AJAX POST FUNCTION
      function ajaxPost(route, formId) {
          const form = document.getElementById(formId);
  
          if (!form) {
              console.error("Form not found:", formId);
              alert("Form not found: " + formId);
              return;
          }
  
          const data = new FormData(form);
  
          fetch(route, {
              method: "POST",
              headers: {
                  "X-CSRF-TOKEN": "{{ csrf_token() }}"
              },
              body: data
          })
          .then(async (res) => {
              const text = await res.text();
  
              // try JSON parse
              try {
                  const json = JSON.parse(text);
  
                  if (json.status === "success") {
                      alert(json.message);
                      location.reload();
                  } else {
                      alert("Error: " + json.message);
                  }
              } catch (e) {
                  console.error("RAW RESPONSE:", text);
                  alert("AJAX ERROR → " + e);
              }
          })
          .catch(err => {
              alert("AJAX ERROR → " + err);
          });
      }
  
      // SAVE PROFILE BTN
      const saveProfileBtn = document.getElementById("saveProfileBtn");
      if (saveProfileBtn) {
          saveProfileBtn.addEventListener("click", () => {
              ajaxPost("{{ route('profile.update') }}", "profileForm");
          });
      }
  
      // SAVE EDUCATION BTN
      const saveEducationBtn = document.getElementById("saveEducationBtn");
      if (saveEducationBtn) {
          saveEducationBtn.addEventListener("click", () => {
              ajaxPost("{{ route('education.store') }}", "educationForm");
          });
      }
  
      // SAVE EXPERIENCE BTN
      const saveExperienceBtn = document.getElementById("saveExperienceBtn");
      if (saveExperienceBtn) {
          saveExperienceBtn.addEventListener("click", () => {
              ajaxPost("{{ route('experience.store') }}", "experienceForm");
          });
      }
  });

window.ajaxPost = function(route, formId) {
    const form = document.getElementById(formId);
    const data = new FormData(form);

    fetch(route, {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: data
    })
    .then(res => res.json())
    .then(json => {
        if (json.success || json.status === "success") {
            alert(json.message);
            location.reload();
        } else {
            alert("Error: " + json.message);
        }
    })
    .catch(err => alert("AJAX ERROR → " + err));
};

// SAVE SKILL
document.getElementById("saveSkillBtn")?.addEventListener("click", function () {
    ajaxPost("{{ route('skills.store') }}", "skillForm");
});

// LOAD EDIT MODAL DATA
document.querySelectorAll(".edit-skill-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.getElementById("edit_skill_id").value = this.dataset.skillId;
        document.getElementById("edit_skill_name").value = this.dataset.skillName;
        document.getElementById("edit_skill_prof").value = this.dataset.skillProf;
    });
});

// UPDATE SKILL
document.getElementById("updateSkillBtn")?.addEventListener("click", function () {
    let id = document.getElementById("edit_skill_id").value;
    ajaxPost(`/skills/update/${id}`, "editSkillForm");
});


  </script>
  @endsection
  
