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
                url: Routing.generate('admin.get_collaborator_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id',  title: 'Id' },
                { data: 'full_name', name: 'full_name', title: 'Ime i prezime' },
                { data: 'email', name: 'email', title: 'Email' },
                { data: 'phone', name: 'phone', title: 'Telefon' },
                { data: 'website', name: 'website', title: 'Websajt' },
                { data: 'has_store', name: 'has_store', title: 'Poseduje prodavnicu' },
                { data: 'location', name: 'location', title: 'Loakcija' },
                { data: 'shopping_mall', name: 'shopping_mall', title: 'Tržni centar' },
                { data: 'total_size', name: 'total_size', title: 'Kvadratura' },
                { data: 'no_floors', name: 'no_floors', title: 'Broj spratova' },
                { data: 'address', name: 'address', title: 'Adresa' },
                { data: 'city', name: 'city', title: 'Mesto' },
                { data: 'zip_code', name: 'zip_code', title: 'Poštanski broj' },
                { data: 'country', name: 'country', title: 'Država' },
                { data: 'presentation_doc', orderable: false, title: 'Prezentacija', render: function (data, type, row, meta) {
                    return type === 'display' && null != data ? `<a href="${data}">Prezentacija</a>` : '';
                    }},
                { data: 'plan_doc', orderable: false, title: 'Plan', render: function (data, type, row, meta) {
                        return type === 'display' && null != data ? `<a href="${data}">Plan</a>` : '';
                    }},
            ],
            order: [[0, 'asc']],
            pageLength: 100,
        });

        Private.registerEvents();
    };

    Public.reload = () => {
        Private.tableRef.DataTable().ajax.reload(null, false);
    };

    Private.registerEvents = () => {
        Private.dataTable.on('row-reorder', (e, diff, edit) => {
            let data = {};
            for(let i = 0; i < diff.length; i++) {
                let rowData = Private.dataTable.row( diff[i].node ).data();

                data[rowData.id] = {
                    'id': rowData.id,
                    'position': diff[i].newPosition + 1,
                };
            }

            $.ajax({
                type: 'POST',
                url: AppHelperService.generateLocalizedUrl('admin.set_sliders_position'),
                data: {'rows': JSON.stringify(data)},
                dataType: 'json',
                success: response => {
                    Public.reload();
                },
                error: error => {

                },
            })
        })
    };

    return Public;
});