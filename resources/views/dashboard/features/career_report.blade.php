@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-3 fw-bold">📄 Career Report</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p class="text-muted">
                Generate a consolidated PDF report of your career journey including:
                skill gaps, academic eligibility, market trends, interview readiness,
                roadmap, and recommended learning resources.
            </p>

            <form method="POST" action="{{ route('career.report.generate') }}">
                @csrf
                <button class="btn btn-primary w-100">
                    Download My Career Report (PDF)
                </button>
            </form>
        </div>
    </div>

    {{-- Latest Report --}}
    @if($latestReport)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                Latest Report
            </div>
            <div class="card-body">
                <h5 class="fw-bold">{{ $latestReport->title }}</h5>
                <p class="text-muted mb-1">
                    Generated on: {{ $latestReport->created_at->format('d M Y, h:i A') }}
                </p>
                <p>{{ $latestReport->summary }}</p>

                <a href="{{ asset('storage/' . $latestReport->file_path) }}"
                   class="btn btn-outline-secondary"
                   target="_blank">
                    Open Last Report
                </a>
            </div>
        </div>
    @endif


    {{-- Report History --}}
    @if($history->count() > 0)
        <div class="card shadow-sm">
            <div class="card-header">
                Report History
            </div>

            <div class="card-body">
                <ul class="list-group">

                    @foreach($history as $report)
                        <li class="list-group-item d-flex justify-content-between align-items-center">

                            <div>
                                <strong>{{ $report->title }}</strong><br>
                                <small class="text-muted">
                                    {{ $report->created_at->format('d M Y, h:i A') }}
                                </small>
                            </div>

                            <div class="d-flex gap-2">

                                {{-- View --}}
                                <a href="{{ route('career.report.view', $report->id) }}" 
                                    class="btn btn-primary btn-sm"
                                    target="_blank">
                                    View
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('career.report.delete', $report->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this report?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>

                            </div>
                        </li>
                    @endforeach

                </ul>
            </div>
        </div>
    @endif

</div>
@endsection
