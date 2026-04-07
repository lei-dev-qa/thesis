@props(['batch'])

@if ($batch->is_full && !$batch->hasSchedule())
    <div class="modal fade" id="createScheduleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.training-schedules.store') }}">
                    @csrf
                    <input type="hidden" name="training_batch_id" value="{{ $batch->id }}">
                    <input type="hidden" name="nc_program" value="{{ $batch->nc_program }}">
                    <input type="hidden" name="max_students" value="{{ $batch->max_students }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Create Schedule for Batch {{ $batch->batch_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Schedule Name</label>
                                <input type="text" name="schedule_name" class="form-control"
                                    value="Batch {{ $batch->batch_number }} Schedule" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="08:00"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">End Time</label>
                                <input type="time" name="end_time" class="form-control" value="17:00"
                                    required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Days</label>
                                <input type="text" name="days" class="form-control"
                                    placeholder="e.g., Monday-Friday" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Venue</label>
                                <input type="text" name="venue" class="form-control" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Instructor</label>
                                <input type="text" name="instructor" class="form-control" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Description (Optional)</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Create Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
