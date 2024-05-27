import DataTableOptions from "./DataTableOptions";

require('./DataTableOptions');
import AppHelperService from "../../../../js/Helper/AppHelperService";
import dtrowreorder from 'datatables.net-rowreorder-bs4';

class SliderDataTables {
    #dataTable;
    #tableRef = '#data-table';
    #dataTableOptionGenerator;

    constructor() {
        this.#dataTableOptionGenerator = new DataTableOptions();
    }

    reload()
    {
        this.#dataTable.ajax.reload(null, false);
    }

    getDataTable()
    {
        return this.#dataTable;
    }


    init()
    {
        const route = Routing.generate('admin.get_slider_list');

        const tableOptions = {
            ajax: {
                url: route,
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'image', orderable: false, title: 'Slika', width: '200px', render: function (data, type, row, meta) {
                        const image = `<img src="${data}" class="slider-table-image">`

                        return type === 'display' ?
                            image :
                            data;
                    } },
                { data: 'position', name: 'position', title: 'Pozicija' },
                { data: 'status_text', name: 'is_active', title: 'Status', width: '200px', render: function (data, type, row, meta) {
                        const checkedAttr = row.is_active === true ? 'checked' : '';
                        const text = Translator.trans(data, null, 'messages', LOCALE);

                        let html = CAN_EDIT ? `<p class="status-text text-uppercase">${text}</p><input type="checkbox" class="set-active-slider" data-id="${row.id}" ${checkedAttr}/>` : `<p class="status-text">${text}</p>`;

                        return type === 'display' ? html : data;
                    } },
                { data: 'id', orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${AppHelperService.generateLocalizedUrl('admin.edit_slider_page', {id: data})}">Izmeni</a> ` : '';
                        const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-id="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
            ],
            order: [[2, 'asc']],
            rowReorder: {
                dataSrc: 'id',
                update: false,
            }
        }

        const options = this.#dataTableOptionGenerator
            .setTableOptions(tableOptions)
            .setAvailableCountries(3)
            .getOptions();

        this.#dataTable = $(this.#tableRef).DataTable(options);
    }
}

const sliderDataTables = new SliderDataTables();

export default sliderDataTables;
