import dt from 'datatables.net-dt';
import AppHelperService from "../../../../js/Helper/AppHelperService";

export default (() => {
    let Public = {},
        Private = {};

    Private.tableRef = $('#data-table');

    Public.init = () => {
        Private.tableRef.DataTable( {
            serverSide: true,
            ajax: {
                url: Routing.generate('admin.get_product_colors_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', title: 'Id' },
                { data: 'hex', title: 'Boja', render: function(data, type) {
                    return type === 'display' ?
                        `<span style="display: block; width: 20px; height: 20px; background-color: ${data}"></span>` :
                        data;
                    } },
                { data: 'rs_name', title: 'Naziv - srpski' },
                { data: 'en_name', title: 'Naziv - engleski' },
                { data: 'id', render: function (id, type, row, meta) {
                    const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${AppHelperService.generateLocalizedUrl('admin.edit_color_page', {id})}">Izmeni</a> ` : '';
                    const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-id="${id}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            id;
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