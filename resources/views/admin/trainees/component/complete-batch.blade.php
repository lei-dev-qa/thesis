@props(['batch', 'completedCount', 'failedCount'])

<div class="modal fade" id="completeBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.training-batches.complete', $batch) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mark Batch as Done</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Important:</strong> All trainees must have a result (Completed or Failed) before
                        marking this batch as done.
                    </div>
                    <p>Are you sure you want to mark <strong>{{ $batch->nc_program }} - Batch
                            {{ $batch->batch_number }}</strong> as completed?</p>
                    <p class="text-muted">This batch will be moved to Training History.</p>

                    <div class="mt-3">
                        <strong>Current Status:</strong>
                        <ul class="mt-2">
                            <li>Total Trainees: {{ $batch->applications->count() }}</li>
                            <li>Completed: <span class="badge bg-success">{{ $completedCount }}</span></li>
                            <li>Failed: <span class="badge bg-danger">{{ $failedCount }}</span></li>
                            <li>Pending: <span
                                    class="badge bg-warning">{{ $batch->applications->count() - ($completedCount + $failedCount) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Yes, Mark as Done</button>
                </div>
            </form>
        </div>
    </div>
</div>
