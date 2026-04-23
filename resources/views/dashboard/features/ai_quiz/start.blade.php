@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <div class="card shadow-lg p-4">
        <h2 class="text-center mb-4">AI Career Discovery Quiz</h2>

        <p class="text-center text-muted">
            This quiz uses AI to generate personalized questions for your career journey.
        </p>

        <div class="text-center mt-4">
            <button id="startQuiz" type="button" class="btn btn-primary btn-lg">
                Start Quiz
            </button>
        </div>
    </div>
</div>


{{-- Profile Required Modal --}}
<div class="modal fade" id="profileRequiredModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Profile Incomplete</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p class="fw-bold">Please complete your profile before starting the AI Career Quiz.</p>
        <a href="{{ route('profile.settings') }}" class="btn btn-primary w-100 mt-2">Complete Profile</a>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {

    // GET PROFILE COMPLETION VALUE FROM BACKEND
    const profileCompleted = {{ Auth::user()->profile_completed }};

    document.getElementById("startQuiz").addEventListener("click", function(event) {

        event.preventDefault();  // STOP all default button behavior
        event.stopPropagation();

        // If incomplete → show popup & STOP
        if (profileCompleted == 0) {
            let modal = new bootstrap.Modal(document.getElementById('profileRequiredModal'));
            modal.show();
            return false;  // BLOCK redirect
        }

        // ELSE redirect to generate AI questions
        window.location.href = "{{ route('ai.quiz.generate') }}";
    });

});
</script>

@endsection
