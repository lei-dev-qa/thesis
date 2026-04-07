{{-- ============================= --}}
{{-- COC PROGRAMS DATA (ONE FILE) --}}
{{-- ============================= --}}
@php
    $cocPrograms = [
        'TOURISM PROMOTION SERVICES NC II' => [
            ['code' => 'COC 1', 'title' => 'Provide Information on Tourism Products and Services'],
            ['code' => 'COC 2', 'title' => 'Promote Tourism Products and Services'],
        ],
        'VISUAL GRAPHIC DESIGN NC III' => [
            ['code' => 'COC 1', 'title' => 'Develop designs for logo and print media'],
            ['code' => 'COC 2', 'title' => 'Develop designs for user experience and user interface'],
            ['code' => 'COC 3', 'title' => 'Develop designs for product packaging'],
            ['code' => 'COC 4', 'title' => 'Design booth and product window/display'],
        ],
        'EVENTS MANAGEMENT SERVICES NC III' => [
            ['code' => 'COC 1', 'title' => 'Pre-Event Planning Services'],
            ['code' => 'COC 2', 'title' => 'Online and/or On-site Events Management Services'],
        ],
        'BOOKKEEPING NC III' => [
            ['code' => 'COC 1', 'title' => 'Journalize Transactions'],
            ['code' => 'COC 2', 'title' => 'Post Journal Entries and Prepare Trial Balance'],
        ],
        'PHARMACY SERVICES NC III' => [
            ['code' => 'COC 1', 'title' => 'Assist in Dispensing Medicines'],
            ['code' => 'COC 2', 'title' => 'Perform Pharmaceutical Calculations'],
            ['code' => 'COC 3', 'title' => 'Perform Inventory Management in Pharmacy'],
        ],
    ];
@endphp


