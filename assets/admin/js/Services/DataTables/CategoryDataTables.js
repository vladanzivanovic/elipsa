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
                url: Routing.generate('admin.get_category_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', title: 'Id' },
                { data: 'rs_name', title: 'Naziv - srpski' },
                { data: 'en_name', title: 'Naziv - engleski' },
                { data: 'parent', title: 'Glavna kategorija' },
                { data: 'show_home_page', title: 'Početna stranica', width: '200px', render: function (data, type, row, meta) {
                        const checkedAttr = data === true ? 'checked' : '';

                        let html = CAN_EDIT ? `<p class="status-text">&nbsp;</p><input type="checkbox" class="set-home-page" data-slug="${row.slug}" ${checkedAttr}/>` : '';

                        if (row.status === 3) {
                            html = `<p class="status-text">${data}</p>`;
                        }

                        return type === 'display' ? html : data;
                    } },
                { data: 'slug', render: function (data, type, row, meta) {
                    const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${AppHelperService.generateLocalizedUrl('admin.edit_category_page', {slug: data})}">Izmeni</a> ` : '';
                    const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-slug="${data}">Ukloni</button>` : '';

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