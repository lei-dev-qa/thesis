@props(['app'])

<div class="modal fade" id="trainingScheduleModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-check"></i> Training Schedule
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
                    <h6 class="mb-0">{{ $app->trainingBatch->batch_name }}</h6>
                </div>

                @if ($app->trainingBatch->trainingSchedule)
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-info text-white">
                            <strong><i class="bi bi-book"></i> Training Schedule</strong>
                        </div>
                        <div class="card-body">
                            {{-- Date Row --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Start Date</label>
                                    <h6 class="mb-0">
                                        <i class="bi bi-calendar3"></i>
                                        {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->start_date)->format('F d, Y') }}
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">End Date</label>
                                    <h6 class="mb-0">
                                        <i class="bi bi-calendar3"></i>
                                        {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->end_date)->format('F d, Y') }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->end_date)->format('l') }}
                                    </small>
                                </div>
                            </div>

                            {{-- Time Row --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Venue</label>
                                    @if ($app->trainingBatch->trainingSchedule->venue)
                                        <h6 class="mb-0">
                                            <i class="bi bi-geo-alt"></i>
                                            {{ $app->trainingBatch->trainingSchedule->venue }}
                                        </h6>
                                    @else
                                        <h6 class="mb-0 text-muted">-</h6>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Time</label>
                                    @if ($app->trainingBatch->trainingSchedule->end_time)
                                        <h6 class="mb-0">
                                            <i class="bi bi-clock"></i>
                                            {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->start_time)->format('h:i A') }}
                                            -
                                            {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->end_time)->format('h:i A') }}
                                        </h6>
                                    @else
                                        <h6 class="mb-0 text-muted">-</h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
