@props(['app'])

<div class="modal fade" id="paymentModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('applicant.payment.upload', $app->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Upload Payment Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Payment Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>GCash Number: <strong>0912-345-6789</strong></li>
                            <li>Account Name: <strong>SHC-TVET Training and Assessment Center</strong></li>
                            <li>Amount: <strong>₱500.00</strong></li>
                        </ul>
                    </div>

                    @if ($app->payment_status === 'rejected' && $app->payment_remarks)
                        <div class="alert alert-danger">
                            <strong>Previous submission was rejected:</strong><br>
                            {{ $app->payment_remarks }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="payment_proof{{ $app->id }}" class="form-label">
                            Upload GCash Screenshot or Receipt <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="payment_proof{{ $app->id }}"
                            name="payment_proof" accept="image/*,.pdf" required>
                        <small class="text-muted">Accepted formats: JPG, PNG, PDF (Max: 2MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit Payment Proof</button>
                </div>
            </form>
        </div>
    </div>
</div>
