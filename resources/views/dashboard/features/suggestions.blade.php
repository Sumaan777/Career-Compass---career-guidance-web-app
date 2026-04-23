@extends('layouts.app')

@section('content')

<style>
    /* --------------------------------------------------------
        LAYOUT + DESIGN
    -------------------------------------------------------- */
    .suggestion-container {
        max-width: 1050px;
        margin: auto;
        padding-bottom: 150px;
    }

    .hero-card {
        background: linear-gradient(135deg, #4e73df, #283a96);
        padding: 55px 45px;
        border-radius: 22px;
        margin-bottom: 45px;
        color: white;
        text-align: center;
        box-shadow: 0 8px 25px rgba(0,0,0,0.18);
        animation: fadeInDown .7s ease;
    }

    .hero-card h1 {
        font-weight: 900;
        font-size: 34px;
    }

    .hero-card p {
        opacity: .93;
        font-size: 17px;
        margin-top: 12px;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .suggestion-card {
        background: white;
        padding: 28px;
        border-radius: 18px;
        margin-bottom: 28px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.07);
        border-left: 6px solid #4e73df;
        transition: .25s ease;
        animation: fadeInUp .6s ease;
    }

    .suggestion-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 7px 28px rgba(0,0,0,0.12);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .suggestion-title {
        font-size: 22px;
        font-weight: 800;
        color: #1b2b53;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .suggestion-icon {
        font-size: 26px;
        color: #4e73df;
    }

    .suggestion-text {
        font-size: 16px;
        line-height: 1.7;
        color: #444;
        white-space: pre-line;
    }

    .badge-tag {
        background: #eef2ff;
        color: #4e73df;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 13px;
        margin-right: 6px;
        display: inline-block;
    }

    .top-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-bottom: 35px;
    }

</style>

<div class="suggestion-container">

    <!-- HERO SECTION -->
    <div class="hero-card">
        <h1><i class="bi bi-stars"></i> Your AI Career Recommendations</h1>
        <p>Based on your quiz answers, interests, and profile details.</p>
    </div>

    <!-- TOP BUTTONS -->
    <div class="top-buttons">
        <a href="{{ route('ai.quiz.start') }}" class="btn btn-outline-light btn-lg" style="background:#4e73df;">
            <i class="bi bi-arrow-repeat"></i> Retake Quiz
        </a>

        <a href="{{ route('career.tracker') }}" class="btn btn-outline-primary btn-lg">
            <i class="bi bi-graph-up-arrow"></i> Open Career Tracker
        </a>
    </div>

    @php
        $raw = $suggestions;
        $sections = preg_split('/\n(?=\d+\.)/', $raw);

        $icons = [
            1 => 'bi-flag',
            2 => 'bi-lightbulb',
            3 => 'bi-rocket',
            4 => 'bi-briefcase',
            5 => 'bi-stack',
            6 => 'bi-lightning-charge',
            7 => 'bi-diagram-3',
            8 => 'bi-activity',
            9 => 'bi-stars'
        ];
    @endphp

    <!-- MULTI-SECTION SUGGESTIONS -->
    @if(count($sections) > 1)

        @foreach($sections as $index => $block)

            @php
                $title = "Insight " . ($index + 1);
                if (preg_match('/^\d+\.\s*(.+)/', $block, $m)) {
                    $title = trim($m[1]);
                }
                $body = trim(preg_replace('/^\d+\.\s*/', '', $block));
            @endphp

            <div class="suggestion-card">
                <div class="suggestion-title">
                    <i class="bi {{ $icons[$index+1] ?? 'bi-circle' }} suggestion-icon"></i>
                    {{ $title }}
                </div>

                <div class="suggestion-text">
                    {!! nl2br(e($body)) !!}
                </div>

                <div class="mt-3">
                    <span class="badge-tag"><i class="bi bi-check2-circle"></i> Recommended</span>
                    <span class="badge-tag"><i class="bi bi-cpu"></i> AI Generated</span>
                </div>
            </div>

        @endforeach

    @else

        <!-- FALLBACK FOR RAW TEXT -->
        <div class="suggestion-card">
            <div class="suggestion-title">
                <i class="bi bi-stars suggestion-icon"></i>
                Career Overview
            </div>

            <div class="suggestion-text">
                {!! nl2br(e($raw)) !!}
            </div>

            <div class="mt-3">
                <span class="badge-tag"><i class="bi bi-check2-circle"></i> Recommended</span>
                <span class="badge-tag"><i class="bi bi-cpu"></i> AI Generated</span>
            </div>
        </div>

    @endif

</div>

@endsection
