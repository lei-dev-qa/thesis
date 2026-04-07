@props(['program', 'loopIndex'])

<div class="modal fade" id="cocModal{{ $loopIndex }}" tabindex="-1"
    aria-labelledby="cocModalLabel{{ $loopIndex }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="cocModalLabel{{ $loopIndex }}">
                    <i class="bi bi-clipboard-data me-2"></i>{{ $program['name'] }} - COC Performance Analysis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Program Summary --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-primary">
                                <i class="bi bi-people fs-2"></i>
                                <div class="fw-bold fs-4">{{ $program['total_assessments'] }}</div>
                                <div class="small text-muted">Total Assessed</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-success">
                                <i class="bi bi-trophy fs-2"></i>
                                <div class="fw-bold fs-4">{{ $program['overall_competent_rate'] }}%</div>
                                <div class="small text-muted">Overall Competent</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-danger">
                                <i class="bi bi-x-circle fs-2"></i>
                                <div class="fw-bold fs-4">{{ $program['overall_nyc_rate'] }}%</div>
                                <div class="small text-muted">Overall NYC</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-info">
                                <i class="bi bi-list-check fs-2"></i>
                                <div class="fw-bold fs-4">{{ count($program['coc_breakdown']) }}</div>
                                <div class="small text-muted">Total COCs</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Charts Section --}}
                <div class="row mb-4">
                    {{-- LEFT: Performance Insights --}}
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Performance Insights</h6>

                        @php
                            $bestCoc = collect($program['coc_breakdown'])
                                ->sortByDesc('competent_rate')
                                ->first();
                            $worstCoc = collect($program['coc_breakdown'])->sortBy('competent_rate')->first();
                        @endphp

                        {{-- Best Performing --}}
                        <div class="card border-success mb-3">
                            <div class="card-body text-center">
                                <i class="bi bi-trophy text-success fs-3"></i>
                                <h6 class="text-success mt-2">Best Performing COC</h6>
                                <div class="fw-bold">{{ $bestCoc['coc_code'] ?? 'N/A' }}</div>
                                <div class="small text-muted">
                                    {{ $bestCoc['competent_rate'] ?? 0 }}% Success Rate
                                </div>
                            </div>
                        </div>

                        {{-- Needs Improvement --}}
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="bi bi-exclamation-triangle text-warning fs-3"></i>
                                <h6 class="text-warning mt-2">Needs Improvement</h6>
                                <div class="fw-bold">{{ $worstCoc['coc_code'] ?? 'N/A' }}</div>
                                <div class="small text-muted">
                                    {{ $worstCoc['competent_rate'] ?? 0 }}% Success Rate
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Donut Chart --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-pie-chart me-2"></i>Overall Success Rate
                                </h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <canvas id="donutChart{{ $loopIndex }}" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bar Chart - Competent vs NYC per COC --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Competent vs NYC Comparison</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="barChart{{ $loopIndex }}" style="max-height: 250px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js Scripts --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prepare data for {{ $program['name'] }}
            const cocLabels{{ $loopIndex }} = {!! json_encode(array_column($program['coc_breakdown']->toArray(), 'coc_code')) !!};
            const competentData{{ $loopIndex }} = {!! json_encode(array_column($program['coc_breakdown']->toArray(), 'competent_count')) !!};
            const nycData{{ $loopIndex }} = {!! json_encode(array_column($program['coc_breakdown']->toArray(), 'nyc_count')) !!};

            // 1. Donut Chart - Overall Success Rate
            const donutCtx{{ $loopIndex }} = document.getElementById('donutChart{{ $loopIndex }}');
            if (donutCtx{{ $loopIndex }}) {
                new Chart(donutCtx{{ $loopIndex }}, {
                    type: 'doughnut',
                    data: {
                        labels: ['Competent', 'Not Yet Competent'],
                        datasets: [{
                            data: [{{ $program['overall_competent_rate'] }}, {{ $program['overall_nyc_rate'] }}],
                            backgroundColor: ['rgba(25, 135, 84, 0.8)', 'rgba(220, 53, 69, 0.8)'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            title: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Bar Chart - Competent vs NYC Comparison
            const barCtx{{ $loopIndex }} = document.getElementById('barChart{{ $loopIndex }}');
            if (barCtx{{ $loopIndex }}) {
                new Chart(barCtx{{ $loopIndex }}, {
                    type: 'bar',
                    data: {
                        labels: cocLabels{{ $loopIndex }},
                        datasets: [{
                                label: 'Competent',
                                data: competentData{{ $loopIndex }},
                                backgroundColor: 'rgba(25, 135, 84, 0.8)',
                                borderColor: 'rgba(25, 135, 84, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Not Yet Competent',
                                data: nycData{{ $loopIndex }},
                                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: false }
                        }
                    }
                });
            }
        });
    </script>
@endpush
