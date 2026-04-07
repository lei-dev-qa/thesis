@props(['application'])

<div class="modal fade" id="requestCorrectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.applications.request-correction', $application) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Request Application Correction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        The applicant will receive an email notification and can edit their application.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">What needs to be corrected? <span
                                class="text-danger">*</span></label>
                        <textarea name="correction_message" class="form-control" rows="5" required
                            placeholder="Example:&#10;1. Email address appears incorrect&#10;2. Photo is too blurry&#10;3. Birthdate doesn't match ID"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane"></i> Send Correction Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
