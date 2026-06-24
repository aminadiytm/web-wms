@push('scripts')
<script>
    $(function() {        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#prd_cat').select2({
            placeholder: 'Select Category',
            width: '100%',
            dropdownParent: $('#prdModal'),
        });

        $('#eprd_cat').select2({
            placeholder: 'Select Category',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#e_prdModal'),
            minimumResultsForSearch: 0
        });

        // 1) Render List Data
        const table = $('#prd_tbl').DataTable({
            ajax: '{{ route('masterdata.prdList') }}',
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
                {data:'prdnm', name:'prdnm'},
                {data:'prdunit', name:'prdunit'},
                {data:'prdcat', name:'prdcat'},
                {data:'prdstck', name:'prdstck'},
                {data:'prddesc', name:'prddesc'},
                {data:'add', name:'add'},
                {data:'upd', name:'upd'},
                {data:'crea', name:'crea'},
                {data:'up', name:'up'}
            ]
        })

        // 2) Clear Add Modal
        $('#prdModal').on('shown.bs.modal', function () {
            $('#prdForm')[0].reset();
            $('#prd_cat').val('').trigger('change');
            $('#prd_code').focus();
        })

        // 3) Save Modal
        $('#prdForm, #e_prdForm').on('submit', function (e) { 
            e.preventDefault();

            let data = $(this).serialize();            

            $.ajax({
                url: "{{ route('masterdata.prdStore') }}",
                method: "POST",
                data: data,
                success: function (res) {
                    $('#prdModal, #e_prdModal').modal('hide');
                    Swal.fire({
                        title: "Success!",
                        text: res.message,
                        icon: res.status
                    })

                    table.ajax.reload();
                },
                error: function (xhr) { 
                    const err = xhr.responseJSON?.errors ?? {};
                    let firstErr = err.prdcd?.[0] || err.prdnm?.[0] || err.prdunit?.[0] || err.prdcat?.[0] || err.prdstck?.[0];
                    Swal.fire(
                        "Error!",
                        firstErr,
                        "error"
                    )
                }
            });
        })

        // 4) Edit Show Modal
        $('#prd_tbl').on('click', '.btn-edit', function () {
            let prdid = $(this).data('id');

            $('#e_prdModal').modal('show');
            let link = "{{ route('masterdata.getEdit', ':id') }}";
            link = link.replace(':id', prdid);

            $.ajax({
                url: link,
                method: "GET",
                success: function (res) {
                    $('#e_prd_id').val(res.prd_id);
                    $('#eprd_code').val(res.prd_code);
                    $('#eprd_name').val(res.prd_name);
                    $('#eprd_unit').val(res.prd_unit);
                    $('#eprd_cat').val(res.ref_cat).trigger('change');
                    $('#eprd_stck').val(res.prd_min_stock);
                    $('#eprd_desc').val(res.prd_desc);
                    
                },
                error: function (xhr) { 
                    $('#e_prdModal').modal('hide');
                    Swal.fire(
                        "Errors!",
                        "An Error Occurred While Retrieving Edit Data",
                        "error",
                    )
                 }
            });
        })

        // 5) Delete Data
        $('#cat_tbl').on('click', '.btn-delete', function() {
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

                let link = '{{ route('masterdata.prdDelete', ':id') }}';
                link = link.replace(':id', id);

                $.ajax({
                  url: link,
                  method: "DELETE",
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