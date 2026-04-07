<div class="modal fade" id="createAnnouncementModal" tabindex="-1"
    aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-primary">
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title" id="createAnnouncementModalLabel">
                    <i class="fas fa-bullhorn"></i> Create New Announcement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.twsp.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label><i class="fas fa-book"></i> Program Name</label>
                                <input type="text" class="form-control" value="Bookkeeping NC III"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label><i class="fas fa-users"></i> Total Slots</label>
                                <input type="number" name="total_slots" class="form-control" min="1"
                                    max="100" value="25" required>
                                @error('total_slots')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Create Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
