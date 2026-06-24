@push('scripts')
<script>
    $(function () {
        let detailRowIndex = 0;

        const productOptions = `
            <option value="">Choose product</option>
            @foreach ($products as $product)
                <option value="{{ $product->prd_id }}">
                    {{ $product->prd_name }}
                    @if(!empty($product->prd_code))
                        - {{ $product->prd_code }}
                    @endif
                </option>
            @endforeach
        `;

        const locationOptions = `
            <option value="">Choose location</option>
            @foreach ($locations as $location)
                <option value="{{ $location->loc_id }}">
                    {{ $location->loc_name }}
                    @if(!empty($location->loc_code))
                        - {{ $location->loc_code }}
                    @endif
                </option>
            @endforeach
        `;

        function createDetailRow(index) {
            return `
                <tr data-row-index="${index}">
                    <td class="text-center align-middle row-number fw-semibold text-secondary"></td>

                    <td>
                        <select name="details[${index}][ref_prd_id]" class="form-select" required>
                            ${productOptions}
                        </select>
                    </td>

                    <td>
                        <select name="details[${index}][ref_loc_id]" class="form-select" required>
                            ${locationOptions}
                        </select>
                    </td>

                    <td>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="details[${index}][qty_order]"
                            class="form-control text-end"
                            value="0"
                            required
                        >
                    </td>

                    <td>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="details[${index}][qty_rcv]"
                            class="form-control text-end"
                            value="0"
                            required
                        >
                    </td>

                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-detail-row">
                            Remove
                        </button>
                    </td>
                </tr>
            `;
        }

        function refreshRowNumbers() {
            $('#detail-table-body tr').each(function (index) {
                $(this).find('.row-number').text(index + 1);
            });
        }

        function addDetailRow() {
            $('#detail-table-body').append(createDetailRow(detailRowIndex));
            detailRowIndex++;
            refreshRowNumbers();
        }

        function removeDetailRow(button) {
            button.closest('tr').remove();
            refreshRowNumbers();
        }

        function validateDetailRows() {
            const rows = $('#detail-table-body tr');

            if (rows.length === 0) {
                alert('Detail row must be filled at least one.');
                return false;
            }

            let isValid = true;

            rows.each(function () {
                const productId = $(this).find('select[name*="[ref_prd_id]"]').val();
                const locationId = $(this).find('select[name*="[ref_loc_id]"]').val();
                const qtyOrder = $(this).find('input[name*="[qty_order]"]').val();
                const qtyRcv = $(this).find('input[name*="[qty_rcv]"]').val();

                if (!productId || !locationId || qtyOrder === '' || qtyRcv === '') {
                    isValid = false;
                    return false;
                }
            });

            if (!isValid) {
                alert('Please complete all detail rows before saving.');
            }

            return isValid;
        }

        $('#btn-add-detail-row').on('click', function () {
            addDetailRow();
        });

        $(document).on('click', '.btn-remove-detail-row', function () {
            removeDetailRow($(this));
        });

        $('#inbound-form').on('submit', function (e) {
            if (!validateDetailRows()) {
                e.preventDefault();
            }
        });

        addDetailRow();
    });
</script>
@endpush