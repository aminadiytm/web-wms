@push('scripts')
<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 1) Render List Data
        const table = $('#wh_tbl').DataTable({
            ajax: '{{ route('masterdata.whList') }}',
            columns : [
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
                {data:'DT_RowIndex', name:'DT_RowIndex', orderable: false, searchable: false},
                {data:'code', name:'code'},
                {data:'name', name:'name'},
                {data:'addr', name:'addr'},
                {data:'desc', name:'desc'},
                {data:'add', name:'add'},
                {data:'upd', name:'upd'},
                {data:'crea', name:'crea'},
                {data:'up', name:'up'},
            ]
        })

        // 2) Clear add Modal
        $('#whModal').on('shown.bs.modal', function() {
            $('#whForm')[0].reset();
            $('#wh_code').focus();
        })

        // 3) Save Modal
        $('#whForm, #e_whForm').on('submit', function (e) {
            e.preventDefault();
            let data = $(this).serialize();

            $.ajax({
                url: "{{ route('masterdata.whStore') }}",
                type: "POST",
                data: data,
                success: function (res) {
                    $('#whModal, #e_whModal').modal('hide');

                    Swal.fire({
                      title: "Success!",
                      text: res.message,
                      icon: res.status
                    });

                    table.ajax.reload();
                },
                error: function(xhr) {
                    const err = xhr.responseJSON?.errors ?? {};
                    let firstErr = err.whcd?.[0] || err.whnm?.[0] || err.whaddr?.[0] || err.whdesc?.[0];
                    Swal.fire(
                        "Error!",
                        firstErr,
                        "error"
                    )
                }
            });
        })

        // 4) Edit Show Modal
        $('#wh_tbl').on('click', '.btn-edit', function() {
            let whid = $(this).data('id');

            $('#e_whModal').modal('show');
            let link = "{{ route('masterdata.whgetData', ':id') }}";
            link = link.replace(':id', whid);

            $.ajax({
                type: "GET",
                url: link,
                success: function (res) {
                    $('#ewh_id').val(res.wh_id);
                    $('#ewh_code').val(res.wh_code);
                    $('#ewh_name').val(res.wh_name);
                    $('#ewh_address').val(res.wh_addr);
                    $('#ewh_desc').val(res.wh_desc);
                },
                error: function(xhr) {
                    Swal.fire(
                        "Error!",
                        "Problem Get Data",
                        "error"
                    )
                }
            });
        })

        // 5) Delete Data
        $('#wh_tbl').on('click', '.btn-delete', function() {
            let id = $(this).data('id');

            Swal.fire({
              title: "Are you sure?",
              text: "Data yang dihapus tidak bisa dikembalikan!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonText: "Yes, delete it!",
              confirmButtonColor: "#d33",
            }).then((result) => {
              if (result.isConfirmed) {
                let link = "{{ route('masterdata.whDelete', ':id') }}";
                link = link.replace(':id', id);
                
                $.ajax({
                    type: "DELETE",
                    url: link,
                    success: function (res) {
                        Swal.fire(res.title, res.message, res.status);
                        table.ajax.reload();
                    },
                    error: function (xhr) {
                      Swal.fire("Error!", xhr.responseJSON?.message, "error");
                    }
                });

              }
            });

        })
    })
</script>
@endpush