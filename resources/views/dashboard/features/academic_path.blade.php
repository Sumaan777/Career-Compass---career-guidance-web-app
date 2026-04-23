@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4">🎓 Academic Path Validator</h2>

    {{-- FORM --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('academic.path.validate') }}">
                @csrf

                <label class="form-label">Target Career</label>
                <input type="text" name="target_career" class="form-control" required placeholder="e.g., Software Engineer">

                <button class="btn btn-primary mt-3 w-100">Validate Academic Path</button>
            </form>
        </div>
    </div>


    {{-- RESULTS --}}
    @if($latest)
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Latest Result ({{ $latest->created_at->format('d M Y') }})
        </div>

        <div class="card-body">

            <h4>Target Career: <span class="text-primary">{{ $latest->target_career }}</span></h4>
            <hr>

            <h5>🎯 Eligibility</h5>
            @if($latest->education_match['eligible'])
                <p class="text-success fw-bold">Yes — You are eligible.</p>
            @else
                <p class="text-danger fw-bold">Not fully eligible.</p>
            @endif
            <ul>
                @foreach($latest->education_match['reasons'] as $r)
                    <li>{{ $r }}</li>
                @endforeach
            </ul>

            <hr>

            <h5>📘 Required Degrees</h5>
            <ul>
                @foreach($latest->required_degrees as $d)
                    <li>{{ $d }}</li>
                @endforeach
            </ul>

            <hr>

            <h5>🛣 Recommended Academic Paths</h5>
            @foreach($latest->recommended_paths as $path)
                <strong>{{ $path['path'] }}</strong>
                <ul>
                    @foreach($path['steps'] as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ul>
                <hr>
            @endforeach

            <h5>📜 Certifications</h5>
            <ul>
                @foreach($latest->certifications as $c)
                    <li>{{ $c }}</li>
                @endforeach
            </ul>

            <hr>

            <h5>📝 Summary</h5>
            <p class="text-muted">{{ $latest->summary }}</p>
        </div>
    </div>
    @endif

</div>
@endsection
