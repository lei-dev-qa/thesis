@extends('layouts.admin')

@section('title', 'Reassessment Payments - SHC-TVET')
@section('page-title', 'Reassessment Payments')

@section('content')
    <div class="container-fluid py-4">
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#pending">
                    Pending Verification ({{ $pendingPayments->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#verified">
                    Verified ({{ $verifiedPayments->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#rejected">
                    Rejected ({{ $rejectedPayments->count() }})
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Pending Tab -->
            <div class="tab-pane fade show active" id="pending">
                <div class="card">
                    <div class="card-body">
                        @if ($pendingPayments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Applicant</th>
                                            <th>Program</th>
                                            <th>Attempt</th>
                                            <th>NYC COCs</th>
                                            <th>Payment Proof</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendingPayments as $index => $app)
                                            @php
                                                $isSecondReassessment =
                                                    $app->second_reassessment_payment_proof !== null;
                                                $paymentProof = $isSecondReassessment
                                                    ? $app->second_reassessment_payment_proof
                                                    : $app->reassessment_payment_proof;
                                                $paymentDate = $isSecondReassessment
                                                    ? $app->second_reassessment_payment_date
                                                    : $app->reassessment_payment_date;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $app->firstname }} {{ $app->surname }}</strong><br>
                                                    <small class="text-muted">{{ $app->user->email }}</small>
                                                </td>
                                                <td>{{ $app->title_of_assessment_applied_for }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $isSecondReassessment ? 'bg-danger' : 'bg-warning' }} text-dark">
                                                        {{ $isSecondReassessment ? '2nd Reassessment' : 'Reassessment' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cocModal{{ $app->id }}">
                                                        {{ $app->getNycCocs()->count() }} NYC
                                                    </button>
                                                </td>
                                                <td>
                                                    <a href="{{ Storage::url($paymentProof) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark-image"></i> View
                                                    </a>
                                                </td>
                                                <td>
                                                    <form method="POST"
                                                        action="{{ route('admin.reassessment.verify-payment', $app->id) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" name="action" value="verify"
                                                            class="btn btn-sm btn-success"
                                                            onclick="return confirm('Verify this payment?')">
                                                            Verify
                                                        </button>
                                                        <button type="submit" name="action" value="reject"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Reject this payment?')">
                                                            Reject
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <!-- COC Details Modal -->
                                            @include('admin.reassessment.component.coc-details', [
                                                'app' => $app,
                                            ])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No pending payment verifications.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Verified Tab -->
            <div class="tab-pane fade" id="verified">
                <div class="card">
                    <div class="card-body">
                        @if ($verifiedPayments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Program</th>
                                            <th>Attempt</th>
                                            <th>Verified On</th>
                                            <th>Official Receipt</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($verifiedPayments as $app)
                                            @php
                                                $isSecondReassessment =
                                                    $app->second_reassessment_payment_status === 'verified';
                                                $officialReceipt = $isSecondReassessment
                                                    ? $app->second_reassessment_official_receipt_photo
                                                    : $app->reassessment_official_receipt_photo;
                                            @endphp
                                            <tr>
                                                <td>{{ $app->firstname }} {{ $app->surname }}</td>
                                                <td>{{ $app->title_of_assessment_applied_for }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $isSecondReassessment ? 'bg-danger' : 'bg-warning' }} text-dark">
                                                        {{ $isSecondReassessment ? '2nd Reassessment' : 'Reassessment' }}
                                                    </span>
                                                </td>
                                                <td>{{ $app->updated_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if ($officialReceipt)
                                                        <a href="{{ Storage::url($officialReceipt) }}" target="_blank"
                                                            class="btn btn-sm btn-info">
                                                            <i class="bi bi-file-earmark-text"></i> View
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Not uploaded</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#uploadReceiptModal{{ $app->id }}">
                                                        <i class="fas fa-upload"></i>
                                                        {{ $officialReceipt ? 'Replace' : 'Upload' }} Receipt
                                                    </button>
                                                </td>
                                            </tr>
                                            {{-- Upload Official Receipt Modal --}}
                                            @include(
                                                'admin.reassessment.component.upload-official-receipt',
                                                [
                                                    'app' => $app,
                                                    'isSecondReassessment' => $isSecondReassessment,
                                                ]
                                            )
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No verified payments awaiting batch assignment.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Rejected Tab -->
            <div class="tab-pane fade" id="rejected">
                <div class="card">
                    <div class="card-body">
                        @if ($rejectedPayments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Program</th>
                                            <th>Rejected On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rejectedPayments as $app)
                                            <tr>
                                                <td>{{ $app->firstname }} {{ $app->surname }}</td>
                                                <td>{{ $app->title_of_assessment_applied_for }}</td>
                                                <td>{{ $app->updated_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ Storage::url($app->reassessment_payment_proof) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        View Proof
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No rejected payments.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
