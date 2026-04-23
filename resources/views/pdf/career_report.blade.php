<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Career Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #2c3e50;
            font-size: 13px;
        }

        /* Header */
        .header {
            background: #0d6efd;
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: bold;
        }

        .section {
            padding: 25px 35px;
            border-bottom: 1px solid #eaeaea;
        }

        .section h2 {
            color: #0d6efd;
            border-left: 4px solid #0d6efd;
            padding-left: 8px;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .subtext {
            margin-top: 8px;
            line-height: 1.6;
        }

        /* Trend chart */
        .chart-container {
            text-align: center;
            margin: 20px 0;
        }

        .chart-container img {
            width: 80%;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h1>Career Report</h1>
        <p>{{ $user->name }} • {{ now()->format('d M, Y') }}</p>
    </div>

    <!-- SECTION 1 -->
    <div class="section">
        <h2>Profile Overview</h2>
        <div class="subtext">
            {!! nl2br(e($context['profile']['full_name'] ?? '')) !!}<br>
            Education: {{ $context['profile']['education_level'] ?? 'N/A' }}<br>
            Field of Interest: {{ $context['profile']['field_of_interest'] ?? 'N/A' }}<br>
            Skills: {{ $context['profile']['skills'] ?? 'N/A' }}<br>
            Experience: {{ $context['profile']['experience_years'] ?? 'N/A' }} years<br>
            Location: {{ $context['profile']['location'] ?? 'N/A' }}<br>
        </div>
    </div>

    <!-- SECTION 2 -->
    <div class="section">
        <h2>AI Summary</h2>
        <div class="subtext">
            {!! nl2br(e($aiSummary)) !!}
        </div>
    </div>

    <!-- SECTION 3 -->
    @if($trend)
    <div class="section">
        <h2>Market Trend Overview</h2>
        <p class="subtext">
            Demand Level: {{ $trend->demand_level }}<br>
            Trend Score: {{ $trend->trend_score }}<br>
            Direction: {{ $trend->trend_direction }}<br>
            Job Openings: {{ $trend->job_openings }}<br>
        </p>

        <!-- SVG Trend Chart -->
        <div class="chart-container">
            <img src="{{ storage_path('app/public/trend_charts/'.$user->id.'.svg') }}">
        </div>
    </div>
    @endif

    <!-- SECTION 4 -->
    <div class="section">
        <h2>Skill Gap Analysis</h2>
        <div class="subtext">
            <strong>Matched Skills:</strong> {{ implode(', ', $skillGap->matched_skills ?? []) }}<br>
            <strong>Missing Skills:</strong> {{ implode(', ', $skillGap->missing_skills ?? []) }}<br>
        </div>
    </div>

    <!-- SECTION 5 -->
    <div class="section">
        <h2>Academic Eligibility</h2>
        <div class="subtext">
            @if($academicPath)
                Eligibility: {{ $academicPath->education_match['eligible'] ? 'Eligible' : 'Not Eligible' }}<br>
                Reasons: {{ implode(', ', $academicPath->education_match['reasons'] ?? []) }}
            @else
                No data available.
            @endif
        </div>
    </div>

    <!-- SECTION 6 -->
    <div class="section">
        <h2>Learning Resources</h2>
        <div class="subtext">
            @forelse($learningResources as $r)
                • <strong>{{ $r->title }}</strong> ({{ $r->platform }}) — {{ $r->skill }}<br>
            @empty
                No learning resources available.
            @endforelse
        </div>
    </div>

    <!-- SECTION 7 -->
    <div class="section">
        <h2>Interview Performance</h2>
        <div class="subtext">
            @if($interview)
                Score: {{ $interview->total_score }}<br>
                Questions: {{ $interview->question_count }}<br>
                <br>{{ $interview->final_report }}
            @else
                No interview data available.
            @endif
        </div>
    </div>

</body>
</html>
