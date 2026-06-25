@push('scripts')
<script src="{{ asset('js/helpers/form-helper.js') }}"></script>
<script>
$(function () {
    firstStep();
    initDetailTable();
    addDet();
    modalAction();
    submitForm();
    sanitizeTextInput('#outb_customer');
    onWh();
    applyModeRules();
});

let detailTable;
let activePicker = null;
let activeRow = null;
let activeCell = null;

function mode() {
    return $('#outbound-form').data('mode');
}

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
    $.ajax({
        type: 'GET',
        url: '{{ route('support.wh') }}',
        success: function (data) {
            $('#wh').append('<option value="">Select Warehouse</option>');

            data.forEach(value => {
                $('#wh').append(
                    `<option value="${value.wh_id}">${value.wh_code} - ${value.wh_name}</option>`
                );
            });

            loadOutbound();
        }
    });

    $('#wh').on('change', function () {
        if (mode() !== 'create') return;

        detailTable.clear().draw();
    });
}

function checkHeader() {
    const fields = [
        { selector: '#wh', message: 'Warehouse Is Required!' },
        { selector: '#outb_customer', message: 'Customer Is Required!' },
        { selector: '#outb_shipped', message: 'Shipped Date Is Required!' },
    ];

    if (mode() === 'create') {
        fields.push({
            selector: '#approval_route_id',
            message: 'Routing Approval Is Required!'
        });
    }

    for (const field of fields) {
        const val = $(field.selector).val();

        if (val === '' || val === null || val === undefined) {
            Swal.fire('Warning', field.message, 'warning');
            $(field.selector).focus();
            return false;
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
                render: function () {
                    return '';
                }
            },
            {
                data: 'product_name',
                defaultContent: '',
                createdCell: function (cell, cellData, rowData) {
                    const isLocked = mode() === 'confirm';

                    $(cell)
                        .text(rowData.product_name || 'Click to select product')
                        .toggleClass('detail-picker-cell', !isLocked)
                        .off('click.product');

                    if (!isLocked) {
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
                    const isLocked = mode() === 'confirm';

                    $(cell)
                        .text(rowData.location_name || 'Click to select location')
                        .toggleClass('detail-picker-cell', !isLocked)
                        .off('click.location');

                    if (!isLocked) {
                        $(cell).on('click.location', function () {
                            openSelectModal('location', rowData, $(this).closest('tr'), cell);
                        });
                    }
                }
            },
            {
                data: 'available_qty',
                className: 'text-end',
                defaultContent: 0,
                render: function (data) {
                    return data ?? 0;
                }
            },
            {
                data: 'qty_req',
                className: 'text-end',
                defaultContent: 0,
                createdCell: function (cell, cellData, rowData) {
                    qtyReqCell(cell, rowData);
                }
            },
            {
                data: 'qty_picked',
                className: 'text-end',
                defaultContent: 0,
                createdCell: function (cell, cellData, rowData) {
                    pickedCell(cell, rowData);
                }
            },
            {
                data: 'qty_outstanding',
                className: 'text-end',
                defaultContent: 0,
                render: function (data) {
                    return data ?? 0;
                }
            },
            {
                data: null,
                className: 'text-center',
                render: function () {
                    if (mode() === 'confirm') {
                        return `
                            <button type="button"
                                    class="btn btn-sm btn-light border text-muted btn-delete-row"
                                    disabled
                                    title="Delete tidak tersedia saat confirm">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }

                    return `
                        <button type="button" class="btn btn-sm btn-danger btn-delete-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    $('#detail-entry-table tbody')
        .off('click', '.btn-delete-row')
        .on('click', '.btn-delete-row', function () {
            if ($(this).prop('disabled')) return;

            detailTable
                .row($(this).closest('tr'))
                .remove()
                .draw(false);
        });

    detailTable.on('draw.dt', function () {
        detailTable.column(0).nodes().each(function (cell, index) {
            cell.innerHTML = index + 1;
        });
    });
}

function qtyReqCell(cell, rowData) {
    if (mode() === 'confirm') {
        $(cell).text(rowData.qty_req ?? 0);
        return;
    }

    $(cell).html(`
        <input
            type="number"
            min="0"
            class="form-control form-control-sm text-end"
            value="${rowData.qty_req ?? 0}"
        >
    `);

    $(cell).find('input')
        .off('input.qtyreq')
        .on('input.qtyreq', function () {
            let input = $(this);
            let value = parseFloat(input.val()) || 0;
            let availableQty = parseFloat(rowData.available_qty) || 0;

            if (value < 0) {
                value = 0;
            }

            if (value > availableQty) {
                value = availableQty;

                Swal.fire({
                    icon: 'warning',
                    title: 'Qty Request Tidak Valid',
                    text: `Qty request tidak boleh melebihi available stock (${availableQty}).`,
                    confirmButtonText: 'OK'
                });
            }

            rowData.qty_req = value;
            rowData.qty_outstanding = value - (parseFloat(rowData.qty_picked_existing) || 0);

            input.val(value);

            const rowIndex = detailTable.row(input.closest('tr')).index();
            const outstandingCell = detailTable.cell(rowIndex, 7).node();
            $(outstandingCell).text(rowData.qty_outstanding);
        });
}

function pickedCell(cell, rowData) {
    if (mode() !== 'confirm') {
        $(cell).text(0);
        rowData.qty_picked = 0;
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

    $(cell).find('input')
        .off('input.qtypicked')
        .on('input.qtypicked', function () {
            let value = parseFloat($(this).val()) || 0;

            if (value < 0) value = 0;

            if (value > rowData.qty_outstanding) {
                value = rowData.qty_outstanding;
                Swal.fire('Warning', 'Qty picked tidak boleh melebihi outstanding', 'warning');
            }

            rowData.qty_picked = value;
            $(this).val(value);
        });
}

function addDet() {
    $('#btn-detail').on('click', function () {
        if (!checkHeader()) return;

        detailTable.row.add({
            id: '',
            product_id: '',
            product_name: '',
            product_desc: '-',
            location_id: '',
            location_name: '',
            available_qty: 0,
            qty_req: 0,
            qty_picked_existing: 0,
            qty_outstanding: 0,
            qty_picked: 0,
        }).draw(false);
    });
}

function openSelectModal(type, rowData, rowNode, cell) {
    if (mode() === 'confirm') return;

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

        if (!id) {
            Swal.fire('Warning', 'Please select warehouse first!', 'warning');
            return;
        }

        url = "{{ route('support.loc', ':id') }}";
        url = url.replace(':id', id);
        selectedValue = rowData.location_id;
    }

    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        success: function (data) {
            let options = '<option value="">Select Data</option>';

            data.forEach(value => {
                if (type === 'product') {
                    options += `
                        <option value="${value.prd_id}"
                                data-name="${value.prd_name}"
                                data-desc="${value.prd_desc}">
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

            loadAvailableStock(rowData, activeRow.node);
        }

        $('#selectModal').modal('hide');
    });
}

function loadAvailableStock(rowData, rowNode) {
    if (!rowData.product_id || !rowData.location_id || !$('#wh').val()) {
        return;
    }

    $.ajax({
        type: 'GET',
        url: '{{ route('inventory.stockAvailable') }}',
        data: {
            product_id: rowData.product_id,
            warehouse_id: $('#wh').val(),
            location_id: rowData.location_id,
        },
        success: function (res) {
            const availableQty = parseFloat(res.available_qty) || 0;
            rowData.available_qty = availableQty;

            const availableCell = detailTable.cell(rowNode, 4).node();
            $(availableCell).text(availableQty);

            if ((parseFloat(rowData.qty_req) || 0) > availableQty) {
                rowData.qty_req = availableQty;
                rowData.qty_outstanding = availableQty - (parseFloat(rowData.qty_picked_existing) || 0);

                const qtyReqCellNode = detailTable.cell(rowNode, 5).node();
                $(qtyReqCellNode).find('input').val(availableQty);

                const outstandingCell = detailTable.cell(rowNode, 7).node();
                $(outstandingCell).text(rowData.qty_outstanding);

                Swal.fire({
                    icon: 'warning',
                    title: 'Qty Request Disesuaikan',
                    text: `Qty request dikembalikan ke available stock (${availableQty}).`,
                    confirmButtonText: 'OK'
                });
            }
        }
    });
}

function loadOutbound() {
    const id = $('#outb_id').val();

    if (!id) return;

    $.ajax({
        url: `/Transaction/Outbound/${id}/data`,
        type: 'GET',
        success: function (res) {
            $('#wh')
                .val(res.ref_wh_id)
                .trigger('change')
                .prop('disabled', true);

            $('#outb_customer').val(res.outb_customer);
            $('#outb_stat').val(res.outb_stat);
            $('#outb_stat_display').val(res.outb_stat);
            $('#outb_shipped').val(res.outb_shipped);

            detailTable.clear();

            res.outbounddets.forEach(item => {
                detailTable.row.add({
                    id: item.outbd_id,
                    product_id: item.ref_prd_id,
                    product_name: item.product?.prd_name ?? '',
                    product_desc: item.product?.prd_desc ?? '-',
                    location_id: item.ref_loc_id,
                    location_name: item.location?.loc_desc ?? '',
                    available_qty: 0,
                    qty_req: item.qty_req,
                    qty_picked_existing: item.qty_picked,
                    qty_outstanding: item.qty_req - item.qty_picked,
                    qty_picked: 0,
                });
            });

            detailTable.draw();
        }
    });
}

function applyModeRules() {
    if (mode() === 'confirm') {
        $('#wh').prop('disabled', true);
        $('#outb_customer').prop('disabled', true);
        $('#outb_stat').prop('disabled', true);
        $('#outb_shipped').prop('disabled', true);
        $('#btn-detail').hide();
    }
}

function submitForm() {
    $('#outbound-form').on('submit', function (e) {
        e.preventDefault();

        if (!checkHeader()) return;

        const details = detailTable.rows().data().toArray();

        if (details.length === 0) {
            Swal.fire('Warning', 'Detail transaction cannot be empty!', 'warning');
            return;
        }

        Swal.fire({
            title: mode() === 'confirm' ? 'Confirm Outbound?' : 'Save Outbound?',
            text: 'Are you sure you want to process this outbound transaction?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Process',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                type: 'POST',
                url: $('#outbound-form').attr('action'),
                dataType: 'json',
                data: {
                    _token: $('input[name="_token"]').val(),
                    _method: $('input[name="_method"]').val() || 'POST',
                    wh: $('#wh').val(),
                    approval_route_id: $('#approval_route_id').val(),
                    outb_customer: $('#outb_customer').val(),
                    outb_stat: $('#outb_stat').val(),
                    outb_shipped: $('#outb_shipped').val(),
                    details: details
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message || 'Outbound berhasil diproses',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                },
                error: function (xhr) {
                    let message = 'Gagal memproses outbound';

                    if (xhr.status === 422) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
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
</script>
@endpush