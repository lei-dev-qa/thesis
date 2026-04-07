@props(['twspAnnouncement', 'twspAvailable'])

<!-- Application Type Modal -->
<div class="modal fade" id="applicationTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Select Application Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-3">
                    <!-- TWSP Option -->
                    <div class="card border-success application-type-card {{ !$twspAvailable ? 'opacity-50' : '' }}"
                        style="cursor: {{ $twspAvailable ? 'pointer' : 'not-allowed' }};"
                        @if ($twspAvailable) onclick="selectApplicationType('TWSP')" @endif>
                        <div class="card-body">
                            <h5 class="card-title text-success">
                                <i class="fas fa-graduation-cap me-2"></i>
                                Training For Work Scholarship Program (TWSP)
                                @if (!$twspAvailable)
                                    <span class="badge bg-danger ms-2">Unavailable</span>
                                @else
                                    <span class="badge bg-success ms-2">
                                        {{ $twspAnnouncement->getRemainingSlots() }}/{{ $twspAnnouncement->total_slots }}
                                        Slots
                                    </span>
                                @endif
                            </h5>
                            <p class="card-text text-muted mb-0">
                                Complete the full training program before taking the assessment.
                                Includes comprehensive training and certification.
                            </p>
                            @if (!$twspAvailable)
                                <small class="text-danger d-block mt-2">
                                    <i class="fas fa-info-circle"></i> TWSP applications are currently closed. Please
                                    check back later.
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- Assessment Only Option -->
                    <div class="card border-primary application-type-card" style="cursor: pointer;"
                        onclick="selectApplicationType('Assessment Only')">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Assessment Only
                            </h5>
                            <p class="card-text text-muted mb-0">
                                Skip training and proceed directly to competency assessment.
                                Suitable for those with prior experience.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
