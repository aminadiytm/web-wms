@push('scripts')
<script>
$(function () {
    const table = $('#approval_route_tbl').DataTable({
        ajax: '{{ route('masterdata.approvalRouteList') }}',
        columns: [
            { data: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'route_code' },
            { data: 'route_name' },
            { data: 'transaction_type' },
            { data: 'approver_name' },
            { data: 'is_default', className: 'text-center' },
            { data: 'is_active', className: 'text-center' },
        ]
    });

    $('#btn-add').on('click', function () {
        resetForm();
        $('#approvalRouteModal').modal('show');
    });

    $('#approval-route-form').on('submit', function (e) {
        e.preventDefault();

        const id = $('#approval_route_id').val();

        let url = id
            ? `{{ url('MasterData/Approval-Route/Update') }}/${id}`
            : `{{ route('masterdata.approvalRouteStore') }}`;

        let data = {
            _token: '{{ csrf_token() }}',
            route_code: $('#route_code').val(),
            route_name: $('#route_name').val(),
            transaction_type: $('#transaction_type').val(),
            approver_user_id: $('#approver_user_id').val(),
            is_default: $('#is_default').val(),
            is_active: $('#is_active').val(),
        };

        if (id) {
            data._method = 'PUT';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function (res) {
                $('#approvalRouteModal').modal('hide');
                table.ajax.reload();

                Swal.fire('Success', res.message, 'success');
            },
            error: function (xhr) {
                let message = 'Gagal menyimpan data';

                if (xhr.status === 422) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire('Error', message, 'error');
            }
        });
    });

    $('#approval_route_tbl').on('click', '.btn-edit', function () {
        const id = $(this).data('id');

        $.ajax({
            url: `{{ url('MasterData/Approval-Route/Edit') }}/${id}`,
            type: 'GET',
            success: function (res) {
                $('#approval_route_id').val(res.approval_route_id);
                $('#route_code').val(res.route_code);
                $('#route_name').val(res.route_name);
                $('#transaction_type').val(res.transaction_type);
                $('#approver_user_id').val(res.approver_user_id);
                $('#is_default').val(res.is_default ? 1 : 0);
                $('#is_active').val(res.is_active ? 1 : 0);

                $('#approvalRouteModal').modal('show');
            }
        });
    });

    $('#approval_route_tbl').on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Delete routing approval?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
        }).then(result => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `{{ url('MasterData/Approval-Route/Delete') }}/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function (res) {
                    table.ajax.reload();
                    Swal.fire('Success', res.message, 'success');
                }
            });
        });
    });

    function resetForm()
    {
        $('#approval_route_id').val('');
        $('#route_code').val('');
        $('#route_name').val('');
        $('#transaction_type').val('INBOUND');
        $('#approver_user_id').val('');
        $('#is_default').val('0');
        $('#is_active').val('1');
    }
});
</script>
@endpush