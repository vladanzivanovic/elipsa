import CoreDataTable from "./CoreDataTable";
import DataTableOptions from "./DataTableOptions";

require('./DataTableOptions');

class SliderTextDataTables {
    #coreDataTable;
    #dataTable = null;
    #dataTableOptionGenerator;

    constructor() {
        this.#coreDataTable = new CoreDataTable();
        this.#dataTableOptionGenerator = new DataTableOptions();
    }

    init() {
        const tableOptions = {
            ajax: {
                url: Routing.generate('admin.get_slider_text_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'title', name: 'description', title: 'Naslov' },
                { data: 'status_text', name: 'is_active', title: 'Status', width: '200px', render: function (data, type, row, meta) {
                        const checkedAttr = row.status === ENTITY_STATUSES.STATUS_ACTIVE ? 'checked' : '';
                        const text = Translator.trans(data, null, 'messages', LOCALE);

                        let html = CAN_EDIT ? `<p class="status-text text-uppercase">${text}</p><input type="checkbox" class="set-active-slider" data-id="${row.id}" ${checkedAttr}/>` : `<p class="status-text">${text}</p>`;

                        return type === 'display' ? html : data;
                    } },
                { data: 'position', name: 'position', title: 'Pozicija', width: '200px', render: function (data, type, row, meta) {
                        const text = Translator.trans(`banner_text.${data}`, null, 'messages', LOCALE);

                        return type === 'display' ? text : '';
                    } },
                { data: 'id', orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${Routing.generate('admin.edit_slider_text_page', {id: data})}">Izmeni</a> ` : '';
                        const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-id="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
            ],
        };

        const options = this.#dataTableOptionGenerator
            .setTableOptions(tableOptions)
            .setAvailableCountries(3)
            .getOptions();

        this.#coreDataTable
            .setTableOptions(options);

        this.#coreDataTable.setOrder(0, 'asc');

        this.#dataTable = this.#coreDataTable.renderTable();
    }

    reload() {
        this.#coreDataTable.table.DataTable().ajax.reload(null, false);
    }
}

export default SliderTextDataTables;

// export default (() => {
//     let Public = {},
//         Private = {};
//
//     Private.tableRef = $('#data-table');
//     Private.dataTable = null;
//
//     Public.init = () => {
//         const options = {
//             ajax: {
//                 url: Routing.generate('admin.get_slider_text_list'),
//                 type: 'POST'
//             },
//             columns: [
//                 { data: 'id', name: 'id', title: 'Id' },
//                 { data: 'description', name: 'description', title: 'Tekst' },
//                 { data: 'status_text', name: 'is_active', title: 'Status', width: '200px', render: function (data, type, row, meta) {
//                         const checkedAttr = row.is_active === true ? 'checked' : '';
//                         const text = Translator.trans(data, null, 'messages', LOCALE);
//
//                         let html = CAN_EDIT ? `<p class="status-text text-uppercase">${text}</p><input type="checkbox" class="set-active-slider" data-id="${row.id}" ${checkedAttr}/>` : `<p class="status-text">${text}</p>`;
//
//                         return type === 'display' ? html : data;
//                     } },
//                 { data: 'id', orderable: false, render: function (data, type, row, meta) {
//                         const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${Routing.generate('admin.edit_slider_text_page', {id: data})}">Izmeni</a> ` : '';
//                         const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-id="${data}">Ukloni</button>` : '';
//
//                         return type === 'display' ?
//                             editLink+removeButton :
//                             data;
//                     } },
//             ],
//             order: [[0, 'asc']],
//         };
//
//         this.coreDataTable.setTableOptions(options);
//
//         this.dataTable = this.coreDataTable.renderTable();
//
//
//
//         // const options = Object.assign({}, window.DATATABLE_OPTIONS, {
//         //     ajax: {
//         //         url: Routing.generate('admin.get_slider_text_list'),
//         //         type: 'POST'
//         //     },
//         //     columns: [
//         //         { data: 'id', name: 'id', title: 'Id' },
//         //         { data: 'description', name: 'description', title: 'Tekst' },
//         //         { data: 'status_text', name: 'is_active', title: 'Status', width: '200px', render: function (data, type, row, meta) {
//         //                 const checkedAttr = row.is_active === true ? 'checked' : '';
//         //                 const text = Translator.trans(data, null, 'messages', LOCALE);
//         //
//         //                 let html = CAN_EDIT ? `<p class="status-text text-uppercase">${text}</p><input type="checkbox" class="set-active-slider" data-id="${row.id}" ${checkedAttr}/>` : `<p class="status-text">${text}</p>`;
//         //
//         //                 return type === 'display' ? html : data;
//         //             } },
//         //         { data: 'id', orderable: false, render: function (data, type, row, meta) {
//         //                 const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${Routing.generate('admin.edit_slider_text_page', {id: data})}">Izmeni</a> ` : '';
//         //                 const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-id="${data}">Ukloni</button>` : '';
//         //
//         //                 return type === 'display' ?
//         //                     editLink+removeButton :
//         //                     data;
//         //             } },
//         //     ],
//         //     order: [[0, 'asc']],
//         // });
//
//         // Private.dataTable = Private.tableRef.DataTable(options);
//     };
//
//     Public.reload = () => {
//         this.coreDataTable.table.DataTable().ajax.reload(null, false);
//     };
//
//     return Public;
// });
