@props(['application', 'fullName'])

<!-- View/Edit Employment Modal -->
<div class="modal fade" id="viewEmploymentModal{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST"
                action="{{ route('admin.employment-feedback.update', $application->employmentRecord->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Employment Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Applicant Name</label>
                        <input type="text" class="form-control-plaintext" value="{{ $fullName }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Employed <span class="text-danger">*</span></label>
                        <input type="date" name="date_employed" class="form-control"
                            value="{{ $application->employmentRecord->date_employed ? $application->employmentRecord->date_employed->format('Y-m-d') : '' }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Occupation <span class="text-danger">*</span></label>
                        <input type="text" name="occupation" class="form-control"
                            value="{{ $application->employmentRecord->occupation ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name of Employer <span class="text-danger">*</span></label>
                        <input type="text" name="employer_name" class="form-control"
                            value="{{ $application->employmentRecord->employer_name ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address of Employer <span class="text-danger">*</span></label>
                        <textarea name="employer_address" class="form-control" rows="2" required>{{ $application->employmentRecord->employer_address ?? '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Classification of Employer <span
                                class="text-danger">*</span></label>
                        <select name="employer_classification" class="form-select" required>
                            <option value="">Select Classification</option>
                            <option value="Private"
                                {{ ($application->employmentRecord->employer_classification ?? '') == 'Private' ? 'selected' : '' }}>
                                Private</option>
                            <option value="Government"
                                {{ ($application->employmentRecord->employer_classification ?? '') == 'Government' ? 'selected' : '' }}>
                                Government</option>
                            <option value="NGO"
                                {{ ($application->employmentRecord->employer_classification ?? '') == 'NGO' ? 'selected' : '' }}>
                                NGO</option>
                            <option value="Self-Employed"
                                {{ ($application->employmentRecord->employer_classification ?? '') == 'Self-Employed' ? 'selected' : '' }}>
                                Self-Employed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monthly Income/Salary <span class="text-danger">*</span></label>
                        <input type="number" name="monthly_income" class="form-control" step="0.01" min="0"
                            value="{{ $application->employmentRecord->monthly_income ?? '' }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
