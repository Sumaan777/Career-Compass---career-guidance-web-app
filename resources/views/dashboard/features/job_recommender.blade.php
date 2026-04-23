@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">

        <!-- Left: Form + Results -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="mb-3">AI Job Recommender</h3>
                    <p class="text-muted mb-4">
                        Using your <strong>degree</strong>, <strong>skills</strong>, and <strong>preferred location</strong>,
                        the system will suggest job roles and fetch live job listings.
                    </p>

                    <div class="mb-3">
                        <h6>Your Profile Snapshot</h6>
                        <ul class="mb-0">
                            <li><strong>Degree:</strong> {{ $profile->education_level ?? 'N/A' }}</li>
                            <li><strong>Field of Interest:</strong> {{ $profile->field_of_interest ?? 'N/A' }}</li>
                            <li><strong>Skills:</strong>{{ $profile->skills->pluck('skill_name')->implode(', ') ?? 'N/A' }}</li>
                            <li><strong>Experience (years):</strong> {{ $profile->experience_years ?? 'N/A' }}</li>
                        </ul>
                    </div>

                    <form id="jobRecommendForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <label for="location" class="form-label">Preferred Job Location</label>
                                <select class="form-select" name="location" id="location" required>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 mt-3 mt-md-0">
                                    Find Jobs
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="statusArea" class="mt-3"></div>
                </div>
            </div>

            <div id="resultsArea"></div>
        </div>

        <!-- Right: Analytics Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Your Job Search Analytics</h5>

                    <h6 class="mt-3">Top Locations</h6>
                    @if($topLocations->count())
                        <ul class="mb-3">
                            @foreach($topLocations as $loc)
                                <li>{{ $loc->location ?? 'Unknown' }} – {{ $loc->total }} jobs saved</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No data yet. Run a search to see analytics.</p>
                    @endif

                    <h6 class="mt-3">Top Suggested Roles</h6>
                    @if($topRoles->count())
                        <ul class="mb-3">
                            @foreach($topRoles as $role)
                                <li>{{ $role->ai_job_title }} – {{ $role->total }} listings</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No roles analyzed yet.</p>
                    @endif

                    <h6 class="mt-3">Recent Searches</h6>
                    @if($recentSearches->count())
                        <ul class="mb-0">
                            @foreach($recentSearches as $search)
                                <li>
                                    <strong>{{ $search->location }}</strong> –
                                    {{ $search->total_results }} jobs
                                    <br>
                                    <small class="text-muted">
                                        {{ $search->created_at->diffForHumans() }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No previous searches found.</p>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('jobs.recommend.history') }}" class="btn btn-outline-secondary btn-sm w-100">
                            View Full History
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- AJAX Script --}}
<script>
document.getElementById('jobRecommendForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const statusArea  = document.getElementById('statusArea');
    const resultsArea = document.getElementById('resultsArea');
    const formData    = new FormData(this);

    statusArea.innerHTML  = "<div class='text-info'>⏳ Generating AI titles and fetching jobs. Please wait...</div>";
    resultsArea.innerHTML = "";

    fetch("{{ route('jobs.recommend') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'success') {
            statusArea.innerHTML = "<div class='alert alert-danger'>"+ (data.message || 'Something went wrong.') +"</div>";
            return;
        }

        statusArea.innerHTML = "<div class='alert alert-success'>✅ Recommendations generated and saved.</div>";

        resultsArea.innerHTML = "";
        if (!data.data || data.data.length === 0) {
            resultsArea.innerHTML = "<div class='alert alert-warning mt-3'>No jobs found for this location.</div>";
            return;
        }

        data.data.forEach(cluster => {
            const card = document.createElement('div');
            card.className = 'card shadow-sm mb-3';

            let html = `
                <div class="card-body">
                    <h5 class="card-title mb-1">${cluster.ai_job_title}</h5>
                    <p class="text-muted small mb-3">${cluster.reason ?? ''}</p>
            `;

            if (!cluster.listings || cluster.listings.length === 0) {
                html += `<p class="text-muted">No live listings found for this role in the selected location.</p>`;
            } else {
                cluster.listings.forEach(job => {
                    html += `
                        <div class="border rounded p-2 mb-2">
                            <strong>${job.job_title ?? 'Untitled Job'}</strong><br>
                            <span>${job.company ?? 'Unknown Company'}</span><br>
                            <small class="text-muted">${job.job_location ?? ''}</small><br>
                            ${job.salary ? `<small class="text-muted">Salary: ${job.salary}</small><br>` : ''}
                            ${job.posted_at ? `<small class="text-muted">Posted: ${job.posted_at}</small><br>` : ''}
                            ${job.match_score ? `<span class="badge bg-success mt-1">Match: ${job.match_score}%</span>` : ''}
                            <div class="mt-2">
                                <a href="${job.redirect_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    View Job
                                </a>
                            </div>
                        </div>
                    `;
                });
            }

            html += `</div>`;
            card.innerHTML = html;
            resultsArea.appendChild(card);
        });
    })
    .catch(err => {
        console.error(err);
        statusArea.innerHTML = "<div class='alert alert-danger'>Server error. Please try again.</div>";
    });
});
</script>
@endsection
