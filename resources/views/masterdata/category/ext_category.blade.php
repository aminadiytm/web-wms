<div class="modal fade premium-modal" id="catModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content premium-modal-content">

            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Master Data</p>
                    <h5 class="modal-title mb-0">Create Category</h5>
                    <small class="text-muted">Add a new category for your product catalog.</small>
                </div>

                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="catForm">
                @csrf

                <div class="modal-body premium-modal-body">

                    <div class="premium-form-group">
                        <label class="premium-label">Category Name</label>
                        <div class="premium-input-wrap">
                            <span class="premium-input-icon">
                                <i class="fas fa-tag"></i>
                            </span>
                            <input
                                type="text"
                                class="premium-input"
                                name="name"
                                id="cat_name"
                                placeholder="Enter category name">
                        </div>
                    </div>

                    <div class="premium-form-group">
                        <label class="premium-label">Category Description</label>
                        <div class="premium-input-wrap">
                            <span class="premium-input-icon">
                                <i class="fas fa-align-left"></i>
                            </span>
                            <input
                                type="text"
                                class="premium-input"
                                name="desc"
                                id="cat_desc"
                                placeholder="Enter description">
                        </div>
                    </div>

                </div>

                <div class="modal-footer premium-modal-footer border-0">
                    <button
                        type="button"
                        class="btn btn-cancel-premium"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-save-premium"
                        id="btn-save">
                        <i class="fas fa-save me-2"></i>
                        Save Category
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade premium-modal" id="e_catModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content premium-modal-content">

            <!-- Header -->
            <div class="modal-header premium-modal-header border-0">
                <div>
                    <p class="modal-badge mb-2">Master Data</p>
                    <h5 class="modal-title mb-0">Edit Category</h5>
                    <small class="text-muted">Update category information.</small>
                </div>

                <button type="button" class="btn-close premium-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="e_catForm">
                @csrf
                <input type="hidden" name="cat_id" id="e_cat_id">

                <div class="modal-body premium-modal-body">

                    <!-- Category Name -->
                    <div class="premium-form-group">
                        <label class="premium-label">Category Name</label>
                        <div class="premium-input-wrap">
                            <span class="premium-input-icon">
                                <i class="fas fa-tag"></i>
                            </span>
                            <input
                                type="text"
                                class="premium-input"
                                name="name"
                                id="e_cat_name"
                                placeholder="Enter category name">
                        </div>
                    </div>

                    <!-- Category Description (tetap input, bukan textarea) -->
                    <div class="premium-form-group">
                        <label class="premium-label">Category Description</label>
                        <div class="premium-input-wrap">
                            <span class="premium-input-icon">
                                <i class="fas fa-align-left"></i>
                            </span>
                            <input
                                type="text"
                                class="premium-input"
                                name="desc"
                                id="e_cat_desc"
                                placeholder="Enter description">
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer premium-modal-footer border-0">
                    <button
                        type="button"
                        class="btn btn-cancel-premium"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-save-premium"
                        id="ebtn-save">
                        <i class="fas fa-save me-2"></i>
                        Update Category
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


@push('scripts')
    <script>
        $(function() {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
          });

          function formatDateWIB(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('id-ID', {
              timeZone: 'Asia/Jakarta',
              day: '2-digit',
              month: '2-digit',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            });
          }

          // 1) Draw DataTables
          const table = $('#cat_tbl').DataTable({
            ajax : '{{ route('masterdata.catList') }}',
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
                                  data-id="${row.cat_id}"
                                  title="Edit"
                              >
                                  <i class="fas fa-pen"></i>
                              </button>
                              <button
                                  type="button"
                                  class="action-icon action-icon-delete btn-delete"
                                  data-id="${row.cat_id}"
                                  title="Delete"
                              >
                                  <i class="fas fa-trash"></i>
                              </button>
                          </div>
                      `;
                  }
              },
              {data: 'DT_RowIndex', name: 'DT_RowIndex', class: "text-center", orderable: false, searchable: false},
              {data: 'cat_name', name: 'cat_name'},
              {data: 'cat_desc', name: 'cat_desc'},
              {
                data: 'cat_add_by', 
                name: 'cat_add_by',
              },
              {
                data: 'cat_upd_by', 
                name: 'cat_upd_by',
                render: function (data) { 
                  return data ? data : '-';
                }
              },
              {
                data: 'created_at',
                name: 'created_at',
                render: function (data, type, row) {
                  return formatDateWIB(data);
                }
              },
              {
                data: 'updated_at',
                name: 'updated_at',
                render: function (data, type, row) {
                  return formatDateWIB(data);
                }
              },
            ]
          })          

          // 2) reset error ketika modal dibukanah ka
          $('#catModal').on('shown.bs.modal', function () {
            $('#catForm')[0].reset();
            $('#cat_name').trigger('focus');
          });
        
          // 3) submit form via AJAX
          $('#catForm, #e_catForm').on('submit', function (e) {
            e.preventDefault();

            let data = $(this).serialize();
          
            $.ajax({
              url: "{{ route('masterdata.catStore') }}",
              method: "POST",
              data: data,
              success: function (res) {
                $('#catModal, #e_catModal').modal('hide');
                
                Swal.fire({
                  title: "Success!",
                  text: res.message,
                  icon: res.status,
                  confirmButtonText: 'OK'
                });
                
                // reload datatable
                table.ajax.reload();
              },
              error: function (xhr) {

                const err = xhr.responseJSON?.errors ?? {};
                let firstErr = err.name?.[0] || err.desc?.[0] || 'Terjadi Kesalahan Saat Data Disimpan';

                Swal.fire({
                  title: 'Error!',
                  text: firstErr,
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
              }
            });
          });

          // 4) Modal Edit
          $('#cat_tbl').on('click', '.btn-edit', function() {
            let id = $(this).data('id');

            let url = "{{ route('masterdata.getData', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
              url: url,
              method: "GET",
              success: function (res) {
                $('#e_cat_id').val(res.cat_id);
                $('#e_cat_name').val(res.cat_name);
                $('#e_cat_desc').val(res.cat_desc);

                $('#e_catModal').modal('show');
              },
              error: function (xhr) { 
                $('#e_catModal').modal('hide');

                Swal.fire({
                  title: "Error!",
                  text: xhr.responseJSON?.message || "Terjadi Kesalahan Saat Mengambil Data",
                  icon: "error"
                });
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

                let link = '{{ route('masterdata.catDelete', ':id') }}';
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
        
        });
    </script>
    
@endpush