{{-- ============================= --}}
{{-- MODALS PER APPLICANT --}}
{{-- ============================= --}}
@foreach ($assessment_batch->applications as $applicant)
    @php
        // Get existing assessment result
        $existingResult = $applicant->assessmentResults()
            ->where('assessment_batch_id', $assessment_batch->id)
            ->first();
        
        $existingCocResults = $existingResult ? $existingResult->cocResults : collect();
        
        // Check if we're changing result type
        $isChangingToCompetent = $existingResult && $existingResult->result !== 'Competent';
        $isChangingToNYC = $existingResult && $existingResult->result === 'Competent';
    @endphp

    {{-- ================= PASS/COMPETENT MODAL ================= --}}
    <div class="modal fade" id="completeAssessmentModal{{ $applicant->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST"
                    action="{{ route('admin.assessment-batches.mark-completed', [$assessment_batch, $applicant]) }}">
                    @csrf
                    <input type="hidden" name="result" value="Competent">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            @if ($isChangingToCompetent)
                                <i class="bi bi-arrow-left-right"></i> Change Result to Competent
                            @elseif ($existingResult && $existingResult->result === 'Competent')
                                <i class="bi bi-pencil"></i> Edit Competent Result
                            @else
                                <i class="bi bi-check-circle"></i> Mark as Competent
                            @endif
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        @if ($isChangingToCompetent)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                <strong>Warning:</strong> You are changing the result from 
                                <span class="badge bg-danger">{{ strtoupper($existingResult->result) }}</span> to 
                                <span class="badge bg-success">COMPETENT</span>
                            </div>
                        @elseif ($existingResult && $existingResult->result === 'Competent')
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You are editing an existing Competent result.
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Applicant:</strong> {{ $applicant->firstname }} {{ $applicant->surname }}<br>
                            <strong>Program:</strong> {{ $applicant->title_of_assessment_applied_for }}
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks (optional)</label>
                            <textarea name="remarks" class="form-control" rows="3">{{ $existingResult && $existingResult->result === 'Competent' ? $existingResult->remarks : '' }}</textarea>
                        </div>

                        @if ($isChangingToCompetent)
                            <div class="alert alert-info">
                                <small><i class="bi bi-info-circle"></i> Previous COC results will be removed when changing to Competent.</small>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            @if ($isChangingToCompetent)
                                <i class="bi bi-arrow-left-right"></i> Change to Competent
                            @elseif ($existingResult && $existingResult->result === 'Competent')
                                <i class="bi bi-save"></i> Update Result
                            @else
                                <i class="bi bi-check-circle"></i> Confirm Competent
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ================= FAIL / NYC MODAL ================= --}}
    @php
        $programKey = strtoupper(trim($applicant->title_of_assessment_applied_for));
        $cocs = $cocPrograms[$programKey] ?? [];
    @endphp

    <div class="modal fade" id="failAssessmentModal{{ $applicant->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST"
                    action="{{ route('admin.assessment-batches.mark-completed', [$assessment_batch, $applicant]) }}">
                    @csrf
                    <input type="hidden" name="result" value="Not Yet Competent">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            @if ($isChangingToNYC)
                                <i class="bi bi-arrow-left-right"></i> Change Result to Not Yet Competent
                            @elseif ($existingResult && $existingResult->result === 'Not Yet Competent')
                                <i class="bi bi-pencil"></i> Edit NYC Result
                            @else
                                <i class="bi bi-x-circle"></i> Mark as Not Yet Competent
                            @endif
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        @if ($isChangingToNYC)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                <strong>Warning:</strong> You are changing the result from 
                                <span class="badge bg-success">COMPETENT</span> to 
                                <span class="badge bg-danger">NOT YET COMPETENT</span>
                            </div>
                        @elseif ($existingResult && $existingResult->result === 'Not Yet Competent')
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You are editing an existing NYC result. Current selections are pre-filled below.
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <strong>Applicant:</strong> {{ $applicant->firstname }} {{ $applicant->surname }} <br>
                            <strong>Program:</strong> {{ $applicant->title_of_assessment_applied_for }}
                        </div>

                        <h6>Select COC Results:</h6>

                        @if (count($cocs) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="120">COC Code</th>
                                            <th>COC Title</th>
                                            <th width="250" class="text-center">Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cocs as $index => $coc)
                                            @php
                                                // Find existing COC result (only if current result is NYC)
                                                $existingCoc = ($existingResult && $existingResult->result === 'Not Yet Competent') 
                                                    ? $existingCocResults->firstWhere('coc_code', $coc['code']) 
                                                    : null;
                                                $hasExistingResult = $existingCoc !== null;
                                                $existingCocResult = $hasExistingResult ? $existingCoc->result : null;
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $coc['code'] }}</strong></td>
                                                <td>{{ $coc['title'] }}</td>
                                                <td class="text-center">
                                                    {{-- Buttons (shown initially or when no existing result) --}}
                                                    <div id="btns-{{ $applicant->id }}-{{ $index }}" 
                                                         class="{{ $hasExistingResult ? 'd-none' : '' }}">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-success me-1"
                                                            onclick="selectCOC({{ $applicant->id }}, {{ $index }}, 'competent', '{{ $coc['code'] }}')">
                                                            <i class="bi bi-check-circle"></i> Competent
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="selectCOC({{ $applicant->id }}, {{ $index }}, 'not_yet_competent', '{{ $coc['code'] }}')">
                                                            <i class="bi bi-x-circle"></i> NYC
                                                        </button>
                                                    </div>

                                                    {{-- Badge (hidden initially, shown if existing result) --}}
                                                    <div id="badge-{{ $applicant->id }}-{{ $index }}"
                                                        class="{{ $hasExistingResult ? '' : 'd-none' }}">
                                                        <span class="badge {{ $existingCocResult === 'competent' ? 'bg-success' : 'bg-danger' }}"
                                                            id="badge-text-{{ $applicant->id }}-{{ $index }}">
                                                            {{ $existingCocResult === 'competent' ? 'COMPETENT' : 'NOT YET COMPETENT' }}
                                                        </span>
                                                        <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2"
                                                            onclick="resetCOC({{ $applicant->id }}, {{ $index }})"
                                                            title="Change selection">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    </div>

                                                    {{-- Hidden inputs --}}
                                                    <input type="hidden" name="coc_results[{{ $coc['code'] }}][code]"
                                                        value="{{ $coc['code'] }}">
                                                    <input type="hidden"
                                                        name="coc_results[{{ $coc['code'] }}][title]"
                                                        value="{{ $coc['title'] }}">
                                                    <input type="hidden"
                                                        id="result-{{ $applicant->id }}-{{ $index }}"
                                                        name="coc_results[{{ $coc['code'] }}][result]" 
                                                        value="{{ $existingCocResult ?? '' }}"
                                                        required>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger">
                                No COCs defined for: <strong>{{ $programKey }}</strong>
                            </div>
                        @endif
                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Overall Score (Optional)</label>
                            <input type="number" name="score" class="form-control" min="0" max="100"
                                step="0.01" value="{{ $existingResult && $existingResult->result === 'Not Yet Competent' ? $existingResult->score : '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remarks" class="form-control" rows="3">{{ $existingResult && $existingResult->result === 'Not Yet Competent' ? $existingResult->remarks : '' }}</textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        @if (count($cocs) > 0)
                            <button type="submit" class="btn btn-danger">
                                @if ($isChangingToNYC)
                                    <i class="bi bi-arrow-left-right"></i> Change to NYC
                                @elseif ($existingResult && $existingResult->result === 'Not Yet Competent')
                                    <i class="bi bi-save"></i> Update Result
                                @else
                                    <i class="bi bi-save"></i> Save NYC Result
                                @endif
                            </button>
                        @else
                            <button type="button" class="btn btn-danger" disabled>
                                Cannot Save
                            </button>
                        @endif
                    </div>

                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
function selectCOC(applicantId, index, result, cocCode) {
    // Confirm selection
    const resultText = result === 'competent' ? 'COMPETENT' : 'NOT YET COMPETENT';
    const confirmed = confirm(`Mark ${cocCode} as ${resultText}?`);
    
    if (!confirmed) return;
    
    // Hide buttons
    document.getElementById(`btns-${applicantId}-${index}`).classList.add('d-none');
    
    // Show badge
    const badgeDiv = document.getElementById(`badge-${applicantId}-${index}`);
    const badgeText = document.getElementById(`badge-text-${applicantId}-${index}`);
    
    if (result === 'competent') {
        badgeText.className = 'badge bg-success';
        badgeText.textContent = 'COMPETENT';
    } else {
        badgeText.className = 'badge bg-danger';
        badgeText.textContent = 'NOT YET COMPETENT';
    }
    
    badgeDiv.classList.remove('d-none');
    
    // Set hidden input
    document.getElementById(`result-${applicantId}-${index}`).value = result;
}

function resetCOC(applicantId, index) {
    // Hide badge
    document.getElementById(`badge-${applicantId}-${index}`).classList.add('d-none');
    
    // Show buttons
    document.getElementById(`btns-${applicantId}-${index}`).classList.remove('d-none');
    
    // Clear hidden input
    document.getElementById(`result-${applicantId}-${index}`).value = '';
}
</script>
