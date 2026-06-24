

@push('scripts')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                }
            })

            let selectedInboundId = null;
            let detailTable = null;

            const masterTable = $('#inb_tbl').DataTable({
                ajax : '{{ route('transaction.inbList') }}',
                columns: [
                    {data:'action', orderable: false, searchable: false, className: 'text-center align-middle'},
                    {data:'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data:'code', name:'code'},
                    {data:'whnm', name:'whnm'},
                    {data:'supp', name:'supp'},
                    {data:'status', name:'status'},
                    {data:'rcv', name:'rcv'},
                    {data:'add', name:'add'},
                    {data:'upd', name:'upd'},
                    {data:'crea', name:'crea'},
                    {data:'upda', name:'upda'},                
                ]
            })

            function initDetailTable() {
                detailTable = $('#inb_detail_tbl').DataTable({
                    ajax: {
                        url: '{{ route('transaction.inbdetList') }}',
                        type: 'GET',
                        data: function (d) {
                            d.inb_id = selectedInboundId;
                        }
                    },
                    dom: 'rt',
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            className: 'text-center align-middle'
                        },
                        { data: 'product_name', name: 'product_name' },
                        { data: 'location_name', name: 'location_name' },
                        {
                            data: 'qty_order',
                            name: 'qty_order',
                            className: 'text-end align-middle'
                        },
                        {
                            data: 'qty_rcv',
                            name: 'qty_rcv',
                            className: 'text-end align-middle'
                        }
                    ],
                    language: {
                        emptyTable: 'No Detail Selected'
                    }
                });
            }
        
            function loadDetail(rowData, clickedRow) {
                selectedInboundId = rowData.id;
            
                $('#inb_tbl tbody tr').removeClass('selected-row');
                clickedRow.addClass('selected-row');
            
                $('#detail-subtitle').text('Inbound Code: ' + rowData.code);
            
                if (!detailTable) {
                    initDetailTable();
                    return;
                }
            
                detailTable.ajax.reload();
            }
        
            $('#inb_tbl tbody').on('click', 'tr', function () {
                const rowData = masterTable.row(this).data();
                if (!rowData) return;
                
                loadDetail(rowData, $(this));
            });
        
        })
    </script>
@endpush