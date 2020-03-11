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
                { data: 'id', title: 'Id' },
                { data: 'title', title: 'Naziv' },
                { data: 'price_from', title: 'Cena od', type: "num" },
                { data: 'status_text', title: 'Status', width: '200px', render: function (data, type, row, meta) {
                    const checkedAttr = row.status === 1 ? 'checked' : '';

                    let html = CAN_EDIT ? `<p class="status-text">${data}</p><input type="checkbox" class="set-active-ad" data-alias="${row.alias}" ${checkedAttr}/>` : `<p class="status-text">${data}</p>`;

                    if (row.status === 2) {
                        html = `<p class="status-text">${data}</p>`;
                    }

                    return type === 'display' ? html : data;
                } },
                { data: 'owner_name', title: 'Vlasnik', type: "string" },
                { data: 'show_on_main_page', title: 'Početna strana', type: "string" },
                { data: 'alias', render: function (data, type, row, meta) {
                    const editLink = CAN_EDIT ? `<a class="btn btn-link" href="${Routing.generate('ads_edit_page', {slug: data})}">Izmeni</a> ` : '';
                    const calendarLink = CAN_EDIT_CALENDAR ? `<a class="btn btn-link" href="${Routing.generate('ads_calendar_page', {slug: data})}">Kalendar</a>` : '';
                    const removeButton = CAN_REMOVE ?`<button class="btn btn-danger remove-item-button" data-alias="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+calendarLink+removeButton :
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