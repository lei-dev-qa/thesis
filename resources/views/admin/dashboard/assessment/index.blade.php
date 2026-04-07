{{-- resources/views/admin/dashboard/assessment/index.blade.php --}}
<div class="card analytics-card">
    <div class="card-header bg-success text-light">
        <h5 class="mb-0">
            <i class="bi bi-clipboard-check me-2"></i>Assessment Analytics
        </h5>
    </div>
    <div class="card-body">
        {{-- Assessment Metrics --}}
        <div class="row text-center mb-4">
            <div class="col-6">
                <div class="text-success">
                    <i class="bi bi-trophy fs-4"></i>
                    <div class="small">Competent</div>
                    <div class="fw-bold">{{ $assessment['competent_count'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="text-danger">
                    <i class="bi bi-x-circle fs-4"></i>
                    <div class="small">Not Yet Competent</div>
                    <div class="fw-bold">{{ $assessment['nyc_count'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Program Overview (Clickable) --}}
        <div class="mb-4">
            <h6 class="text-muted mb-3">Program Performance Overview</h6>
            @if (isset($assessment['programs']) && count($assessment['programs']) > 0)
                @foreach ($assessment['programs'] as $program)
                    <div class="mb-3">
                        <div class="card border-0 bg-light program-card" style="cursor: pointer;" data-bs-toggle="modal"
                            data-bs-target="#cocModal{{ $loop->index }}">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-dark">{{ $program['name'] }}</strong>
                                        <small class="text-muted d-block">{{ count($program['coc_breakdown']) }}
                                            COCs</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">{{ $program['overall_competent_rate'] }}%
                                        </div>
                                        <small class="text-muted">{{ $program['total_assessments'] }} assessed</small>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $program['overall_competent_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted small">No assessment data available</p>
            @endif
        </div>

        {{-- Reassessment Analysis --}}
        <div>
            <h6 class="text-muted mb-3">Reassessment Analysis</h6>
            <div class="row text-center">
                <div class="col-4">
                    <div class="text-warning">
                        <div class="fw-bold">{{ $assessment['reassessment']['first'] ?? 0 }}</div>
                        <div class="small">1st Reassess</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-danger">
                        <div class="fw-bold">{{ $assessment['reassessment']['second'] ?? 0 }}</div>
                        <div class="small">2nd Reassess</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-success">
                        <div class="fw-bold">{{ $assessment['reassessment']['success_rate'] ?? 0 }}%</div>
                        <div class="small">Success Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Enhanced COC Detail Modals with Charts --}}
@if (isset($assessment['programs']) && count($assessment['programs']) > 0)
    @foreach ($assessment['programs'] as $program)
        @include('admin.dashboard.assessment.component.coc-performance-analysis', [
            'program' => $program,
            'loopIndex' => $loop->index,
        ])
    @endforeach
@endif
