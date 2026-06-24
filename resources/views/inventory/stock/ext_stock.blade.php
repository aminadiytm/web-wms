@push('scripts')
<script>
$(function () {
    $('#stock_tbl').DataTable({
        ajax: '{{ route('inventory.stockList') }}',
        processing: true,
        serverSide: true,
        dom: 'rtip',
        columns: [
            {
                data: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            { data: 'product_code', name: 'prd.prd_code' },
            { data: 'product_name', name: 'prd.prd_name' },
            {
                data: 'warehouse_name',
                name: 'warehouse_name',
            },
            {
                data: 'location_name',
                name: 'location_name',
            },
            {
                data: 'qty_on_hand',
                name: 'stocks.qty_on_hand',
                className: 'text-end'
            },
            {
                data: 'qty_reserved',
                name: 'stocks.qty_reserved',
                className: 'text-end'
            },
            {
                data: 'available_qty',
                name: 'available_qty',
                className: 'text-end',
                orderable: false,
                searchable: false
            },
        ]
    });
});
</script>
@endpush