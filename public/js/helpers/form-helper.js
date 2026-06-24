function sanitizeTextInput(selector, regex = /[^a-zA-Z0-9 .\-]/g) {
    $(selector).on('input', function() {
        const cleanValue = $(this).val().replace(regex, '');
        $(this).val(cleanValue);
    });
}

function escapeHtml(value) {

    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

}

function appendTableDataToForm(
    formSelector,
    table,
    inputName,
    fields
) {

    $(`.${inputName}-hidden-input`).remove();

    const rows = table.rows().data().toArray();

    rows.forEach((row, index) => {

        fields.forEach(field => {

            $(formSelector).append(`
                <input
                    type="hidden"
                    class="${inputName}-hidden-input"
                    name="${inputName}[${index}][${field}]"
                    value="${escapeHtml(row[field] ?? '')}">
            `);

        });

    });

}