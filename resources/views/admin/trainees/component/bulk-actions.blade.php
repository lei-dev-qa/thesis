<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkActionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="bulkActionContent"></div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="training_remarks" class="form-control" rows="3"
                            placeholder="Enter remarks for all selected trainees" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="bulkActionBtn"></button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript for Bulk Actions --}}
<script>
    // Bulk selection functions
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.trainee-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        toggleBulkActions();
    }

    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.trainee-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');

        if (checkedBoxes.length > 0) {
            bulkActions.style.display = 'inline-block';
        } else {
            bulkActions.style.display = 'none';
        }
    }

    function bulkComplete() {
        const checkedBoxes = document.querySelectorAll('.trainee-checkbox:checked');
        const applicationIds = Array.from(checkedBoxes).map(cb => cb.value);

        if (applicationIds.length === 0) {
            alert('Please select trainees to complete.');
            return;
        }

        document.getElementById('bulkActionTitle').textContent = 'Bulk Complete Training';
        document.getElementById('bulkActionContent').innerHTML =
            `<p>Mark <strong>${applicationIds.length}</strong> selected trainee(s) as completed?</p>`;
        document.getElementById('bulkActionBtn').textContent = 'Complete All';
        document.getElementById('bulkActionBtn').className = 'btn btn-success';

        // Set form action and hidden inputs
        document.getElementById('bulkActionForm').action = '{{ route('admin.training-batches.bulk-complete') }}';

        // Add hidden inputs for application IDs
        const form = document.getElementById('bulkActionForm');
        // Remove existing hidden inputs
        form.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());

        applicationIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'application_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
    }

    function bulkFail() {
        const checkedBoxes = document.querySelectorAll('.trainee-checkbox:checked');
        const applicationIds = Array.from(checkedBoxes).map(cb => cb.value);

        if (applicationIds.length === 0) {
            alert('Please select trainees to fail.');
            return;
        }

        document.getElementById('bulkActionTitle').textContent = 'Bulk Fail Training';
        document.getElementById('bulkActionContent').innerHTML =
            `<p>Mark <strong>${applicationIds.length}</strong> selected trainee(s) as failed?</p>`;
        document.getElementById('bulkActionBtn').textContent = 'Fail All';
        document.getElementById('bulkActionBtn').className = 'btn btn-danger';

        // Set form action and hidden inputs
        document.getElementById('bulkActionForm').action = '{{ route('admin.training-batches.bulk-fail') }}';

        // Add hidden inputs for application IDs
        const form = document.getElementById('bulkActionForm');
        // Remove existing hidden inputs
        form.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());

        applicationIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'application_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
    }
</script>
