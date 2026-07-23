@extends('layouts.app')

@section('title', 'Meetings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="m-0"><i class="fas fa-handshake text-primary me-2"></i> Meeting Management</h4>
            <a href="{{ route('meetings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Schedule Meeting
            </a>
        </div>
    </div>

    @include('layouts.alerts')

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Date & Time</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                    <tr>
                        <td><strong>{{ $meeting->title }}</strong></td>
                        <td>{{ $meeting->department ? $meeting->department->department_name : 'General' }}</td>
                        <td>
                            <div><i class="far fa-calendar-alt text-muted"></i> {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y') }}</div>
                            <div class="small text-muted"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}</div>
                        </td>
                        <td>
                            @if($meeting->meeting_mode == 'Online')
                                <span class="badge bg-success"><i class="fas fa-video"></i> Online</span>
                            @elseif($meeting->meeting_mode == 'Offline')
                                <span class="badge bg-secondary"><i class="fas fa-building"></i> Offline</span>
                            @else
                                <span class="badge bg-info"><i class="fas fa-random"></i> Hybrid</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $meeting->status == 'Scheduled' ? 'warning' : ($meeting->status == 'Completed' ? 'success' : 'secondary') }}">
                                {{ $meeting->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('meetings.show', $meeting->id) }}" class="btn btn-sm btn-light border"><i class="fas fa-eye text-primary"></i> View</a>
                            <form action="{{ route('meetings.destroy', $meeting->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border" onclick="return confirm('Delete this meeting?')"><i class="fas fa-trash text-danger"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No meetings scheduled.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
