@push('scripts')
<script>
$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let selectedOutboundId = null;
    let detailTable = null;

    const masterTable = $('#outb_tbl').DataTable({
        ajax: '{{ route('transaction.outbList') }}',
        columns: [
            { data: 'action', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'code', name: 'code' },
            { data: 'whnm', name: 'whnm' },
            { data: 'customer', name: 'customer' },
            { data: 'status', name: 'status' },
            { data: 'shipped', name: 'shipped' },
            { data: 'add', name: 'add' },
            { data: 'upd', name: 'upd' },
            { data: 'crea', name: 'crea' },
            { data: 'upda', name: 'upda' },
        ]
    });

    function initDetailTable() {
        detailTable = $('#outb_detail_tbl').DataTable({
            ajax: {
                url: '{{ route('transaction.outbdetList') }}',
                type: 'GET',
                data: function (d) {
                    d.outb_id = selectedOutboundId;
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
                    data: 'qty_req',
                    name: 'qty_req',
                    className: 'text-end align-middle'
                },
                {
                    data: 'qty_picked',
                    name: 'qty_picked',
                    className: 'text-end align-middle'
                }
            ],
            language: {
                emptyTable: 'No Detail Selected'
            }
        });
    }

    function loadDetail(rowData, clickedRow) {
        selectedOutboundId = rowData.id;

        $('#outb_tbl tbody tr').removeClass('selected-row');
        clickedRow.addClass('selected-row');

        $('#detail-subtitle').text('Outbound Code: ' + rowData.code);

        if (!detailTable) {
            initDetailTable();
            return;
        }

        detailTable.ajax.reload();
    }

    $('#outb_tbl tbody').on('click', 'tr', function () {
        const rowData = masterTable.row(this).data();
        if (!rowData) return;

        loadDetail(rowData, $(this));
    });
});
</script>
@endpush