@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4">🎓 Recommended Learning Resources</h2>
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">🔍 Find Learning Resources</h5>
    
            <form action="{{ route('learning.resources.generate') }}" method="POST">
                @csrf
    
                <label class="form-label">
                    Enter skills (comma separated)
                </label>
    
                <input type="text"
                       name="manual_skills"
                       class="form-control mb-3"
                       placeholder="e.g. Laravel, Python, Data Analysis"
                       required>
    
                <button class="btn btn-primary">
                    Generate Resources
                </button>
            </form>
        </div>
    </div>
    

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($resources->count() == 0)
        <div class="alert alert-info">
            No learning resources found.  
            <br>
            <strong>Go to Skill Gap Analyzer and generate recommendations.</strong>
        </div>
    @endif

    <div class="row">
        @foreach($resources as $res)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $res->title }}</h5>
                        <p class="text-muted">Skill: {{ $res->skill }}</p>

                        <p>{{ $res->description }}</p>

                        <p>
                            <span class="badge bg-primary">{{ $res->platform }}</span>
                            <span class="badge bg-warning text-dark">{{ $res->difficulty }}</span>
                        </p>

                        @if($res->duration)
                            <p><strong>Duration:</strong> {{ $res->duration }} hrs</p>
                        @endif

                        @if($res->url)
                        <a href="{{ $res->url }}" target="_blank" class="btn btn-sm btn-success">
                            Visit Resource
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
