@props(['app', 'isSecondReassessment'])

<div class="modal fade" id="uploadReceiptModal{{ $app->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ $isSecondReassessment ? route('admin.reassessment.upload-second-official-receipt', $app->id) : route('admin.reassessment.upload-official-receipt', $app->id) }}" 
                method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Upload Official Receipt</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Upload the official receipt for {{ $isSecondReassessment ? '2nd' : '' }} reassessment payment.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Official Receipt Photo <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" 
                            name="{{ $isSecondReassessment ? 'second_reassessment_official_receipt_photo' : 'reassessment_official_receipt_photo' }}" 
                            accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">Accepted formats: JPG, PNG, PDF (Max: 5MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>
