@props(['application'])

<div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payment.reject', $application->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Payment Proof</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        The applicant will be notified and can re-upload a new payment proof.
                    </div>

                    <div class="mb-3">
                        <label for="payment_remarks" class="form-label">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="payment_remarks" name="payment_remarks" rows="3" required
                            placeholder="e.g., Image is blurry, wrong amount, incomplete details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
