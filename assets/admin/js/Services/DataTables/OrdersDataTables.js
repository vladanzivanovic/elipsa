require('./DataTableOptions');
import AppHelperService from "../../../../js/Helper/AppHelperService";
import dtrowreorder from 'datatables.net-rowreorder-bs4';

export default (() => {
    let Public = {},
        Private = {};

    Private.tableRef = $('#data-table');
    Private.dataTable = null;

    Public.init = () => {
        const options = Object.assign({}, window.DATATABLE_OPTIONS, {
            ajax: {
                url: Routing.generate('admin.get_order_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'completed_at', name: 'completed_at', title: 'Datum naručivanja' },
                { data: 'full_name', name: 'full_name', title: 'Ime i prezime' },
                { data: 'email', name: 'email', title: 'Email' },
                { data: 'payment_type', name: 'payment_type', title: 'Tip plaćanja', render: function (payment_type, type, row, meta) {
                        return Translator.trans(payment_type, null, 'messages', LOCALE);
                    } },
                { data: 'status', name: 'status', title: 'Status', render: function (status, type, row, meta) {
                        return Translator.trans(status, null, 'messages', LOCALE);
                    } },
                { data: 'country', name: 'country_code', title: 'Zamlja naručivanja'},
                { data: 'visited', name: 'visited', title: 'Nova porudžbina', render: function (visited, type, row, meta) {
                        return true === visited ? 'Pregledana' : ' Nije pregledana';
                    } },
                { data: 'token', render: function (token, type, row, meta) {
                        const viewLink = CAN_VIEW ? `<a class="btn btn-outline-primary" href="${Routing.generate('admin.view_single_order', {token})}">Pregled</a> ` : '';

                        return type === 'display' ?
                            viewLink : token;
                    } },
            ],
            order: [[1, 'desc']],
        });

        Private.dataTable = Private.tableRef.DataTable(options);
    };

    Public.reload = () => {
        Private.tableRef.DataTable().ajax.reload(null, false);
    };

    return Public;
});
