@props(['application', 'fullName'])

<div class="modal fade" id="addEmploymentModal{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST"
                action="{{ route('admin.employment-feedback.store', $application->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Employment Record</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Applicant Name</label>
                        <input type="text" class="form-control-plaintext"
                            value="{{ $fullName }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Employed <span
                                class="text-danger">*</span></label>
                        <input type="date" name="date_employed" class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Occupation <span
                                class="text-danger">*</span></label>
                        <input type="text" name="occupation" class="form-control"
                            placeholder="e.g., Bookkeeper" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name of Employer <span
                                class="text-danger">*</span></label>
                        <input type="text" name="employer_name" class="form-control"
                            placeholder="e.g., ABC Corporation" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address of Employer <span
                                class="text-danger">*</span></label>
                        <textarea name="employer_address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Classification of Employer <span
                                class="text-danger">*</span></label>
                        <select name="employer_classification" class="form-select"
                            required>
                            <option value="">Select Classification</option>
                            <option value="Private">Private</option>
                            <option value="Government">Government</option>
                            <option value="NGO">NGO</option>
                            <option value="Self-Employed">Self-Employed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monthly Income/Salary <span
                                class="text-danger">*</span></label>
                        <input type="number" name="monthly_income" class="form-control"
                            step="0.01" min="0" placeholder="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
