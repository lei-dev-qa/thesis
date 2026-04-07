@props(['app'])

<div class="modal fade" id="scheduleModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-check"></i> Assessment Schedule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="text-muted small">Program</label>
                    <h6 class="mb-0">{{ $app->title_of_assessment_applied_for }}</h6>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Batch Name</label>
                    <h6 class="mb-0">{{ $app->assessmentBatch->batch_name }}</h6>
                </div>

                {{-- Intensive Review Training Schedule --}}
                @if ($app->assessmentBatch->intensive_review_day1 || $app->assessmentBatch->intensive_review_day2)
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-warning">
                            <strong><i class="bi bi-book"></i> Intensive Review Training</strong>
                        </div>
                        <div class="card-body">
                            @if ($app->assessmentBatch->intensive_review_day1)
                                <div class="mb-2">
                                    <div class="row align-items-center">
                                        {{-- Column 1: Date --}}
                                        <div class="col-md-6">
                                            <label class="text-muted small">Day 1</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-calendar3"></i>
                                                {{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day1)->format('F d, Y') }}
                                                <span class="text-muted">
                                                    ({{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day1)->format('l') }})
                                                </span>
                                            </h6>
                                        </div>

                                        {{-- Column 2: Time --}}
                                        <div class="col-md-6">
                                            <label class="text-muted small">Time</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-clock"></i>
                                                {{ $app->assessmentBatch->intensive_review_day1_start->format('g:i a') }}
                                                -
                                                {{ $app->assessmentBatch->intensive_review_day1_end->format('g:i a') }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($app->assessmentBatch->intensive_review_day2)
                                <div class="mb-2">
                                    <div class="row align-items-center">
                                        {{-- Column 1: Date --}}
                                        <div class="col-md-6">
                                            <label class="text-muted small">Day 2</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-calendar3"></i>
                                                {{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day2)->format('F d, Y') }}
                                                <span class="text-muted">
                                                    ({{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day2)->format('l') }})
                                                </span>
                                            </h6>
                                        </div>

                                        {{-- Column 2: Time --}}
                                        <div class="col-md-6">
                                            <label class="text-muted small">Time</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-clock"></i>
                                                {{ $app->assessmentBatch->intensive_review_day2_start->format('g:i a') }}
                                                -
                                                {{ $app->assessmentBatch->intensive_review_day2_end->format('g:i a') }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Assessment Date --}}
                <div class="card bg-light mb-3">
                    <div class="card-header bg-success text-white">
                        <strong><i class="bi bi-clipboard-check"></i> Assessment Day</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="text-muted small">Date</label>
                                <h6 class="mb-0">
                                    <i class="bi bi-calendar3"></i>
                                    {{ \Carbon\Carbon::parse($app->assessmentBatch->assessment_date)->format('F d, Y') }}
                                </h6>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="text-muted small">Time</label>
                                <h6 class="mb-0">
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($app->assessmentBatch->start_time)->format('g:i a') }}
                                    -
                                    {{ \Carbon\Carbon::parse($app->assessmentBatch->end_time)->format('g:i a') }}
                                </h6>
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="text-muted small">Venue</label>
                            <h6 class="mb-0">
                                <i class="bi bi-geo-alt"></i>
                                {{ $app->assessmentBatch->venue ?? 'TESDA Assessment Center' }}
                            </h6>
                        </div>

                        @if ($app->assessmentBatch->assessor_name)
                            <div class="mt-2">
                                <label class="text-muted small">Assessor</label>
                                <h6 class="mb-0">
                                    <i class="bi bi-person-badge"></i>
                                    {{ $app->assessmentBatch->assessor_name }}
                                </h6>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($app->assessmentBatch->remarks)
                    <div class="alert alert-warning">
                        <strong><i class="bi bi-info-circle"></i> Important Notes:</strong><br>
                        {{ $app->assessmentBatch->remarks }}
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
