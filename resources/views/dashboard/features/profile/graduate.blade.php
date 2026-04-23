{{-- FRESH GRADUATE PROFILE FORM --}}
<form id="graduateForm">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">University Name</label>
        <input type="text" name="university_name" class="form-control"
            value="{{ optional($profile->graduateProfile)->university_name }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Degree Name</label>
        <input type="text" name="degree_name" class="form-control"
            placeholder="BS, MS, etc"
            value="{{ optional($profile->graduateProfile)->degree_name }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Major</label>
        <input type="text" name="major" class="form-control"
            placeholder="Computer Science, Electrical Engineering"
            value="{{ optional($profile->graduateProfile)->major }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Graduation Year</label>
        <input type="number" min="1950" max="{{ date('Y') }}"
            name="graduation_year" class="form-control"
            value="{{ optional($profile->graduateProfile)->graduation_year }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">CGPA</label>
        <input type="number" step="0.01" max="4" min="0"
            name="cgpa" class="form-control"
            value="{{ optional($profile->graduateProfile)->cgpa }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Final Year Project Title</label>
        <input type="text" name="final_project_title" class="form-control"
            value="{{ optional($profile->graduateProfile)->final_project_title }}">
    </div>

    <button type="button" id="saveGraduateBtn" class="btn btn-primary">
        Save Graduate Info
    </button>
</form>

<script>
document.getElementById('saveGraduateBtn')?.addEventListener('click', function () {
    ajaxPost("{{ route('profile.graduate.save') }}", 'graduateForm');
});
</script>
