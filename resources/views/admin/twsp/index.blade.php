@extends('layouts.admin')
@section('title', 'TWSP Announcements - SHC-TVET')
@section('page-title', 'TWSP Announcements Management')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <!-- Current Active Announcement -->
                <div class="card mb-4 border border-primary">
                    <div
                        class="card-header bg-primary text-light d-flex justify-content-center align-items-center ">
                        <h5 class="mb-0 font-weight-bold">Current Announcement</h5>
                    </div>
                    <div class="card-body ">
                        @if ($announcement)
                            <div class="border border-1 border-primary p-3">
                                <h5>{{ $announcement->program_name }}</h5>
                                <p><strong>Total Slots:</strong> {{ $announcement->total_slots }}</p>
                                <p><strong>Filled Slots:</strong> {{ $announcement->filled_slots }}</p>
                                <p><strong>Remaining Slots:</strong> {{ $announcement->getRemainingSlots() }}</p>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-success">Active</span>
                                </p>
                            </div>

                            <form action="{{ route('admin.twsp.close', $announcement->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger mt-3"
                                    onclick="return confirm('Are you sure you want to close this announcement?')">
                                    Close Announcement
                                </button>
                            </form>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-3">No active announcement</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#createAnnouncementModal">
                                    <i class="fas fa-plus-circle"></i> Create New Announcement
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Create Announcement Modal  -->
                @include('admin.twsp.component.create-announcement')

                <div class="card">
                    <div class="card-header">
                        <h4>Announcement History</h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Program</th>
                                    <th>Total Slots</th>
                                    <th>Filled</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                    <tr>
                                        <td>{{ $item->program_name }}</td>
                                        <td>{{ $item->total_slots }}</td>
                                        <td>{{ $item->filled_slots }}</td>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td><span class="badge bg-secondary">Closed</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No history</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
