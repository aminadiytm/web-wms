@push('scripts')
<script src="{{ asset('js/helpers/form-helper.js') }}"></script>
<script>
    $(function () {
        firstStep();
        onWh();
        addDet();
        initDetailTable();
        modalAction();
        submitForm();
        sanitizeTextInput('#inb_supplier');
        loadInbound();
        applyModeRules();
    });

    let detailTable;
    let activePicker = null;
    let activeRow = null;
    let activeCell = null;

    function firstStep() {
        $('#wh').select2({
                placeholder: 'Select Warehouse',
        }); 

        $('#selectModalOption').select2({
            placeholder: 'Select Data',
            allowClear: true,
            dropdownParent: $('#selectModal'),
            width: '100%'
        });

    }

    function onWh() {
        let link = '{{ route('support.wh') }}';
        $.ajax({
            type: "GET",
            url: link,
            success: function (data) {
                $('#wh').append('<option value="">Select Warehouse</option>');
                data.forEach(value => {
                    $('#wh').append(
                        `<option value="${value.wh_id}">${value.wh_code}  -  ${value.wh_name}</option>`
                    )
                });               
            }
        });

        $('#wh').on('change', function () {
        
            detailTable.rows().every(function () {
            
                const rowData = this.data();
            
                rowData.location_id = '';
                rowData.location_name = '';
            
                const rowNode = this.node();
            
                const locationCell = detailTable.cell(rowNode, 3).node();
            
                $(locationCell).text('Click to select location');
            
            });
        
        });
        
    }

    function checkHeader() {
        const fields = [
            {
                selector: '#wh',
                message: 'Warehouse Is Required!'
            },
            {
                selector: '#inb_supplier',
                message: 'Supplier Is Required!'
            },
            {
                selector: '#inb_stat',
                message: 'Status Is Required!'
            },
            {
                selector: '#inb_rcv',
                message: 'Receive Date Is Required!'
            },
            {
                selector: '#approval_route_id',
                message: 'Approval Is Required!'
            }
        ];

        for (const field of fields) {
            const val = $(field.selector).val();
            if(val === '' || val === null) {
                Swal.fire('Warning', field.message, 'warning');
                $(field.selector).focus();
                return false 
            }
        }
        return true;
    }

    function initDetailTable() {
        detailTable = $('#detail-entry-table').DataTable({
            data: [],
            searching: false,
            paging: false,
            info: false,
            ordering: false,
            autoWidth: false,
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return '';
                    }
                },
                {
                    data: 'product_name',
                    defaultContent: '',
                    createdCell: function (cell, cellData, rowData) {
                        const mode = $('#inbound-form').data('mode');

                        $(cell)
                            .text(rowData.product_name || 'Click to select product')
                            .toggleClass('detail-picker-cell', mode !== 'receive')
                            .off('click.product');

                        if (mode !== 'receive') {
                            $(cell).on('click.product', function () {
                                openSelectModal('product', rowData, $(this).closest('tr'), cell);
                            });
                        }
                    }
                },
                {
                    data: 'product_desc',
                    render: function (data) { 
                        return data || '-';
                    }
                },
                {
                    data: 'location_name',
                    defaultContent: '',
                    createdCell: function (cell, cellData, rowData) {
                        const mode = $('#inbound-form').data('mode');

                        $(cell)
                            .text(rowData.location_name || 'Click to select location')
                            .toggleClass('detail-picker-cell', mode !== 'receive')
                            .off('click.location');

                        if (mode !== 'receive') {
                            $(cell).on('click.location', function () {
                                openSelectModal('location', rowData, $(this).closest('tr'), cell);
                            });
                        }
                    }
                },
                {
                    data: 'qty_order',
                    className: 'text-end',
                    defaultContent: 0,
                    createdCell: function (cell, cellData, rowData) {
                        const mode = $('#inbound-form').data('mode');

                        if (mode === 'receive') {
                            $(cell).text(rowData.qty_order ?? 0);
                            return;
                        }
                    
                        numberCell(cell, rowData, 'qty_order');
                    }
                },
                {
                    data: 'qty_receive',
                    className: 'text-end',
                    defaultContent: 0,
                    createdCell: function (cell, cellData, rowData) {
                        receiveCell(cell, rowData);
                    }
                },
                {
                    data: 'qty_outstanding',
                    className: 'text-end',
                    defaultContent: 0,
                    render: function(data) {
                        return data ?? 0;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function () {
                        const mode = $('#inbound-form').data('mode');

                        if (mode === 'receive') {
                            return `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-secondary btn-delete-row"
                                    disabled
                                    title="Delete tidak tersedia saat proses receive"
                                >
                                    Delete
                                </button>
                            `;
                        }
                    
                        return `
                            <button
                                type="button"
                                class="btn btn-sm btn-danger btn-delete-row"
                            >
                                Delete
                            </button>
                        `;
                        
                    }
                }
            ]
        });

        $('#detail-entry-table tbody').off('click', '.btn-delete-row')
            .on('click', '.btn-delete-row', function () {
                detailTable.row($(this).closest('tr')).remove().draw(false);
            });

        detailTable.on('draw.dt', function () {
        
            detailTable.column(0).nodes().each(function(cell, index) {
            
                cell.innerHTML = index + 1;
            
            });
        
        });
    }

    function numberCell(cell, rowData, fieldName) {
        $(cell)
            .html(`
                <input
                    type="number"
                    min="0"
                    class="form-control form-control-sm detail-qty-input text-end"
                    value="${rowData[fieldName] ?? 0}"
                >
            `);

        $(cell).find('input')
            .off('change.qty')
            .on('change.qty', function () {
                let val = parseFloat($(this).val());

                if (isNaN(val) || val < 0) val = 0;

                rowData[fieldName] = val;
                $(this).val(val);
            });
    }


    function addDet() {
        $('#btn-detail').on('click', function () {
            if (!checkHeader()) return;

            detailTable.row.add({
                product_id: '',
                product_name: '',
                product_desc: '-',
                location_id: '',
                location_name: '',
                qty_order: 0,
                qty_received: 0,
                qty_outstanding: 0,
                qty_receive: 0
            }).draw(false);
        });
    }

    function openSelectModal(type, rowData, rowNode, cell) {
        const mode = $('#inbound-form').data('mode');
        
        if (mode === 'receive') {
            return;
        }

        activePicker = type;

        activeRow = {
            data: rowData,
            node: rowNode
        };

        activeCell = cell;        

        let url = '';
        let selectedValue = '';

        if (type === 'product') {
            $('#selectModalTitle').text('Select Product');
            url = "{{ route('support.prd') }}";
            selectedValue = rowData.product_id;
        }

        if (type === 'location') {
            $('#selectModalTitle').text('Select Location');
            let id = $('#wh').val();
            url = "{{ route('support.loc', ':id' ) }}";
            url = url.replace(':id', id);
            selectedValue = rowData.location_id;
        }

        $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function (data) {
                let options = '<option value="">Select Data</option>';

                data.forEach(value => {
                    if (type === 'product') {
                        options += `
                            <option value="${value.prd_id}" 
                                    data-name="${value.prd_name}"
                                    data-desc="${value.prd_desc}"
                            >
                                ${value.prd_name} - ${value.prd_desc}
                            </option>
                        `;
                    }

                    if (type === 'location') {
                        options += `
                            <option value="${value.loc_id}" data-name="${value.loc_desc}">
                                ${value.loc_code} - ${value.loc_desc}
                            </option>
                        `;
                    }
                });

                $('#selectModalOption')
                    .html(options)
                    .val(selectedValue || '')
                    .trigger('change');
            }
        });

        $('#selectModal').modal('show');
    }

    function modalAction() {
        $('#btnSelectModal').off('click').on('click', function () {
            const selected = $('#selectModalOption option:selected');

            if (!selected.val()) {
                Swal.fire('Warning', 'Please select data first!', 'warning');
                return;
            }

            const id = selected.val();
            const name = selected.data('name');
            const rowData = activeRow.data;

            if (activePicker === 'product') {
                rowData.product_id = id;
                rowData.product_name = name;
                rowData.product_desc = selected.data('desc') || '-';

                $(activeCell).text(name);
                
                const descCell = detailTable.cell(activeRow.node, 2).node();
                $(descCell).text(rowData.product_desc);

            }

            if (activePicker === 'location') {
                rowData.location_id = id;
                rowData.location_name = name;

                $(activeCell).text(name);
            }

            $('#selectModal').modal('hide');
        });
    }

    function submitForm() {
        $('#inbound-form').on('submit', function (e) {
            e.preventDefault();

            if (!checkHeader()) return;

            const details = detailTable.rows().data().toArray();

            if (details.length === 0) {
                Swal.fire('Warning', 'Detail transaction cannot be empty!', 'warning');
                return;
            }

            Swal.fire({
                title: 'Save Inbound?',
                text: 'Are you sure you want to save this inbound transaction?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    type: 'POST',
                    url: $('#inbound-form').attr('action'),
                    dataType: 'json',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        _method: $('input[name="_method"]').val() || 'POST',
                        wh: $('#wh').val(),
                        inb_supplier: $('#inb_supplier').val(),
                        inb_stat: $('#inb_stat').val(),
                        inb_rcv: $('#inb_rcv').val(),
                        approval_route_id: $('#approval_route_id').val(),
                        details: details
                    },
                    beforeSend: function () {
                        Swal.fire({
                            title: 'Saving...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message || 'Inbound berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = res.redirect;
                        });
                    },
                    error: function (xhr) {
                        let message = 'Gagal menyimpan inbound';

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('\n');
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            });
        });
    }

    function loadInbound()
    {
        const id = $('#inb_id').val();

        if (!id) return;

        $.ajax({
            url: `/Transaction/Inbound/${id}/data`,
            type: 'GET',
            success: function(res){

                $('#wh')
                    .val(res.ref_wh_id)
                    .trigger('change')
                    .prop('disabled', true);

                $('#inb_supplier').val(res.inb_supplier);
                $('#inb_stat').val(res.inb_stat);
                $('#inb_stat_display').val(res.inb_stat);
                $('#inb_rcv').val(res.inb_rcv);
                $('#approval_route_id').val(res.approval_route_id);

                detailTable.clear();

                res.inbounddets.forEach(item => {

                detailTable.row.add({
                    id: item.inbd_id,
                    product_id: item.ref_prd_id,
                    product_name: item.product.prd_name,
                    product_desc: item.product.prd_desc,
                    location_id: item.ref_loc_id,
                    location_name: item.location.loc_desc,
                    qty_order: item.qty_order,
                    qty_received: item.qty_rcv,
                    qty_outstanding: item.qty_order - item.qty_rcv,
                    qty_receive: 0
                });

                });

                detailTable.draw();
            }
        });
    }

    function applyModeRules()
    {
        const mode = $('#inbound-form').data('mode');

        if (mode === 'receive') {
            $('#wh').prop('disabled', true);
            $('#inb_supplier').prop('disabled', true);
            $('#inb_stat').prop('disabled', true);
            $('#inb_rcv').prop('disabled', true);
            $('#approval_route_id').prop('disabled', true);
            $('#btn-detail').hide();
        }

        if (mode === 'create' || mode === 'edit') {
            // qty receive akan dikunci 0 / readonly

        }
    }

    function receiveCell(cell, rowData)
    {
        const mode = $('#inbound-form').data('mode');
    
        if (mode !== 'receive') {
            $(cell).text(0);
            rowData.qty_receive = 0;
            return;
        }
    
        $(cell).html(`
            <input
                type="number"
                min="0"
                max="${rowData.qty_outstanding}"
                class="form-control form-control-sm text-end"
                value="0"
            >
        `);
    
        $(cell).find('input').on('input', function () {
            let value = parseFloat($(this).val()) || 0;
        
            if (value > rowData.qty_outstanding) {
                value = rowData.qty_outstanding;
                Swal.fire('Warning', 'Qty receive tidak boleh melebihi outstanding', 'warning');
            }
        
            if (value < 0) value = 0;
        
            rowData.qty_receive = value;
            $(this).val(value);
        });
    }
    
</script>
@endpush