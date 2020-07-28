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
                url: Routing.generate('admin.get_career_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'full_name', name: 'full_name', title: 'Ime i prezime' },
                { data: 'position', name: 'position', title: 'Pozicija' },
                { data: 'applying_date', name: 'applying_date', title: 'Datum prijave' },
                { data: 'id', orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${Routing.generate('admin.view_career_details', {id: data})}">Pregled</a> ` : '';


                        return type === 'display' ?
                            editLink :
                            data;
                    } },
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