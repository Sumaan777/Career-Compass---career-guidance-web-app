{{-- UNIVERSITY STUDENT PROFILE FORM --}}
<form id="universityForm">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">University Name</label>
        <input type="text" name="university_name" class="form-control"
            value="{{ optional($profile->universityProfile)->university_name }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Degree Program</label>
        <input type="text" name="degree_program" class="form-control"
            placeholder="BS Computer Science, BBA, etc"
            value="{{ optional($profile->universityProfile)->degree_program }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Current Semester</label>
        <input type="text" name="current_semester" class="form-control"
            placeholder="1st, 5th, 7th"
            value="{{ optional($profile->universityProfile)->current_semester }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">CGPA</label>
        <input type="number" step="0.01" max="4" min="0"
            name="cgpa" class="form-control"
            value="{{ optional($profile->universityProfile)->cgpa }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Interests</label>
        <textarea name="interests" class="form-control" rows="2">
{{ optional($profile->universityProfile)->interests }}</textarea>
    </div>

    <button type="button" id="saveUniversityBtn" class="btn btn-primary">
        Save University Info
    </button>
</form>

<script>
document.getElementById('saveUniversityBtn')?.addEventListener('click', function () {
    ajaxPost("{{ route('profile.university.save') }}", 'universityForm');
});
</script>
