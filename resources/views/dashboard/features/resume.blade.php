@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <div class="card shadow-lg p-4 rounded-4 border-0" style="background: #fdfdfd;">
        <h2 class="mb-4 text-center fw-bold">📤 Upload Your Resume</h2>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success text-center fw-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Upload Form --}}
        <form method="POST" action="{{ route('career.resume.upload') }}" enctype="multipart/form-data" class="mb-4">
            @csrf

            <label class="form-label fw-bold mb-2">Choose Resume (PDF, DOC, DOCX)</label>
            <input type="file" class="form-control form-control-lg rounded-3 shadow-sm" name="resume" required>

            <div class="text-center mt-4">
                <button class="btn btn-primary btn-lg px-4 shadow-sm">
                    ⬆ Upload Resume
                </button>
            </div>
        </form>


        {{-- Resume Actions --}}
        @if($resume)
            <hr class="my-4">

            <h4 class="fw-bold mb-3">📄 Last Uploaded Resume:</h4>

            <div class="d-flex flex-wrap gap-3">

                {{-- View Button --}}
                <a href="{{ Storage::url($resume->file_path) }}" 
                   class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm"
                   target="_blank">
                    👁 View Resume
                </a>

                {{-- Analyze Button --}}
                <button type="button" 
                        class="btn btn-success px-4 py-2 rounded-3 shadow-sm" 
                        onclick="analyzeResume()">
                    🤖 Analyze with AI
                </button>

                {{-- Delete Button --}}
                <form action="{{ route('career.resume.delete') }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this resume? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger px-4 py-2 rounded-3 shadow-sm">
                        🗑 Delete
                    </button>
                </form>

            </div>
        @endif

    </div>
</div>


<!-- ----------------------- -->
<!-- AI ANALYSIS MODAL UI -->
<!-- ----------------------- -->
<div class="modal fade" id="analysisModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content rounded-4 shadow">

        <div class="modal-header border-0">
            <h4 class="modal-title fw-bold">🤖 AI Resume Analysis</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body" id="analysisContent"
             style="white-space: pre-line; font-size: 17px; line-height: 1.8;">

            <div class="text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-3 fw-semibold text-muted">Analyzing your resume... Please wait.</p>
            </div>

        </div>

        <div class="modal-footer border-0">
            <button class="btn btn-dark px-4 rounded-3" data-bs-dismiss="modal">Close</button>
        </div>

    </div>
  </div>
</div>


<script>
function analyzeResume() {

    // Open modal
    let analysisModal = new bootstrap.Modal(document.getElementById('analysisModal'));
    analysisModal.show();

    // Loading placeholder
    document.getElementById('analysisContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
            <p class="mt-3 fw-semibold text-muted">Analyzing your resume... Please wait.</p>
        </div>
    `;

    // Fetch AI analysis
    fetch("{{ route('career.resume.analyze') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById('analysisContent').innerHTML =
                `<div class="p-3">${data.analysis.replace(/\n/g, '<br>')}</div>`;
        } else {
            document.getElementById('analysisContent').innerHTML =
                `<div class="text-danger text-center fw-bold py-5">❌ Failed to analyze resume.</div>`;
        }
    })
    .catch(() => {
        document.getElementById('analysisContent').innerHTML =
            `<div class="text-danger text-center fw-bold py-5">❌ Something went wrong.</div>`;
    });
}
</script>

@endsection
