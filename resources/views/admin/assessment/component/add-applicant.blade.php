@props(['assessment_batch', 'eligibleApplicants'])

<div class="modal fade" id="addApplicantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.assessment-batches.add-applicants', $assessment_batch) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Applicant to Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($eligibleApplicants->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Select Applicant</label>
                            <select name="application_id" class="form-select" required>
                                <option value="">-- Select Applicant --</option>
                                @foreach ($eligibleApplicants as $applicant)
                                    <option value="{{ $applicant->id }}">
                                        {{ $applicant->surname }}, {{ $applicant->firstname }}
                                        {{ $applicant->middlename }}
                                        ({{ $applicant->user->email }})
                                        @if ($applicant->training_completed_at)
                                            - Completed: {{ $applicant->training_completed_at->format('M d, Y') }}
                                        @endif
                                        @if ($applicant->reassessment_payment_status === 'verified')
                                            - [REASSESSMENT]
                                        @elseif ($applicant->training_completed_at)
                                            - Completed: {{ $applicant->training_completed_at->format('M d, Y') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Only showing eligible applicants for
                                <strong>{{ $assessment_batch->nc_program }}</strong>
                                who are not assigned to any batch.
                            </small>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            No eligible applicants available to add.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if ($eligibleApplicants->count() > 0)
                        <button type="submit" class="btn btn-primary">Add Applicant</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
