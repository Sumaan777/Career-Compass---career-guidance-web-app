{{-- UNDECIDED PROFILE FORM --}}
<form id="undecidedForm">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">Interests</label>
        <textarea name="interests" class="form-control" rows="2">
{{ optional($profile->undecidedProfile)->interests }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Strengths</label>
        <textarea name="strengths" class="form-control" rows="2">
{{ optional($profile->undecidedProfile)->strengths }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Motivation Level</label>
        <input type="text" name="motivation_level" class="form-control"
            placeholder="low, medium, high, or 1–5"
            value="{{ optional($profile->undecidedProfile)->motivation_level }}">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Preferred Learning Style</label>
        <input type="text" name="preferred_learning_style" class="form-control"
            placeholder="visual, reading, hands-on"
            value="{{ optional($profile->undecidedProfile)->preferred_learning_style }}">
    </div>

    <button type="button" id="saveUndecidedBtn" class="btn btn-primary">
        Save General Info
    </button>
</form>

<script>
document.getElementById('saveUndecidedBtn')?.addEventListener('click', function () {
    ajaxPost("{{ route('profile.undecided.save') }}", 'undecidedForm');
});
</script>
