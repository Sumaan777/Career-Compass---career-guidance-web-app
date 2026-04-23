@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
    body {
        background: #f5f7fb !important;
    }

    .cc-header-box {
        background: linear-gradient(135deg, #4f46e5, #6366f1, #8b5cf6);
        border-radius: 18px;
        color: white;
        padding: 30px 38px;
        box-shadow: 0 12px 28px rgba(0,0,0,0.15);
        animation: fadeInDown 0.6s ease;
    }

    .trend-card {
        background: white;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid rgba(0,0,0,0.08);
        box-shadow: 0 6px 14px rgba(0,0,0,0.05);
        transition: .3s ease;
    }

    .trend-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 22px rgba(0,0,0,0.08);
    }

    .trend-title {
        font-weight: 700;
        font-size: 20px;
    }

    .stat-box {
        background: #eef2ff;
        border-radius: 12px;
        padding: 14px;
        text-align: center;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #4f46e5;
    }

    .stat-label {
        font-size: 13px;
        color: #6b7280;
    }
</style>

<div class="container mt-4">

    {{-- HEADER --}}
    <div class="cc-header-box mb-4">
        <h2 class="fw-bold mb-1">
            📈 Career Trends — {{ $careerName }}
        </h2>
        <p class="opacity-75 mb-0">
            Real-time labour market signals, generated using AI.
        </p>
    </div>

    {{-- CAREER SEARCH FORM --}}
<div class="trend-card mb-4">
    <h4 class="trend-title mb-3">🔍 Search Career Trend</h4>

    <form method="GET" action="{{ route('career.trends') }}" class="row g-3">

        <div class="col-md-8">
            <input 
                type="text" 
                name="career" 
                class="form-control form-control-lg"
                placeholder="Enter a career role (e.g., Web Developer, AI Engineer)"
                value="{{ $careerName }}"
                required
            >
        </div>

        <div class="col-md-4">
            <button class="btn btn-primary btn-lg w-100">
                View Trend
            </button>
        </div>
        <div class="col-md-12">
            <p class="text-muted small mt-2 mb-1">Popular Careers:</p>
        
            <div class="d-flex flex-wrap gap-2">
                @foreach(['AI Engineer', 'Data Scientist', 'Web Developer', 'Cybersecurity Analyst', 'DevOps Engineer', 'Graphic Designer', 'UI/UX Designer'] as $role)
                    <a href="{{ route('career.trends', ['career' => $role]) }}"
                       class="badge bg-primary text-white p-2"
                       style="cursor:pointer">
                        {{ $role }}
                    </a>
                @endforeach
            </div>
        </div>
        

    </form>
</div>


    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- REFRESH BUTTON --}}
    <form method="POST" action="{{ route('career.trends.refresh') }}">
        @csrf
        <input type="hidden" name="career_name" value="{{ $careerName }}">
        <button class="btn btn-primary mb-3">
            🔄 Refresh Trend Insight
        </button>
    </form>

    {{-- MAIN TREND INSIGHT CARD --}}
    <div class="trend-card mb-4">
        <h4 class="trend-title mb-3">📊 Market Insight Summary</h4>

        <p class="text-muted">{{ $trend->insight_summary ?? 'No insight available.' }}</p>

        <div class="row mt-4 g-3">
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-value">{{ $trend->demand_level ?? 'Unknown' }}</div>
                    <div class="stat-label">Demand Level</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-value">{{ $trend->trend_direction ?? 'Unknown' }}</div>
                    <div class="stat-label">Trend Direction</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-value">{{ $trend->trend_score ?? 0 }}</div>
                    <div class="stat-label">Trend Score</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART AREA --}}
    <div class="trend-card">
        <h4 class="trend-title mb-3">📉 Trend History (Last 6 Months)</h4>

        <div id="chartArea">
            <canvas id="trendChart" height="120"></canvas>
        </div>
    </div>
</div>



{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const labels        = @json($labels);
    const trendScores   = @json($trendScores);
    const jobOpenings   = @json($jobOpenings);
    const searchVolumes = @json($searchVolumes);

    console.log("Labels:", labels);
    console.log("Trend Scores:", trendScores);
    console.log("Job Openings:", jobOpenings);
    console.log("Search Volumes:", searchVolumes);

    if (labels.length === 0) {
        document.getElementById("chartArea").innerHTML =
            "<div class='alert alert-warning mt-3'>⚠ No historical trend data available.</div>";
        return;
    }

    const ctx = document.getElementById('trendChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Trend Score',
                    data: trendScores,
                    borderWidth: 3,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.12)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Job Openings',
                    data: jobOpenings,
                    borderWidth: 2,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.12)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Search Volume',
                    data: searchVolumes,
                    borderWidth: 2,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249,115,22,0.12)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

});
</script>

@endsection
