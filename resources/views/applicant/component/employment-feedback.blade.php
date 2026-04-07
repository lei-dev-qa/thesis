@props(['app'])

<div class="modal fade" id="employmentModal{{ $app->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('applicant.employment-feedback.store', $app->id) }}">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-briefcase"></i> TWSP Employment Feedback
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Please provide your current employment
                        information. This helps us track the effectiveness of our TWSP programs.
                    </div>

                    <div class="row">
                        {{-- Date Employed --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Employed <span class="text-danger">*</span></label>
                            <input type="date" name="date_employed" class="form-control"
                                value="{{ $app->employmentRecord ? $app->employmentRecord->date_employed->format('Y-m-d') : '' }}"
                                required>
                        </div>

                        {{-- Occupation --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Occupation <span class="text-danger">*</span></label>
                            <input type="text" name="occupation" class="form-control"
                                value="{{ $app->employmentRecord->occupation ?? '' }}"
                                placeholder="e.g., Bookkeeper, Event Coordinator, Tourism Assistant" required>
                        </div>

                        {{-- Name of Employer --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name of Employer <span class="text-danger">*</span></label>
                            <input type="text" name="employer_name" class="form-control"
                                value="{{ $app->employmentRecord->employer_name ?? '' }}"
                                placeholder="Company/Organization Name" required>
                        </div>

                        {{-- Classification of Employer --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Classification of Employer <span
                                    class="text-danger">*</span></label>
                            <select name="employer_classification" class="form-select" required>
                                <option value="">Select Classification</option>
                                <option value="Private"
                                    {{ ($app->employmentRecord->employer_classification ?? '') == 'Private' ? 'selected' : '' }}>
                                    Private
                                </option>
                                <option value="Government"
                                    {{ ($app->employmentRecord->employer_classification ?? '') == 'Government' ? 'selected' : '' }}>
                                    Government
                                </option>
                                <option value="NGO"
                                    {{ ($app->employmentRecord->employer_classification ?? '') == 'NGO' ? 'selected' : '' }}>
                                    NGO
                                </option>
                                <option value="Self-Employed"
                                    {{ ($app->employmentRecord->employer_classification ?? '') == 'Self-Employed' ? 'selected' : '' }}>
                                    Self-Employed
                                </option>
                            </select>
                        </div>

                        {{-- Address of Employer --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address of Employer <span class="text-danger">*</span></label>
                            <textarea name="employer_address" class="form-control" rows="3"
                                placeholder="Complete address of employer" required>{{ $app->employmentRecord->employer_address ?? '' }}</textarea>
                        </div>

                        {{-- Monthly Income --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monthly Income/Salary <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" name="monthly_income" class="form-control" step="0.01"
                                    min="0" value="{{ $app->employmentRecord->monthly_income ?? '' }}"
                                    placeholder="0.00" required>
                            </div>
                            <small class="text-muted">Enter your gross monthly income</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-check-circle"></i>
                        {{ $app->employmentRecord ? 'Update' : 'Submit' }} Employment Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
