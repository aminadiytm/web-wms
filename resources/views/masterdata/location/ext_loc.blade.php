@push('scripts')
@php
use App\Helpers\MenuPermissionHelper;
@endphp

<script>
    $(function() {        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#loc_wh').select2({
            placeholder: 'Select Warehouse',
            width: '100%',
            dropdownParent: $('#locModal'),
        });

        const locPermission = {
            canEdit: @json(MenuPermissionHelper::canEdit('masterdata.locIndex')),
            canDelete: @json(MenuPermissionHelper::canDelete('masterdata.locIndex'))
        };

        // 1) Render List Data
        const table = $('#loc_tbl').DataTable({
            ajax: '{{ route('masterdata.locList') }}',
            columns : [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center align-middle",
                    render: function (data, type, row) {
                        let actions = '';
                        
                        if (locPermission.canEdit) {
                            actions += `
                                <button
                                    type="button"
                                    class="action-icon action-icon-edit btn-edit"
                                    data-id="${row.id}"
                                    title="Edit"
                                >
                                    <i class="fas fa-pen"></i>
                                </button>
                            `;
                        }
                      
                        if (locPermission.canDelete) {
                            actions += `
                                <button
                                    type="button"
                                    class="action-icon action-icon-delete btn-delete"
                                    data-id="${row.id}"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                      
                        if (!actions) {
                            return `
                                <span class="text-muted" title="No action permission">
                                    <i class="fas fa-lock"></i>
                                </span>
                            `;
                        }
                      
                        return `
                            <div class="action-group d-flex justify-content-center align-items-center gap-2">
                                ${actions}
                            </div>
                        `;
                    }
                },
                {data:'DT_RowIndex', name:'DT_RowIndex', orderable: false, searchable: false},
                {data:'code', name:'code'},
                {data:'locdesc', name:'locdesc'},
                {data:'locwh', name:'locwh'},
                {data:'act', name:'act'},
                {data:'add', name:'add'},
                {data:'upd', name:'upd'},
                {data:'crea', name:'crea'},
                {data:'up', name:'up'}
            ]
        })

        // 2) Clear Add Modal
        $('#locModal').on('shown.bs.modal', function () {
            $('#locForm')[0].reset();
            $('#loc_wh').val('').trigger('change');
            $('#loc_code').focus();
        })

        // 3) Save Modal
        $('#locForm, #e_locForm').on('submit', function (e) { 
            e.preventDefault();

            let data = $(this).serialize();            

            $.ajax({
                url: "{{ route('masterdata.locStore') }}",
                method: "POST",
                data: data,
                success: function (res) {
                    $('#locModal, #e_locModal').modal('hide');
                    Swal.fire({
                        title: "Success!",
                        text: res.message,
                        icon: res.status
                    })

                    table.ajax.reload();
                },
                error: function (xhr) { 
                    const err = xhr.responseJSON?.errors ?? {};
                    let firstErr = err.loccd?.[0] || err.locwh?.[0] || err.locdesc?.[0];
                    Swal.fire(
                        "Error!",
                        firstErr,
                        "error"
                    )
                }
            });
        })

        // 4) Edit Show Modal
        $('#loc_tbl').on('click', '.btn-edit', function () {
            let locid = $(this).data('id');

            $('#e_locModal').modal('show');
            let link = "{{ route('masterdata.locgetData', ':id') }}";
            link = link.replace(':id', locid);

            $.ajax({
                url: link,
                method: "GET",
                success: function (res) {
                    $('#e_loc_id').val(res.loc_id);
                    $('#eloc_code').val(res.loc_code);
                    $('#eloc_desc').val(res.loc_desc);
                    $('#eloc_wh').val(res.ref_wh).trigger('change');
                    $('#eloc_act').val(res.loc_act);
                },
                error: function (xhr) {
                    $('#e_locModal').modal('hide');
                    Swal.fire(
                        "Errors!",
                        "An Error Occurred While Retrieving Edit Data",
                        "error",
                    )
                 }
            });
        })

        // 5) Delete Data
        $('#loc_tbl').on('click', '.btn-delete', function() {
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

                let link = '{{ route('masterdata.locDelete', ':id') }}';
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