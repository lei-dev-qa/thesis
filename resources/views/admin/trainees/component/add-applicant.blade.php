@props(['batch', 'availableApplicants'])

<div class="modal fade" id="addApplicantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.training-batches.add-applicant', $batch) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Applicant to Batch {{ $batch->batch_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($availableApplicants->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Select Applicant</label>
                            <select name="application_id" class="form-select" required>
                                <option value="">-- Select Applicant --</option>
                                @foreach ($availableApplicants as $applicant)
                                    <option value="{{ $applicant->id }}">
                                        {{ $applicant->firstname }} {{ $applicant->surname }}
                                        ({{ $applicant->user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Only showing approved applicants for <strong>{{ $batch->nc_program }}</strong>
                                who are not enrolled in any batch.
                            </small>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            No available applicants found for this program.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if ($availableApplicants->count() > 0)
                        <button type="submit" class="btn btn-primary">Add Applicant</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>