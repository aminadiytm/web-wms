<div class="modal fade" id="approvalRouteModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="approval-route-form">
            @csrf
            <input type="hidden" id="approval_route_id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Routing Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Route Code</label>
                        <input type="text" id="route_code" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Route Name</label>
                        <input type="text" id="route_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Transaction Type</label>
                        <select id="transaction_type" class="form-select" required>
                            <option value="INBOUND">INBOUND</option>
                            <option value="OUTBOUND">OUTBOUND</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Approver</label>
                        <select id="approver_user_id" class="form-select" required>
                            <option value="">Select Approver</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Default Route</label>
                        <select id="is_default" class="form-select" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select id="is_active" class="form-select" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>