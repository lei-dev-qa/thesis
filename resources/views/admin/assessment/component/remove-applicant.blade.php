@props(['assessment_batch', 'applicant'])

<div class="modal fade" id="removeApplicantModal{{ $applicant->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST"
                action="{{ route('admin.assessment-batches.unassign-applicant', [$assessment_batch, $applicant]) }}">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Remove Applicant from Batch</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action will remove the applicant
                        from the batch.
                    </div>
                    <p>Are you sure you want to remove
                        <strong>{{ $applicant->firstname }}
                            {{ $applicant->surname }}</strong> from this batch?
                    </p>
                    <p class="text-muted"><small>The applicant's assessment status will
                            be reset and they can be assigned to another batch
                            later.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Remove
                        Applicant</button>
                </div>
            </form>
        </div>
    </div>
</div>
