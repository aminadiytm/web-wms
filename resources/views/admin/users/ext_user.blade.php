@push('scripts')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.user-role-select').select2({
            width: '100%',
            placeholder: 'Select role'
        });

        // 1) Render List Data
        const table = $('#user_tbl').DataTable({
            ajax: '{{ route('admin.users.usrList') }}',
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center align-middle",
                    render: function (data, type, row) {
                        return `
                            <div class="action-group d-flex justify-content-center align-items-center gap-2">
                                <button
                                    type="button"
                                    class="action-icon action-icon-edit btn-edit"
                                    data-id="${row.id}"
                                    title="Edit"
                                >
                                    <i class="fas fa-pen"></i>
                                </button>

                                <button
                                    type="button"
                                    class="action-icon action-icon-warning btn-reset"
                                    data-id="${row.id}"
                                    title="Reset Password"
                                >
                                    <i class="fas fa-key"></i>
                                </button>

                                <button
                                    type="button"
                                    class="action-icon action-icon-delete btn-delete"
                                    data-id="${row.id}"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role_name', name: 'role_name', orderable: false, searchable: false },
                { data: 'crea', name: 'crea' },
                { data: 'up', name: 'up' }
            ]
        });

        // 2) Clear Add Modal
        $('#userModal').on('shown.bs.modal', function () {
            $('#userForm')[0].reset();
            $('#user_roles').val(null).trigger('change');
            $('#user_name').focus();
        });

        // 3) Save Add / Edit User
        $('#userForm, #e_userForm').on('submit', function (e) {
            e.preventDefault();

            let data = $(this).serialize();

            $.ajax({
                url: "{{ route('admin.users.usrStore') }}",
                method: "POST",
                data: data,
                success: function (res) {
                    $('#userModal, #e_userModal').modal('hide');

                    Swal.fire({
                        title: "Success!",
                        text: res.message,
                        icon: res.status
                    });

                    table.ajax.reload();
                },
                error: function (xhr) {
                    const err = xhr.responseJSON?.errors ?? {};
                    let firstErr =
                        err.name?.[0] ||
                        err.email?.[0] ||
                        err.password?.[0] ||
                        xhr.responseJSON?.message ||
                        'Terjadi kesalahan.';

                    Swal.fire("Error!", firstErr, "error");
                }
            });
        });

        // 4) Edit Show Modal
        $('#user_tbl').on('click', '.btn-edit', function () {
            let userId = $(this).data('id');

            $('#e_userModal').modal('show');

            let link = "{{ route('admin.users.usrEdit', ':id') }}";
            link = link.replace(':id', userId);

            $.ajax({
                url: link,
                method: "GET",
                success: function (res) {
                    $('#e_user_id').val(res.user_id);
                    $('#e_user_name').val(res.name);
                    $('#e_user_email').val(res.email);
                    $('#e_user_roles').val(res.roles).trigger('change');
                },
                error: function () {
                    $('#e_userModal').modal('hide');

                    Swal.fire(
                        "Error!",
                        "An Error Occurred While Retrieving Edit Data",
                        "error"
                    );
                }
            });
        });

        // 5) Reset Password Modal
        $('#user_tbl').on('click', '.btn-reset', function () {
            let userId = $(this).data('id');

            $('#resetUserForm')[0].reset();
            $('#reset_user_id').val(userId);
            $('#resetUserModal').modal('show');
        });

        // 6) Submit Reset Password
        $('#resetUserForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.users.usrUpdate') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function (res) {
                    $('#resetUserModal').modal('hide');

                    Swal.fire({
                        title: "Success!",
                        text: res.message,
                        icon: res.status
                    });
                },
                error: function (xhr) {
                    const err = xhr.responseJSON?.errors ?? {};
                    let firstErr =
                        err.password?.[0] ||
                        err.user_id?.[0] ||
                        xhr.responseJSON?.message ||
                        'Terjadi kesalahan.';

                    Swal.fire("Error!", firstErr, "error");
                }
            });
        });

        // 7) Delete Data
        $('#user_tbl').on('click', '.btn-delete', function () {
            let id = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "User yang dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {
                    let link = "{{ route('admin.users.usrDelete', ':id') }}";
                    link = link.replace(':id', id);

                    $.ajax({
                        url: link,
                        method: "DELETE",
                        success: function (res) {
                            Swal.fire(res.title, res.message, res.status);
                            table.ajax.reload();
                        },
                        error: function (xhr) {
                            Swal.fire(
                                "Error!",
                                xhr.responseJSON?.message || "User gagal dihapus.",
                                "error"
                            );
                        }
                    });
                }
            });
        });
    });
</script>
@endpush