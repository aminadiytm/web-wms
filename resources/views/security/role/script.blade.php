@push('scripts')
<script>
$(function () {
    const table = $('#role_tbl').DataTable({
        ajax: '{{ route('security.roleList') }}',
        columns: [
            { data: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'total_permission', className: 'text-center' },
        ]
    });

    $('#btn-add').on('click', function () {
        resetForm();
        $('#roleModal').modal('show');
    });

    $('#role-form').on('submit', function (e) {
        e.preventDefault();

        const id = $('#role_id').val();

        const permissions = $('.permission-checkbox:checked')
            .map(function () {
                return $(this).val();
            })
            .get();

        let url = id
            ? `{{ url('Security/Role-Permission/Update') }}/${id}`
            : `{{ route('security.roleStore') }}`;

        let data = {
            _token: '{{ csrf_token() }}',
            role_name: $('#role_name').val(),
            permissions: permissions
        };

        if (id) {
            data._method = 'PUT';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function (res) {
                $('#roleModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', res.message, 'success');
            },
            error: function (xhr) {
                let message = 'Gagal menyimpan role';

                if (xhr.status === 422) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire('Error', message, 'error');
            }
        });
    });

    $('#role_tbl').on('click', '.btn-edit', function () {
        const id = $(this).data('id');

        $.ajax({
            url: `{{ url('Security/Role-Permission/Edit') }}/${id}`,
            type: 'GET',
            success: function (res) {
                resetForm();

                $('#role_id').val(res.id);
                $('#role_name').val(res.name);

                res.permissions.forEach(permission => {
                    $(`.permission-checkbox[value="${permission}"]`).prop('checked', true);
                });

                $('#roleModal').modal('show');
            }
        });
    });

    $('#role_tbl').on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Delete role?',
            text: 'Role yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
        }).then(result => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `{{ url('Security/Role-Permission/Delete') }}/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE',
                },
                success: function (res) {
                    table.ajax.reload();
                    Swal.fire('Success', res.message, 'success');
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Gagal hapus role', 'error');
                }
            });
        });
    });

    function resetForm()
    {
        $('#role_id').val('');
        $('#role_name').val('');
        $('.permission-checkbox').prop('checked', false);
    }

// Select All per Column
$(document).on('change', '.select-column', function () {

    const action = $(this).data('action');
    const checked = $(this).is(':checked');

    $(`.permission-checkbox[data-action="${action}"]`)
        .prop('checked', checked);
});

$(document).on('change', '.permission-checkbox', function () {

    ['view', 'create', 'edit', 'delete'].forEach(action => {

        const total =
            $(`.permission-checkbox[data-action="${action}"]`).length;

        const checked =
            $(`.permission-checkbox[data-action="${action}"]:checked`).length;

        $(`.select-column[data-action="${action}"]`)
            .prop('checked', total === checked);
    });

});
});
</script>
@endpush