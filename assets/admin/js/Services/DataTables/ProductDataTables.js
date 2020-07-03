import dt from 'datatables.net-dt';

export default (() => {
    let Public = {},
        Private = {};

    Private.tableRef = $('#data-table');

    Public.init = () => {
        Private.tableRef.DataTable( {
            serverSide: true,
            ajax: {
                url: Routing.generate('admin.get_product_list'),
                type: 'POST'
            },
            columns: [
                { data: 'code', title: 'Šifra' },
                { data: 'title', title: 'Naziv' },
                { data: 'price', title: 'Cena od', type: "num" },
                { data: 'status_text', title: 'Status', width: '200px', render: function (data, type, row, meta) {
                    const checkedAttr = row.status === 2 ? 'checked' : '';

                    let html = CAN_EDIT ? `<p class="status-text">${data}</p><input type="checkbox" class="set-active-product" data-slug="${row.slug}" ${checkedAttr}/>` : `<p class="status-text">${data}</p>`;

                    if (row.status === 3) {
                        html = `<p class="status-text">${data}</p>`;
                    }

                    return type === 'display' ? html : data;
                } },
                { data: 'position_text', title: 'Početna stranica', width: '200px', render: function (data, type, row, meta) {
                    let html = '';

                    if (data) {
                        html = `<p class="status-text d-block letter-capitalize">${Translator.trans(data, null, 'messages', LOCALE)}</p>`
                    }
;
                    return type === 'display' ? html : data;
                } },
                { data: 'slug', render: function (data, type, row, meta) {
                    const editLink = CAN_EDIT ? `<a class="btn btn-link" href="${Routing.generate('admin.edit_product_page', {slug: data})}">Izmeni</a> ` : '';
                    const removeButton = CAN_REMOVE ?`<button class="btn btn-danger remove-item-button" data-alias="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
            ],
            order: [[0, 'desc']],
            pageLength: 100,
        });
    };

    Public.reload = () => {
        Private.tableRef.DataTable().ajax.reload(null, false);
    };

    return Public;
});