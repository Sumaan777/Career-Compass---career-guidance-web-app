{{-- HIGH SCHOOL PROFILE FORM --}}
<form id="highschoolForm">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">School Name</label>
        <input type="text" name="school_name" class="form-control"
            value="{{ optional($profile->highschoolProfile)->school_name }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Class Level</label>
        <input type="text" name="class_level" class="form-control"
            placeholder="9, 10, 11, 12"
            value="{{ optional($profile->highschoolProfile)->class_level }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Academic Interests</label>
        <textarea name="academic_interests" class="form-control" rows="2">
{{ optional($profile->highschoolProfile)->academic_interests }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Strengths</label>
        <textarea name="strengths" class="form-control" rows="2">
{{ optional($profile->highschoolProfile)->strengths }}</textarea>
    </div>

    <button type="button" id="saveHighschoolBtn" class="btn btn-primary">
        Save High School Info
    </button>
</form>

<script>
document.getElementById('saveHighschoolBtn')?.addEventListener('click', function () {
    ajaxPost("{{ route('profile.highschool.save') }}", 'highschoolForm');
});
</script>
