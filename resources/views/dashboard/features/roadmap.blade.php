@extends('layouts.app')

@section('content')

<style>
/* ===============================
   CareerCompass – Modern Roadmap UI
=================================*/

.roadmap-hero {
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color: #fff;
    border-radius: 16px;
}

.roadmap-chip {
    background: rgba(255,255,255,0.18);
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 13px;
}

.timeline-card {
    border-radius: 16px;
}

.phase-mini-card {
    border-left: 4px solid #0d6efd;
    transition: all 0.2s ease;
}

.phase-mini-card:hover {
    transform: translateY(-2px);
    background: #f8f9fa;
}

.phase-card {
    border-radius: 16px;
}

.skill-chip {
    border-radius: 50px;
    font-size: 13px;
}

.action-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 8px 12px;
}
</style>

<a href="{{ route('career.trends', ['career' => $lastRoadmap->career ?? 'AI Engineer']) }}"
   class="btn btn-outline-info btn-sm mb-3">
    View Market Trend for this Career
</a>

<form action="{{ route('roadmap.generate') }}" method="POST" class="mb-4 position-relative">
    @csrf
    <label class="fw-bold mb-2">Search a career</label>
    <input id="careerSearch" name="career_query" class="form-control"
           placeholder="Search e.g. AI Engineer, Data Scientist..." autocomplete="off" required>
    <ul id="careerSuggestions" class="list-group"
        style="display:none; position:absolute; z-index:999; width:100%"></ul>

    <button class="btn btn-primary mt-3">
        <i class="bi bi-compass"></i> Generate Roadmap
    </button>
</form>

@if($roadmapData)

{{-- ================= HERO SECTION ================= --}}
<div class="roadmap-hero p-4 mb-4">
    <h3>{{ $roadmapData['career_title'] }}</h3>
    <p class="opacity-75 mb-3">
        {{ $roadmapData['short_summary'] }}
    </p>

    <div class="d-flex flex-wrap gap-2">
        <span class="roadmap-chip"><i class="bi bi-stars"></i> AI Generated</span>
        <span class="roadmap-chip"><i class="bi bi-clock-history"></i> Long-term Plan</span>
        <span class="roadmap-chip"><i class="bi bi-graph-up-arrow"></i> Market Aware</span>
    </div>
</div>

