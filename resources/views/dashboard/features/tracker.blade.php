@extends('layouts.app')

@section('content')
<div class="container mt-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Header + Progress --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">
                Career Progress Tracker
            </h4>
            <p class="text-muted mb-2">
                Career: <strong>{{ $progress->career_name }}</strong>
            </p>

            <div class="d-flex align-items-center mb-2">
                <div class="flex-grow-1 me-3">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $progress->progress_percent }}%;"
                             aria-valuenow="{{ $progress->progress_percent }}"
                             aria-valuemin="0" aria-valuemax="100">
                            {{ $progress->progress_percent }}%
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-3">
                <span class="badge bg-primary">
                    Total Tasks: {{ $totalTasks }}
                </span>
                <span class="badge bg-success">
                    Completed: {{ $completedTasks }}
                </span>
                <span class="badge bg-warning text-dark">
                    Pending: {{ $pendingTasks }}
                </span>
            </div>
        </div>
    </div>

   {{-- Add Task Form --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header">
        <strong>Add New Task</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('career.tracker.task.add') }}">
            @csrf

            <!-- REQUIRED FIELD (You forgot this!) -->
            <input type="hidden" name="progress_id" value="{{ $progress->id }}">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Task Title</label>
                    <input type="text" name="task_title" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Phase (optional)</label>
                    <input type="text" name="phase_name" class="form-control"
                           placeholder="e.g. Basics, Projects, Interview Prep">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description (optional)</label>
                    <textarea name="task_description" rows="2" class="form-control"
                              placeholder="Describe what you will do for this step..."></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Order Number</label>
                    <input type="number" name="order_number" value="{{ $tasks->count() + 1 }}" class="form-control">
                </div>
            </div>

            <div class="mt-3 text-end">
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Task
                </button>
            </div>
        </form>
    </div>
</div>

    {{-- Task List --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Your Tasks</strong>
        </div>
        <div class="card-body p-0">
            @if($tasks->isEmpty())
                <p class="p-3 text-muted mb-0">
                    No tasks yet. Add your first step towards your career goal 👇
                </p>
            @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;"></th>
                                <th>Task</th>
                                <th>Phase</th>
                                <th>Status</th>
                                <th style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr @if($task->is_completed) class="table-success" @endif>
                                    <td class="text-center">
                                        @if($task->is_completed)
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @else
                                            <i class="bi bi-circle text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $task->task_title }}</strong>
                                        @if($task->task_description)
                                            <div class="text-muted small">
                                                {{ $task->task_description }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $task->phase_name ?? '-' }}
                                    </td>
                                    <td>
                                        @if($task->is_completed)
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td class="d-flex gap-2">

                                        {{-- Toggle Complete --}}
                                        <form method="POST" 
                                              action="{{ route('career.tracker.task.toggle', $task->id) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-primary">
                                                @if($task->is_completed)
                                                    Pending
                                                @else
                                                    Done
                                                @endif
                                            </button>
                                        </form>
                                    
                                        {{-- DELETE BUTTON --}}
                                        <form method="POST"
                                              action="{{ route('career.tracker.task.delete', $task->id) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
