@extends('layouts.admin')

@section('title', 'Assessment Batch Details - SHC-TVET')
@section('page-title', 'Assessment Batch: ' . $assessment_batch->batch_name)

@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('admin.assessment-batches.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Batches
                </a>
            </div>
        </div>

        <!-- Batch Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="fw-bold mb-1">{{ $assessment_batch->batch_name }}</h4>
                        <span
                            class="badge bg-{{ $assessment_batch->status == 'completed' ? 'success' : ($assessment_batch->status == 'ongoing' ? 'warning' : 'primary') }}">
                            {{ ucfirst($assessment_batch->status) }}
                        </span>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge bg-light text-dark fs-6 me-2">
                            {{ $assessment_batch->applications->count() }}/{{ $assessment_batch->max_applicants }} Assigned
                        </span>
                        <span class="badge bg-warning">{{ $eligibleApplicants->count() }} Eligible</span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Batch Name:</strong> {{ $assessment_batch->batch_name }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>NC Program:</strong> {{ $assessment_batch->nc_program }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Assessment Date:</strong> {{ $assessment_batch->assessment_date->format('F d, Y') }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Time:</strong> {{ $assessment_batch->start_time->format('H:i') }} -
                        {{ $assessment_batch->end_time->format('H:i') }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Intensive Review Day 1:</strong>
                        {{ $assessment_batch->intensive_review_day1->format('F d, Y') }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Time:</strong> {{ $assessment_batch->intensive_review_day1_start->format('H:i') }} -
                        {{ $assessment_batch->intensive_review_day1_end->format('H:i') }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Intensive Review Day 2:</strong>
                        {{ $assessment_batch->intensive_review_day2->format('F d, Y') }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Time:</strong> {{ $assessment_batch->intensive_review_day2_start->format('H:i') }} -
                        {{ $assessment_batch->intensive_review_day2_end->format('H:i') }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Venue:</strong> {{ $assessment_batch->venue }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Assessor:</strong> {{ $assessment_batch->assessor_name ?? 'Not Assigned' }}
                    </div>
                    @if ($assessment_batch->remarks)
                        <div class="col-md-12 mb-3">
                            <strong>Remarks:</strong> {{ $assessment_batch->remarks }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-12">
                @if (
                    $assessment_batch->status !== 'completed' &&
                        $assessment_batch->applications->count() < $assessment_batch->max_applicants)
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addApplicantModal">
                        <i class="bi bi-person-plus"></i> Add Applicant
                    </button>
                @endif

                @if ($assessment_batch->applications->count() > 0 && $assessment_batch->status === 'scheduled')
                    @if ($assessment_batch->hasScheduleNotificationsSent())
                        <!-- Show disabled button with different text when already sent -->
                        <button type="button" class="btn btn-primary" disabled>
                            <i class="fas fa-check"></i> Schedule Notifications Sent
                        </button>
                    @else
                        <!-- Show normal send button when not sent yet -->
                        <form action="{{ route('admin.assessment-batches.send-schedule', $assessment_batch) }}"
                            method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Send schedule notifications to all {{ $assessment_batch->applications->count() }} applicant(s) in this batch?')">
                                <i class="fas fa-envelope"></i> Send Schedule Notifications
                            </button>
                        </form>
                    @endif
                @endif

                <!-- Attendance PDF Button -->
                <a href="{{ route('admin.assessment-batches.attendance-pdf', $assessment_batch) }}" target="_blank"
                    class="btn btn-secondary">
                    <i class="bi bi-file-pdf"></i> Print Attendance Sheet
                </a>

                @if ($assessment_batch->status !== 'completed')
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeBatchModal">
                        <i class="bi bi-check-circle-fill"></i> Mark Batch as Done
                    </button>
                @endif
            </div>
        </div>

        <!-- Assigned Applicants -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Assigned Applicants ({{ $assessment_batch->applications->count() }})</h5>
            </div>
            <div class="card-body">
                @if ($assessment_batch->applications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Reference Number</th>
                                    <th>Assessment Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assessment_batch->applications as $index => $applicant)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $applicant->surname }}, {{ $applicant->firstname }}
                                            {{ $applicant->middlename }}
                                        </td>
                                        <td>{{ $applicant->user->email ?? 'N/A' }}</td>
                                        <td>
                                            @if ($applicant->reference_number)
                                                <span class="badge bg-success">{{ $applicant->reference_number }}</span>
                                            @else
                                                <span class="badge bg-warning">
                                                    Missing
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $res = $applicant
                                                    ->assessmentResults()
                                                    ->where('assessment_batch_id', $assessment_batch->id)
                                                    ->first();
                                            @endphp

                                            @if ($res)
                                                @php
                                                    $badgeClass = match ($res->result) {
                                                        \App\Models\Assessment\AssessmentResult::RESULT_PASS => 'success',
                                                        \App\Models\Assessment\AssessmentResult::RESULT_FAIL => 'danger',
                                                        \App\Models\Assessment\AssessmentResult::RESULT_INCOMPLETE => 'secondary',
                                                        default => 'secondary',
                                                    };
                                                    $scoreHtml =
                                                        $res->score === null
                                                            ? ''
                                                            : '<small class="text-muted ms-2">Score: ' .
                                                                e($res->score) .
                                                                '</small>';
                                                @endphp

                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <span
                                                        class="badge bg-{{ $badgeClass }}">{{ strtoupper($res->result) }}</span>{!! $scoreHtml !!}

                                                    {{-- Add Edit Buttons - Show BOTH options to allow changing result type --}}
                                                    @if ($assessment_batch->status !== 'completed')
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-sm btn-outline-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#completeAssessmentModal{{ $applicant->id }}"
                                                                title="Mark as Competent">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#failAssessmentModal{{ $applicant->id }}"
                                                                title="Mark as NYC">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif (in_array($assessment_batch->status, ['scheduled', 'ongoing']))
                                                <div class="d-flex gap-1 btn-group btn-group-sm ">
                                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#completeAssessmentModal{{ $applicant->id }}">
                                                        Competent
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#failAssessmentModal{{ $applicant->id }}">
                                                        NYC
                                                    </button>
                                                </div>
                                            @else
                                                <small class="text-muted">
                                                    Assessment {{ ucfirst($applicant->assessment_status ?? 'pending') }}
                                                </small>
                                            @endif
                                        </td>


                                        <td>
                                            <a href="{{ route('admin.applications.show', $applicant) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if ($assessment_batch->status !== 'completed' && !$applicant->assessmentResult)
                                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                    data-bs-target="#removeApplicantModal{{ $applicant->id }}">
                                                    <i class="bi bi-dash-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    <!-- Remove Applicant Modal -->
                                    @include('admin.assessment.component.remove-applicant', [
                                        'assessment_batch' => $assessment_batch,
                                        'applicant' => $applicant,
                                    ])
                                @endforeach
                            </tbody>
                        </table>
                        @foreach ($assessment_batch->applications as $applicant)
                            @include('admin.assessment.component.assessment-completion')
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3">No applicants assigned to this batch yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Applicant Modal -->
    @include('admin.assessment.component.add-applicant', [
        'assessment_batch' => $assessment_batch,
        'eligibleApplicants' => $eligibleApplicants,
    ])
    <!-- Mark Batch as Done Modal -->
    @include('admin.assessment.component.complete-batch', ['assessment_batch' => $assessment_batch])

@endsection
