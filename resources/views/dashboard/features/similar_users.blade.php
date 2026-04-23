@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold mb-3">🤖 AI Similar Users</h3>

    @if(empty($matches))
        <p class="text-muted">No similar users found.</p>
    @else

        @foreach($matches as $m)
            <div class="card shadow-sm mb-3">
                <div class="card-body d-flex justify-content-between">
                    
                    <div>
                        <h5 class="fw-bold">{{ $m['user']->name }}</h5>
                        <p class="text-muted small">Similarity: {{ $m['score'] }}%</p>
                        <p class="small">
                            {{ $m['user']->career_suggestion }}
                        </p>
                    </div>

                    <button class="btn btn-outline-primary btn-sm">
                        View Profile
                    </button>

                </div>
            </div>
        @endforeach

    @endif

</div>
@endsection
