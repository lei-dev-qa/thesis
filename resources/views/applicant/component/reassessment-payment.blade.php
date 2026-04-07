@props(['app', 'assessmentResult'])

<div class="modal fade" id="reassessmentModal{{ $app->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('applicant.reassessment.submit', $app->id) }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Pay for Reassessment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Program:</strong> {{ $app->title_of_assessment_applied_for }}<br>
                        <strong>Reassessment Fee:</strong> ₱500.00
                    </div>

                    <h6>Previous Assessment Results:</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>COC Code</th>
                                    <th>COC Title</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assessmentResult->cocResults as $coc)
                                    <tr>
                                        <td><strong>{{ $coc->coc_code }}</strong></td>
                                        <td>{{ $coc->coc_title }}</td>
                                        <td>
                                            @if ($coc->result === 'competent')
                                                <span class="badge bg-success">COMPETENT</span>
                                            @else
                                                <span class="badge bg-danger">NOT YET COMPETENT</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i>
                        <strong>Note:</strong> Reassessment requires you to retake ALL COCs, not just the NYC
                        ones.
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Upload Payment Proof <span
                                class="text-danger">*</span></label>
                        <input type="file" name="payment_proof" class="form-control"
                            accept="image/*,.pdf" required>
                        <small class="text-muted">Accepted formats: JPG, PNG, PDF (Max 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Reference Number (Optional)</label>
                        <input type="text" name="payment_reference" class="form-control"
                            placeholder="e.g., GCash Ref #, Bank Transaction #">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
