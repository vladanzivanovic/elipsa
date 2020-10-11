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
                url: Routing.generate('admin.get_order_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'full_name', name: 'full_name', title: 'Ime i prezime' },
                { data: 'email', name: 'email', title: 'Email' },
                { data: 'payment_type', name: 'payment_type', title: 'Tip plaćanja', render: function (payment_type, type, row, meta) {
                    return Translator.trans(payment_type, null, 'messages', LOCALE);
                    } },
                { data: 'status', name: 'status', title: 'Status', render: function (status, type, row, meta) {
                        return Translator.trans(status, null, 'messages', LOCALE);
                    } },
                { data: 'id', render: function (id, type, row, meta) {
                        const viewLink = CAN_VIEW ? `<a class="btn btn-outline-primary" href="${Routing.generate('admin.view_single_order', {id})}">Pregled</a> ` : '';

                        return type === 'display' ?
                            viewLink : id;
                    } },
            ],
            order: [[0, 'DESC']],
            pageLength: 100,
        });
    };

    Public.reload = () => {
        Private.tableRef.DataTable().ajax.reload(null, false);
    };

    return Public;
});