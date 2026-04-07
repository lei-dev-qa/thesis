@props(['assessment_batch'])

<div class="modal fade" id="completeBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.assessment-batches.close', $assessment_batch) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mark Batch as Done</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Important:</strong> All assigned applicants must have assessment results (Pass/Fail)
                        before marking this batch as done.
                    </div>
                    <p>Are you sure you want to mark <strong>{{ $assessment_batch->batch_name }}</strong> as completed?
                    </p>
                    <p class="text-muted">This batch will be moved to Assessment History.</p>

                    <div class="mt-3">
                        <strong>Current Status:</strong>
                        <ul class="mt-2">
                            <li>Total Assigned: {{ $assessment_batch->applications->count() }}</li>
                            <li>With Results: <span
                                    class="badge bg-success">{{ $assessment_batch->applications->filter(fn($a) => $a->assessmentResult)->count() }}</span>
                            </li>
                            <li>Pending: <span
                                    class="badge bg-warning">{{ $assessment_batch->applications->filter(fn($a) => !$a->assessmentResult)->count() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Yes, Mark as Done</button>
                </div>
            </form>
        </div>
    </div>
</div>
