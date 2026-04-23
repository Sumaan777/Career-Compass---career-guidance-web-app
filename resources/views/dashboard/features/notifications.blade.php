@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="container mt-4">

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">All Notifications</h5>

            @if($unreadCount > 0)
            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-primary">Mark all as read</button>
            </form>
            @endif
        </div>

        <div class="card-body p-0">

            @if($notifications->count() == 0)

                <p class="text-center text-muted py-4">No notifications found.</p>

            @else
                <ul class="list-group list-group-flush">
                    @foreach($notifications as $note)
                        @php $data = $note->data; @endphp

                        <li class="list-group-item {{ is_null($note->read_at) ? 'bg-light' : '' }}">
                            <div class="d-flex">
                                <i class="bi {{ $data['icon'] ?? 'bi-bell' }} me-3 fs-5"></i>

                                <div>
                                    <strong>{{ $data['title'] }}</strong><br>
                                    <span class="text-muted small">{{ $data['message'] }}</span><br>
                                    <small class="text-muted">{{ $note->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="p-3">
                    {{ $notifications->links() }}
                </div>

            @endif

        </div>
    </div>

</div>

@endsection