{{-- ================= TIMELINE + OVERVIEW ================= --}}
@if(!empty($mermaidCode))
<div class="row mb-5">

    {{-- LEFT: VISUAL TIMELINE --}}
    <div class="col-md-5 mb-3">
        <div class="card timeline-card shadow border-0 h-100">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-1">
                    <i class="bi bi-diagram-3"></i> Career Progression Timeline
                </h6>
                <p class="text-muted small mb-3">
                    High-level visual journey of your career path
                </p>

                <div class="mermaid">
{!! $mermaidCode !!}
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: PHASE OVERVIEW --}}
    <div class="col-md-7">
        <h6 class="fw-bold text-secondary mb-3">
            <i class="bi bi-layers"></i> Roadmap Phases Overview
        </h6>

        @foreach($roadmapData['phases'] as $index => $phase)
        <div class="card phase-mini-card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h6 class="fw-bold mb-1">
                        Phase {{ $index + 1 }} — {{ $phase['name'] }}
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        {{ $phase['level'] }}
                    </span>
                </div>

                <small class="text-muted">
                    ⏱ {{ $phase['duration'] }}
                </small>

                <p class="text-muted mt-2 mb-0">
                    {{ $phase['goals'][0] ?? '' }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endif

{{-- ================= AI TASK BUTTON ================= --}}
<form method="POST" action="{{ route('career.tasks.generateAI') }}" class="mb-4">
    @csrf
    <input type="hidden" name="roadmap_id" value="{{ $lastRoadmap->id }}">
    <button class="btn btn-success">
        <i class="bi bi-magic"></i> Generate Smart Tasks from Roadmap
    </button>
</form>

{{-- ================= GLOBAL SKILLS ================= --}}
@if(!empty($roadmapData['global_skills']))
<h5 class="fw-bold mt-4">Key Global Skills</h5>
@foreach($roadmapData['global_skills'] as $skill)
    <span class="badge skill-chip bg-light text-dark border me-1 mb-1">
        {{ $skill }}
    </span>
@endforeach
@endif

{{-- ================= PREREQUISITES ================= --}}
@if(!empty($roadmapData['prerequisites']))
<h5 class="fw-bold mt-4">Prerequisites</h5>
<ul>
@foreach($roadmapData['prerequisites'] as $pre)
    <li>{{ $pre }}</li>
@endforeach
</ul>
@endif

{{-- ================= DETAILED PHASES ================= --}}
<h4 class="fw-bold mt-5">Detailed Roadmap Phases</h4>

@foreach($roadmapData['phases'] as $phase)
<div class="card phase-card shadow-sm border-0 mb-4">
    <div class="card-body">

        <h5 class="fw-bold">{{ $phase['name'] }}</h5>
        <span class="badge bg-success">{{ $phase['level'] }}</span>
        <span class="badge bg-dark">{{ $phase['duration'] }}</span>

        <hr>

        <h6 class="fw-bold">Goals</h6>
        <ul>
        @foreach($phase['goals'] as $goal)
            <li>{{ $goal }}</li>
        @endforeach
        </ul>

        <h6 class="fw-bold mt-3">Skills to Learn</h6>
        @foreach($phase['skills_to_learn'] as $skill)
            <span class="badge skill-chip bg-light text-dark border me-1 mb-1">
                {{ $skill }}
            </span>
        @endforeach

        <h6 class="fw-bold mt-3">Actions</h6>
        @foreach($phase['actions'] as $action)
            <div class="action-item mb-2">
                <i class="bi bi-check-circle text-success me-1"></i>
                {{ $action }}
            </div>
        @endforeach

        <h6 class="fw-bold mt-3">Resources</h6>
        <ul>
        @foreach($phase['resources'] as $res)
            <li>{{ $res }}</li>
        @endforeach
        </ul>

        <form action="{{ route('career.tracker.addFromRoadmap') }}" method="POST" class="mt-3">
            @csrf
            <input type="hidden" name="phase_name" value="{{ $phase['name'] }}">
            <input type="hidden" name="tasks" value='@json($phase['actions'])'>
            <button class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Add Actions to My Tasks
            </button>
        </form>

    </div>
</div>
@endforeach

{{-- ================= SUGGESTED ROLES ================= --}}
@if(!empty($roadmapData['suggested_roles']))
<h4 class="fw-bold mt-4">Possible Job Roles</h4>
@foreach($roadmapData['suggested_roles'] as $role)
    <span class="badge bg-secondary me-1 mb-1">{{ $role }}</span>
@endforeach
@endif

@endif

{{-- ================= MARKET INSIGHT ================= --}}
@if(isset($trendForRoadmap) && $trendForRoadmap)
<div class="alert alert-info mt-4">
    <strong>Market Insight:</strong>
    <span class="badge bg-success">Demand: {{ $trendForRoadmap->demand_level }}</span>
    <span class="badge bg-primary">Trend: {{ $trendForRoadmap->trend_direction }}</span>
    @if($trendForRoadmap->trend_score)
        <span class="badge bg-dark">Score: {{ $trendForRoadmap->trend_score }}/100</span>
    @endif
</div>
@endif

{{-- ================= AUTOCOMPLETE JS ================= --}}
<script>
const input = document.getElementById('careerSearch');
const list = document.getElementById('careerSuggestions');

input.addEventListener('input', function () {
    const q = this.value;
    if (q.length < 2) { list.style.display = 'none'; return; }

    fetch(`{{ route('career.search') }}?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            list.innerHTML = '';
            if (!data.length) { list.style.display = 'none'; return; }

            data.forEach(item => {
                const li = document.createElement('li');
                li.classList.add('list-group-item');
                li.style.cursor = 'pointer';
                li.innerHTML = `<strong>${item.title}</strong>`;
                li.onclick = () => { input.value = item.title; list.style.display = 'none'; };
                list.appendChild(li);
            });
            list.style.display = 'block';
        });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
mermaid.initialize({
    startOnLoad: true,
    theme: 'default',
    securityLevel: 'strict'
});
</script>

@endsection
