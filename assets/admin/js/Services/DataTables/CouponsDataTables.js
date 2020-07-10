import dt from 'datatables.net-dt';
import AppHelperService from "../../../../js/Helper/AppHelperService";
import dtrowreorder from 'datatables.net-rowreorder-bs4';

export default (() => {
    let Public = {},
        Private = {};

    Private.tableRef = $('#data-table');
    Private.dataTable = null;

    Public.init = () => {
        Private.dataTable = Private.tableRef.DataTable( {
            serverSide: true,
            ajax: {
                url: Routing.generate('admin.get_promotion_coupons_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'code', name: 'code', title: 'Kod' },
                { data: 'validFrom', name: 'validFrom', title: 'Važi od' },
                { data: 'validTo', name: 'validTo', title: 'Važi do' },
                { data: 'discount', name: 'discount', title: 'Popust', render: function (data, type){
                    return type === 'display' ?
                        `${data}%` :
                        data;
                    } },
                { data: 'id', orderable: false, render: function (data, type, row, meta) {
                    const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${AppHelperService.generateLocalizedUrl('admin.edit_coupon_page', {id: data})}">Izmeni</a> ` : '';
                    const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-id="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
            ],
            order: [[0, 'asc']],
            pageLength: 100,
        });
    };

    Public.reload = () => {
        Private.tableRef.DataTable().ajax.reload(null, false);
    };

    return Public;
});