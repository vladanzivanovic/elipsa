import DataTableOptions from "./DataTableOptions";
import AppHelperService from "../../../../js/Helper/AppHelperService";


// require('./DataTableOptions');


class PromotionDataTables {
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
        const route =  Routing.generate('admin.get_promotions_list');

        const tableOptions = {
            ajax: {
                url: route,
                type: 'POST'
            },
            dom:'<"row" <"col-md-3" l><"col-md-6 action-list"><"col-md-3" f>>rtip',
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'code', name: 'code', title: 'Kod' },
                { data: 'type_text', name: 'type', title: 'Tip' },
                { data: 'validFrom', name: 'validFrom', title: 'Važi od' },
                { data: 'validTo', name: 'validTo', title: 'Važi do' },
                { data: 'discount', name: 'discount', title: 'Popust', render: function (data, type){
                        return type === 'display' ?
                            `${data}%` :
                            data;
                    } },
                { data: 'id', orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${AppHelperService.generateLocalizedUrl('admin.edit_promotions_page', {id: data})}">Izmeni</a> ` : '';
                        const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-id="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
            ],
            order: [[0, 'asc']],
            initComplete: () => {
                this.#generateActionBox().appendTo('.action-list');
            }
        };

        const options = this.#dataTableOptionGenerator
            .setTableOptions(tableOptions)
            .getOptions();

        this.#dataTable = $(this.#tableRef).DataTable(options);
    }

    #generateActionBox()
    {
        let dom = $('<select class="action-box"></select>');

        dom.append('<option value="">Izaberite akciju...</option>');

        for (let typeProp in PROMOTION_TYPES) {
            dom.append(`<option value="${PROMOTION_TYPES[typeProp]}" data-action-type="promotion_type">${Translator.trans(`promotion.type.${PROMOTION_TYPES[typeProp]}`)}</option>`);
        }

        return dom;
    }
}

const promotionDataTables = new PromotionDataTables();

export default promotionDataTables;
