@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('admin.training-batches.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Batches
                </a>
            </div>
        </div>

        <!-- Batch Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="fw-bold mb-1">
                            {{ $batch->nc_program }} - Batch {{ $batch->batch_number }}
                        </h4>
                        <span
                            class="badge 
                            @if ($batch->status === 'scheduled') bg-info
                            @elseif($batch->status === 'ongoing') bg-warning
                            @elseif($batch->status === 'completed') bg-secondary
                            @elseif($batch->is_full) bg-success
                            @else bg-primary @endif">
                            {{ $batch->is_full && $batch->status === 'enrolling' ? 'FULL' : strtoupper($batch->status) }}
                        </span>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge bg-light text-dark fs-6 me-2">
                            {{ $batch->applications->count() }}/{{ $batch->max_students }} Enrolled
                        </span>
                        <span class="badge bg-success me-1">{{ $completedCount }} Completed</span>
                        <span class="badge bg-danger">{{ $failedCount }} Failed</span>
                    </div>
                </div>
            </div> <!-- Closing card-header -->

            @if ($batch->trainingSchedule)
                <div class="card-body">
                    <div class="mb-0">
                        <strong><i class="bi bi-calendar-check"></i> Schedule:</strong>
                        {{ $batch->trainingSchedule->start_date->format('M d, Y') }} -
                        {{ $batch->trainingSchedule->end_date->format('M d, Y') }} |
                        {{ $batch->trainingSchedule->days }} |
                        {{ $batch->trainingSchedule->start_time->format('h:i A') }} -
                        {{ $batch->trainingSchedule->end_time->format('h:i A') }} |
                        <strong>Venue:</strong> {{ $batch->trainingSchedule->venue }} |
                        <strong>Instructor:</strong> {{ $batch->trainingSchedule->instructor }}
                    </div>
                </div>
            @endif
        </div> <!-- Closing card -->

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-12">
                @if (!$batch->is_full)
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addApplicantModal">
                        <i class="bi bi-person-plus"></i> Add Applicant
                    </button>
                @endif
                {{-- Check if batch has a training schedule and applications --}}
                @if ($batch->trainingSchedule && $batch->trainingSchedule->applications->count() > 0)
                    {{-- Check if notifications were already sent --}}
                    @if ($batch->trainingSchedule->schedule_notifications_sent_at)
                        <button type="button" class="btn btn-primary" disabled>
                            <i class="fas fa-check"></i> Schedule Notifications Sent
                        </button>
                    @else
                        {{-- Show normal send button when not sent yet --}}
                        <form action="{{ route('admin.training-schedules.send-schedule', $batch->trainingSchedule) }}"
                            method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Send training schedule notifications to all {{ $batch->trainingSchedule->applications->count() }} applicant(s) in this schedule?')">
                                <i class="fas fa-envelope"></i> Send Schedule Notifications
                            </button>
                        </form>
                    @endif
                @endif

                @if ($batch->is_full && !$batch->hasSchedule())
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                        <i class="bi bi-calendar-plus"></i> Create Schedule
                    </button>
                @endif

                @if ($batch->status !== 'completed')
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeBatchModal">
                        <i class="bi bi-check-circle-fill"></i> Mark Batch as Done
                    </button>
                @endif
            </div>
        </div>

        <!-- Trainees List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-0">Enrolled Trainees</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <!-- Bulk Action Buttons -->
                        <div class="btn-group" id="bulkActions" style="display: none;">
                            <button type="button" class="btn btn-success btn-sm" onclick="bulkComplete()">
                                <i class="bi bi-check-circle"></i> Complete Selected
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="bulkFail()">
                                <i class="bi bi-x-circle"></i> Fail Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div> <!-- Closing card-header -->

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Reference Number</th>
                                <th>Training Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batch->applications as $index => $application)
                                <tr>
                                    <td>
                                        @if ($application->training_status === 'ongoing')
                                            <input type="checkbox" class="trainee-checkbox" value="{{ $application->id }}"
                                                onchange="toggleBulkActions()">
                                        @endif
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $application->firstname }} {{ $application->surname }}</td>
                                    <td>{{ $application->user->email }}</td>
                                    <td class="d-flex justify-content-center align-items-center">
                                        @if ($application->reference_number)
                                            <span class="badge bg-success">
                                                {{ $application->reference_number }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                Missing
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($application->training_status === 'enrolled') bg-primary
                                            @elseif($application->training_status === 'ongoing') bg-warning
                                            @elseif($application->training_status === 'completed') bg-success
                                            @elseif($application->training_status === 'failed') bg-danger @endif">
                                            {{ strtoupper($application->training_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.applications.show', $application) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if (
                                            $batch->status !== 'completed' &&
                                                !in_array($application->training_status, ['completed', 'failed']) &&
                                                $application->training_status !== 'ongoing')
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#removeApplicantModal{{ $application->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No trainees enrolled in this batch yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Individual Modals (Complete, Fail, Remove) -->
        @foreach ($batch->applications as $application)
            {{-- Remove Applicant Modal --}}
            @include('admin.trainees.component.remove-applicant', [
                'batch' => $batch,
                'application' => $application,
            ])
        @endforeach

        {{-- Add Applicant Modal --}}
        @include('admin.trainees.component.add-applicant', [
            'batch' => $batch,
            'availableApplicants' => $availableApplicants,
        ])

        {{-- Create Schedule Modal --}}
        @include('admin.trainees.component.create-schedule', ['batch' => $batch])

        {{-- Completion and Failure Modal  --}}
        @include('admin.trainees.component.bulk-actions')

        {{-- Mark Batch as Done Modal --}}
        @include('admin.trainees.component.complete-batch', [
            'batch' => $batch,
            'completedCount' => $completedCount,
            'failedCount' => $failedCount,
        ])

        {{-- <script>
            // Auto-submit forms after selection (optional enhancement)
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-submit quick complete form when remarks are selected
                const quickCompleteSelect = document.querySelector(
                    '#quickCompleteModal select[name="training_remarks"]');
                if (quickCompleteSelect) {
                    quickCompleteSelect.addEventListener('change', function() {
                        if (this.value && this.value !== '') {
                            // Optional: Auto-submit after 1 second delay
                            setTimeout(() => {
                                if (confirm('Auto-complete with selected remarks?')) {
                                    document.getElementById('quickCompleteForm').submit();
                                }
                            }, 1000);
                        }
                    });
                }
            });
        </script> --}}
    @endsection
