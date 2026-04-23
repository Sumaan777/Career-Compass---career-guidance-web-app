@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-3">Job Recommendation History</h3>
            <p class="text-muted">
                All job listings that were recommended to you via the AI Job Recommender.
            </p>

            @if($recommendations->count())
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>AI Role</th>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Match %</th>
                                <th>Saved At</th>
                                <th>Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recommendations as $rec)
                                <tr>
                                    <td>{{ $rec->ai_job_title }}</td>
                                    <td>{{ $rec->job_title }}</td>
                                    <td>{{ $rec->company }}</td>
                                    <td>{{ $rec->job_location }}</td>
                                    <td>{{ $rec->match_score ?? '-' }}</td>
                                    <td>{{ $rec->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($rec->redirect_url)
                                            <a href="{{ $rec->redirect_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $recommendations->links('pagination::bootstrap-5') }}
                </div>
                
            @else
                <p class="text-muted mb-0">No recommendations stored yet. Try running a search first.</p>
            @endif
        </div>
    </div>
</div>
@endsection
