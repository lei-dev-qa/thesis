{{-- Assessment Volume Analytics --}}
<div class="card analytics-card">
    <div class="card-header bg-secondary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-graph-up me-2"></i>Assessment Volume Analytics
            </h5>
            <div>
                <select class="form-select form-select-sm" id="yearSelector" style="width: auto;">
                    @foreach ($volume['available_years'] as $year)
                        <option value="{{ $year }}" {{ $year == $volume['selected_year'] ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{-- Key Metrics Row --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-clipboard-data text-primary fs-2"></i>
                        <div class="fw-bold fs-3 mt-2" id="totalAssessments">{{ $volume['total_this_year'] }}</div>
                        <div class="small text-muted">Total Assessments <span
                                id="totalAssessmentsYear">{{ $volume['selected_year'] }}</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-{{ $volume['year_over_year_growth'] >= 0 ? 'success' : 'danger' }}"
                    id="yoyCard">
                    <div class="card-body text-center">
                        <i class="bi bi-{{ $volume['year_over_year_growth'] >= 0 ? 'arrow-up' : 'arrow-down' }}-circle text-{{ $volume['year_over_year_growth'] >= 0 ? 'success' : 'danger' }} fs-2"
                            id="yoyIcon"></i>
                        <div class="fw-bold fs-3 mt-2" id="yoyGrowth">{{ abs($volume['year_over_year_growth']) }}%</div>
                        <div class="small text-muted">YoY Growth</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-star text-warning fs-2"></i>
                        <div class="fw-bold fs-3 mt-2" id="peakMonth">
                            {{ $volume['peak_month']['month_short'] ?? 'N/A' }}</div>
                        <div class="small text-muted">Peak Month (<span
                                id="peakMonthCount">{{ $volume['peak_month']['count'] ?? 0 }}</span> assessed)</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="bi bi-trophy text-info fs-2"></i>
                        <div class="fw-bold fs-5 mt-2" id="mostActiveProgram">
                            {{ $volume['most_active_program']['name'] ?? 'N/A' }}</div>
                        <div class="small text-muted">Most Active Program</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 1: Line Chart + Donut Chart --}}
        <div class="row mb-4">
            {{-- Monthly Trend Line Chart --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>Monthly Assessment Trend
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyTrendChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- Program Distribution Donut --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-pie-chart me-2"></i>Program Distribution
                        </h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="programDistributionChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 2: Stacked Bar Chart --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-bar-chart-steps me-2"></i>Program Assessment Volume by Month
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="stackedBarChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 3: Horizontal Bar Chart --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-bar-chart me-2"></i>Yearly Program Totals
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="yearlyProgramChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store chart instances globally so we can update them
            let monthlyTrendChart = null;
            let programDistributionChart = null;
            let stackedBarChart = null;
            let yearlyProgramChart = null;

            // Color palette
            const colors = [
                'rgba(13, 110, 253, 0.8)', // Blue
                'rgba(25, 135, 84, 0.8)', // Green
                'rgba(255, 193, 7, 0.8)', // Yellow
                'rgba(220, 53, 69, 0.8)', // Red
                'rgba(111, 66, 193, 0.8)' // Purple
            ];

            const borderColors = [
                'rgba(13, 110, 253, 1)',
                'rgba(25, 135, 84, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(220, 53, 69, 1)',
                'rgba(111, 66, 193, 1)'
            ];

            // Function to initialize all charts
            function initializeCharts(data) {
                const monthLabels = data.monthly_totals.map(m => m.month_short);
                const monthlyCounts = data.monthly_totals.map(m => m.count);

                const programNames = data.program_monthly_data.map(p => p.name);
                const programMonthlyData = data.program_monthly_data.map(p => p.data);
                const programYearlyTotals = data.yearly_program_totals.map(p => p.count);
                const programPercentages = data.yearly_program_totals.map(p => p.percentage);

                // 1. Monthly Trend Line Chart
                const monthlyTrendCtx = document.getElementById('monthlyTrendChart');
                if (monthlyTrendCtx) {
                    if (monthlyTrendChart) {
                        monthlyTrendChart.destroy(); // Destroy existing chart
                    }
                    monthlyTrendChart = new Chart(monthlyTrendCtx, {
                        type: 'line',
                        data: {
                            labels: monthLabels,
                            datasets: [{
                                label: 'Total Assessments',
                                data: monthlyCounts,
                                borderColor: 'rgba(13, 110, 253, 1)',
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Assessments: ' + context.parsed.y;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }

                // 2. Program Distribution Donut Chart
                const programDistCtx = document.getElementById('programDistributionChart');
                if (programDistCtx) {
                    if (programDistributionChart) {
                        programDistributionChart.destroy();
                    }
                    programDistributionChart = new Chart(programDistCtx, {
                        type: 'doughnut',
                        data: {
                            labels: programNames,
                            datasets: [{
                                data: programPercentages,
                                backgroundColor: colors,
                                borderColor: '#ffffff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
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

                // 3. Stacked Bar Chart
                const stackedBarCtx = document.getElementById('stackedBarChart');
                if (stackedBarCtx) {
                    if (stackedBarChart) {
                        stackedBarChart.destroy();
                    }
                    const datasets = programNames.map((name, index) => ({
                        label: name,
                        data: programMonthlyData[index],
                        backgroundColor: colors[index],
                        borderColor: borderColors[index],
                        borderWidth: 1
                    }));

                    stackedBarChart = new Chart(stackedBarCtx, {
                        type: 'bar',
                        data: {
                            labels: monthLabels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }

                // 4. Horizontal Bar Chart
                const yearlyProgramCtx = document.getElementById('yearlyProgramChart');
                if (yearlyProgramCtx) {
                    if (yearlyProgramChart) {
                        yearlyProgramChart.destroy();
                    }
                    yearlyProgramChart = new Chart(yearlyProgramCtx, {
                        type: 'bar',
                        data: {
                            labels: programNames,
                            datasets: [{
                                label: 'Total Assessments',
                                data: programYearlyTotals,
                                backgroundColor: colors,
                                borderColor: borderColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Assessments: ' + context.parsed.x;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Function to update metric cards
            function updateMetricCards(data) {
                // Total assessments
                document.querySelector('#totalAssessments').textContent = data.total_this_year;
                document.querySelector('#totalAssessmentsYear').textContent = data.selected_year;

                // YoY Growth
                const yoyGrowth = data.year_over_year_growth;
                const yoyElement = document.querySelector('#yoyGrowth');
                const yoyIcon = document.querySelector('#yoyIcon');
                const yoyCard = document.querySelector('#yoyCard');

                yoyElement.textContent = Math.abs(yoyGrowth) + '%';

                if (yoyGrowth >= 0) {
                    yoyIcon.className = 'bi bi-arrow-up-circle text-success fs-2';
                    yoyCard.className = 'card border-success';
                } else {
                    yoyIcon.className = 'bi bi-arrow-down-circle text-danger fs-2';
                    yoyCard.className = 'card border-danger';
                }

                // Peak month
                document.querySelector('#peakMonth').textContent = data.peak_month?.month_short || 'N/A';
                document.querySelector('#peakMonthCount').textContent = data.peak_month?.count || 0;

                // Most active program
                document.querySelector('#mostActiveProgram').textContent = data.most_active_program?.name || 'N/A';
            }

            // Function to load data via AJAX
            function loadVolumeData(year) {
                const yearSelector = document.getElementById('yearSelector');
                const analyticsCard = document.querySelector('#volume-analytics-section .card-body');

                // Show loading state
                yearSelector.disabled = true;

                // Add loading overlay (optional)
                if (analyticsCard) {
                    analyticsCard.style.opacity = '0.5';
                    analyticsCard.style.pointerEvents = 'none';
                }

                // Fetch data
                fetch(`{{ route('admin.analytics.volume-data') }}?year=${year}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data loaded successfully:', data);

                        // Update metric cards
                        updateMetricCards(data);

                        // Reinitialize charts with new data
                        initializeCharts(data);
                    })
                    .catch(error => {
                        console.error('Error loading volume data:', error);
                        alert('Failed to load data. Please try again.');
                    })
                    .finally(() => {
                        // ✅ ALWAYS re-enable dropdown and remove loading state
                        // This runs whether success or error
                        yearSelector.disabled = false;

                        if (analyticsCard) {
                            analyticsCard.style.opacity = '1';
                            analyticsCard.style.pointerEvents = 'auto';
                        }

                        console.log('Dropdown re-enabled');
                    });
            }

            // Year selector change handler
            const yearSelector = document.getElementById('yearSelector');
            if (yearSelector) {
                yearSelector.addEventListener('change', function() {
                    const selectedYear = this.value;
                    loadVolumeData(selectedYear);
                });
            }

            // Initialize charts with initial data
            const initialData = {
                monthly_totals: {!! json_encode($volume['monthly_totals']) !!},
                program_monthly_data: {!! json_encode($volume['program_monthly_data']) !!},
                yearly_program_totals: {!! json_encode($volume['yearly_program_totals']) !!},
                total_this_year: {{ $volume['total_this_year'] }},
                selected_year: {{ $volume['selected_year'] }},
                year_over_year_growth: {{ $volume['year_over_year_growth'] }},
                peak_month: {!! json_encode($volume['peak_month']) !!},
                most_active_program: {!! json_encode($volume['most_active_program']) !!}
            };

            initializeCharts(initialData);
        });
    </script>
@endpush
