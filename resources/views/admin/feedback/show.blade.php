@extends('layouts.admin')

@section('title', 'Employment Feedback - SHC-TVET')
@section('page-title', 'Employment Feedback - ' . $batch->nc_program . ' Batch ' . $batch->batch_number)

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5>{{ $batch->nc_program }} - Batch {{ $batch->batch_number }}</h5>
                <small class="text-muted">Training Applicants</small>
            </div>
            <a href="{{ route('admin.employment-feedback.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Batches
            </a>
        </div>

        <!-- Search Bar -->
        <div class="card-header bg-light ">
            <input type="text" id="searchInput" value="{{ request('q') }}" class="form-control"
                placeholder="Search by applicant name...">
        </div>

        <!-- Applicants Block (for AJAX replacement) -->
        <div id="applicantsBlock">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Training Result</th>
                                <th>Assessment Result</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($applications as $application)
                                @php
                                    $fullName = trim(
                                        $application->firstname .
                                            ' ' .
                                            ($application->middlename ? $application->middlename . ' ' : '') .
                                            $application->surname .
                                            ' ' .
                                            ($application->name_extension ?? ''),
                                    );

                                    // Get assessment result and map to Competent/Not Yet Competent
                                    $assessmentResult = $application->assessmentResult;
                                    if ($assessmentResult) {
                                        if ($assessmentResult->result === 'Competent') {
                                            $assessmentStatus = 'Competent';
                                            $assessmentBadgeClass = 'success';
                                        } elseif ($assessmentResult->result === 'Not Yet Competent') {
                                            $assessmentStatus = 'Not Yet Competent';
                                            $assessmentBadgeClass = 'danger';
                                        } else {
                                            $assessmentStatus = $assessmentResult->result;
                                            $assessmentBadgeClass = 'secondary';
                                        }
                                    } else {
                                        $assessmentStatus = 'N/A';
                                        $assessmentBadgeClass = 'secondary';
                                    }

                                    // Get training result
                                    $trainingResult = $application->trainingResult;
                                    $trainingStatus = $application->training_status
                                        ? ucfirst($application->training_status)
                                        : 'N/A';

                                    // Check if employment record exists
                                    $hasEmployment = $application->employmentRecord !== null;
                                @endphp
                                <tr>
                                    <td>
                                        {{ ($applications->currentPage() - 1) * $applications->perPage() + $loop->iteration }}
                                        @if ($hasEmployment && $application->employmentRecord->isNew())
                                            <span class="badge bg-danger ms-2">NEW</span>
                                        @endif
                                    </td>

                                    <td>{{ $fullName }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $application->training_status === 'completed' ? 'success' : 'danger' }}">
                                            {{ $trainingStatus }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assessmentBadgeClass }}">
                                            {{ $assessmentStatus }}
                                        </span>

                                        @if ($hasEmployment)
                                            <span class="badge bg-primary ms-1">Employed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($hasEmployment)
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewEmploymentModal{{ $application->id }}"
                                                data-employment-id="{{ $application->employmentRecord->id }}">
                                                <i class="bi bi-eye"></i> View/Edit
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addEmploymentModal{{ $application->id }}">
                                                <i class="bi bi-briefcase"></i> Employment
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Add Employment Modal -->
                                @if (!$hasEmployment)
                                    @include('admin.feedback.component.add-employment', [
                                        'application' => $application,
                                        'fullName' => $fullName,
                                    ])
                                @endif

                                <!-- View/Edit Employment Modal -->
                                @if ($hasEmployment)
                                    @include('admin.feedback.component.view-edit-employment', [
                                        'application' => $application,
                                        'fullName' => $fullName,
                                    ])
                                @endif

                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No completed applicants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3 mb-3" id="applicantsPagination">
                {{ $applications->onEachSide(1)->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let timer;

        // Debounced live search
        document.getElementById('searchInput').addEventListener('keyup', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                let query = this.value;
                let url =
                    `{{ route('admin.employment-feedback.show', $batch->id) }}?q=${encodeURIComponent(query)}`;

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        document.getElementById('applicantsBlock').innerHTML =
                            doc.querySelector('#applicantsBlock').innerHTML;

                        // Re-attach event listeners after AJAX reload
                        attachModalListeners();
                    })
                    .catch(error => console.error('Error fetching search results:', error));
            }, 400);
        });

        // Function to attach modal listeners
        function attachModalListeners() {
            document.querySelectorAll('[data-employment-id]').forEach(button => {
                button.addEventListener('click', function() {
                    const employmentId = this.getAttribute('data-employment-id');

                    // Mark as viewed when modal is opened
                    fetch(`{{ url('/admin/employment-feedback') }}/${employmentId}/mark-viewed`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove NEW badge from this row
                                const row = this.closest('tr');
                                const newBadge = row.querySelector('.badge.bg-danger');
                                if (newBadge && newBadge.textContent === 'NEW') {
                                    newBadge.remove();
                                }

                                // Update sidebar count (optional - will update on page refresh)
                                updateSidebarCount();
                            }
                        })
                        .catch(error => console.error('Error marking as viewed:', error));
                });
            });
        }

        // Function to update sidebar count
        function updateSidebarCount() {
            fetch('{{ route('admin.employment-feedback.index') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newCount = doc.querySelector('.sidebar .badge');
                    const currentBadge = document.querySelector('.sidebar .badge');

                    if (newCount && currentBadge) {
                        currentBadge.textContent = newCount.textContent;
                        if (newCount.textContent === '0') {
                            currentBadge.style.display = 'none';
                        }
                    }
                });
        }

        // Initial attachment of listeners
        document.addEventListener('DOMContentLoaded', function() {
            attachModalListeners();
        });
    </script>
@endsection
