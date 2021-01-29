require('./DataTableOptions');
import AppHelperService from "../../../../js/Helper/AppHelperService";

export default (() => {
    let Public = {},
        Private = {};

    Private.tableRef = $('#data-table');

    Public.init = () => {
        let title = 'Povezani proizvodi';
        if (ROUTE_SUB_NAME === 'blog') {
            title = 'Povezani blogovi';
        }

        const options = Object.assign({}, window.DATATABLE_OPTIONS, {
            ajax: {
                url: Routing.generate(`admin.get_${ROUTE_SUB_NAME}_tags_list`),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'rs_name', name: 'rs_name', title: 'Naziv - srpski' },
                { data: 'en_name', name: 'en_name', title: 'Naziv - engleski' },
                { data: 'total_products', name: 'total_products', title: title},
                { data: 'slug', orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${AppHelperService.generateLocalizedUrl(`admin.edit_${ROUTE_SUB_NAME}_tag_page`, {slug: data})}">Izmeni</a> ` : '';
                        const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-slug="${data}" data-total-products="${row.total_products}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    }},
            ],
            order: [[0, 'desc']],
        });

        Private.tableRef.DataTable(options);
    };

    Public.reload = () => {
        Private.tableRef.DataTable().ajax.reload(null, false);
    };

    return Public;
});