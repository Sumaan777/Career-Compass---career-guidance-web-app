@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Success & Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif


    <div class="row">

        {{-- Left Panel: Run Analysis --}}
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Run Skill Gap Analysis</h5>
                </div>

                <div class="card-body">

                    <p class="text-muted">
                        You may enter a target career.  
                        If left blank, the system will use your saved <strong>Career Suggestion</strong>.
                    </p>

                    <form method="POST" action="{{ route('skill-gap.analyze') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Target Career</label>
                            <input type="text" name="target_career" class="form-control"
                                   placeholder="e.g., Data Scientist, Web Developer">
                        </div>

                        <button class="btn btn-primary w-100">
                            Analyze Skill Gaps
                        </button>
                    </form>

                    <hr>

                    <h6>Your Current Skills</h6>

                    @if($skills->count())
                        <ul class="list-group small">
                            @foreach($skills as $skill)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ ucfirst($skill->skill_name) }}</span>
                                    <span class="badge bg-info text-dark">{{ ucfirst($skill->proficiency) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <p class="mt-2 small">
                            <a href="{{ route('profile.settings') }}">Manage your skills</a>
                        </p>
                    @else
                        <p class="text-danger small">No skills found. Please add skills in your profile.</p>
                    @endif

                </div>
            </div>
        </div>



        {{-- Right Panel: Latest Results --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <h5 class="mb-0">Latest Skill Gap Analysis</h5>

                    @if($latestAnalysis)
                        <span class="badge bg-secondary">
                            {{ $latestAnalysis->created_at->format('d M Y, h:i A') }}
                        </span>
                    @endif
                </div>

                <div class="card-body">

                    @if(!$latestAnalysis)
                        <p class="text-muted">No analysis generated yet.</p>

                    @else

                        {{-- Target Career --}}
                        <h5>
                            Target Career:
                            <span class="text-primary">{{ $latestAnalysis->target_career }}</span>
                        </h5>

                        @php
                            $required = $latestAnalysis->required_skills ?? [];
                            $matched = $latestAnalysis->matched_skills ?? [];
                            $missing = $latestAnalysis->missing_skills ?? [];
                            $extra   = $latestAnalysis->extra_skills ?? [];

                            $progress = count($required) > 0
                                ? round((count($matched) / count($required)) * 100)
                                : 0;
                        @endphp

                        {{-- Progress Bar --}}
                        <div class="mt-3 mb-4">
                            <label class="fw-semibold">Skill Match Progress</label>
                            <div class="progress" style="height: 22px;">
                                <div class="progress-bar
                                    @if($progress < 40) bg-danger
                                    @elseif($progress < 70) bg-warning
                                    @else bg-success
                                    @endif"
                                     style="width: {{ $progress }}%;">
                                    {{ $progress }}%
                                </div>
                            </div>
                            <p class="text-muted small mt-1">
                                {{ count($matched) }} / {{ count($required) }} required skills matched.
                            </p>
                        </div>

                        <div class="row">

                            {{-- Matched --}}
                            <div class="col-md-4">
                                <h6 class="text-success">Matched Skills</h6>
                                <ul class="list-group small">
                                    @forelse($matched as $s)
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success me-1"></i>
                                            {{ ucfirst($s) }}
                                        </li>
                                    @empty
                                        <p class="text-muted small">No matched skills.</p>
                                    @endforelse
                                </ul>
                            </div>

                            {{-- Missing --}}
                            <div class="col-md-4">
                                <h6 class="text-danger">Missing Skills</h6>
                                <ul class="list-group small">
                                    @forelse($missing as $s)
                                        <li class="list-group-item">
                                            <i class="bi bi-exclamation-circle text-danger me-1"></i>
                                            {{ ucfirst($s) }}
                                        </li>
                                    @empty
                                        <p class="text-muted small">No missing skills. Good job!</p>
                                    @endforelse
                                </ul>

                                {{-- Learning Resource Recommender Button --}}
                                @if(count($missing) > 0)
                                    <form action="{{ route('learning.resources.generate') }}" method="POST">
                                        @csrf

                                        @foreach($missing as $skill)
                                            <input type="hidden" name="skills[]" value="{{ $skill }}">
                                        @endforeach

                                        <button class="btn btn-primary btn-sm w-100 mt-3">
                                            Generate Learning Resources
                                        </button>
                                    </form>
                                @endif

                            </div>

                            {{-- Extra --}}
                            <div class="col-md-4">
                                <h6 class="text-info">Extra Skills</h6>
                                <ul class="list-group small">
                                    @forelse($extra as $s)
                                        <li class="list-group-item">
                                            <i class="bi bi-star-fill text-info me-1"></i>
                                            {{ ucfirst($s) }}
                                        </li>
                                    @empty
                                        <p class="text-muted small">No extra skills.</p>
                                    @endforelse
                                </ul>
                            </div>

                        </div>

                        <hr>

                        {{-- All AI Required Skills --}}
                        <h6>All Required Skills (AI)</h6>
                        <p class="small text-muted">
                            {{ implode(', ', $required) }}
                        </p>

                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
