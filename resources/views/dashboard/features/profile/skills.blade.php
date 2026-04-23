{{-- ===============================
       SKILLS SECTION (PARTIAL)
   =============================== --}}

   <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Skills</h5>

        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSkillModal">
            <i class="bi bi-plus"></i> Add Skill
        </button>
    </div>

    <div class="card-body">
        @if($profile->skills && $profile->skills->count() > 0)
            <ul class="list-group">
                @foreach($profile->skills as $skill)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ ucfirst($skill->skill_name) }}</strong><br>
                            <span class="badge bg-info text-dark">{{ ucfirst($skill->proficiency) }}</span>
                        </div>

                        <div class="d-flex gap-2">
                            {{-- EDIT --}}
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary edit-skill-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editSkillModal"
                                    data-skill-id="{{ $skill->id }}"
                                    data-skill-name="{{ $skill->skill_name }}"
                                    data-skill-prof="{{ $skill->proficiency }}">
                                <i class="bi bi-pencil"></i>
                            </button>

                            {{-- DELETE --}}
                            <form action="{{ route('skills.delete', $skill->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted">No skills added yet. Add your first skill.</p>
        @endif
    </div>
</div>


{{-- ===============================
         ADD SKILL MODAL
   =============================== --}}
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title mb-0">Add Skill</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="skillForm">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Skill Name</label>
                <input type="text" name="skill_name" class="form-control" placeholder="e.g., Laravel" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Proficiency</label>
                <select name="proficiency" class="form-control" required>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="expert">Expert</option>
                </select>
            </div>
        </form>
      </div>

      <div class="modal-footer bg-light">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveSkillBtn">Save Skill</button>
      </div>
    </div>
  </div>
</div>


{{-- ===============================
         EDIT SKILL MODAL
   =============================== --}}
<div class="modal fade" id="editSkillModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title mb-0">Edit Skill</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="editSkillForm">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" id="edit_skill_id">

            <div class="mb-3">
                <label class="form-label fw-semibold">Skill Name</label>
                <input type="text" name="skill_name" id="edit_skill_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Proficiency</label>
                <select name="proficiency" id="edit_skill_prof" class="form-control" required>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="expert">Expert</option>
                </select>
            </div>
        </form>
      </div>

      <div class="modal-footer bg-light">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-secondary" id="updateSkillBtn">Update Skill</button>
      </div>
    </div>
  </div>
</div>
