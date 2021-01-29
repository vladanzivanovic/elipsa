require('./DataTableOptions');
import AppHelperService from "../../../../js/Helper/AppHelperService";

export default (() => {
    let Public = {},
        Private = {};

    Private.tableRef = $('#data-table');

    Public.init = () => {
        const options = Object.assign({}, window.DATATABLE_OPTIONS, {
            ajax: {
                url: Routing.generate('admin.get_category_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'rs_name', name: 'titles', title: 'Naziv - srpski' },
                { data: 'en_name', name: 'titles', title: 'Naziv - engleski' },
                { data: 'parent', name: 'parent', title: 'Glavna kategorija' },
                { data: 'slug', orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${AppHelperService.generateLocalizedUrl('admin.edit_category_page', {slug: data})}">Izmeni</a> ` : '';
                        const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-slug="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
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