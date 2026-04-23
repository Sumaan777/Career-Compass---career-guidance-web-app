{{-- CAREER SWITCHER PROFILE FORM --}}
<form id="switcherForm">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">Current Field</label>
        <input type="text" name="current_field" class="form-control"
            placeholder="e.g., Software Engineering"
            value="{{ optional($profile->switcherProfile)->current_field }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Previous Field</label>
        <input type="text" name="previous_field" class="form-control"
            placeholder="e.g., Mechanical Engineering"
            value="{{ optional($profile->switcherProfile)->previous_field }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Total Experience (Years)</label>
        <input type="number" min="0" max="50" name="past_experience_years"
            class="form-control"
            value="{{ optional($profile->switcherProfile)->past_experience_years }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Skills (comma separated)</label>
        <textarea name="skills_json" class="form-control" rows="2">{{ optional($profile->switcherProfile)->skills_json ? implode(',', $profile->switcherProfile->skills_json) : '' }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Certifications (comma separated)</label>
        <textarea name="certifications_json" class="form-control" rows="2">{{ optional($profile->switcherProfile)->certifications_json ? implode(',', $profile->switcherProfile->certifications_json) : '' }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Past Job Roles (comma separated)</label>
        <textarea name="past_roles_json" class="form-control" rows="2">{{ optional($profile->switcherProfile)->past_roles_json ? implode(',', $profile->switcherProfile->past_roles_json) : '' }}</textarea>
    </div>

    <button type="button" id="saveSwitcherBtn" class="btn btn-primary">
        Save Switcher Info
    </button>
</form>

<script>
document.getElementById('saveSwitcherBtn')?.addEventListener('click', function () {
    ajaxPost("{{ route('profile.switcher.save') }}", 'switcherForm');
});
</script>
