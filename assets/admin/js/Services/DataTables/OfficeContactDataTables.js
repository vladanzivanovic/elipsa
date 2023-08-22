import CoreDataTable from "./CoreDataTable";

require('./DataTableOptions');

class OfficeContactDataTables {
    #coreDataTable;
    #dataTable = null;

    constructor() {
        if(!OfficeContactDataTables.instance) {
            this.#coreDataTable = new CoreDataTable();

            OfficeContactDataTables.instance = this;
        }

        return OfficeContactDataTables.instance;
    }

    init() {
        const options = {
            ajax: {
                url: Routing.generate('admin.get_office_contact_list'),
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'title', name: 'title', title: 'Naziv' },
                { data: 'telephone', name: 'telephone', title: 'Broj telefona' },
                { data: 'isShownInFooter', name: 'isShownInFooter', title: 'Footer', width: '200px', render: function (data, type, row, meta) {
                        const text = true === data ? 'Da' : 'Ne';

                        return type === 'display' ? text : '';
                    } },
                { data: 'id', orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-outline-primary" href="${Routing.generate('admin.edit_office_contact_page', {id: data})}">Izmeni</a> ` : '';
                        const removeButton = CAN_REMOVE ?`<button class="btn btn-outline-danger remove-item-button" data-id="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
            ],
            order: [[0, 'asc']],
        };

        this.#coreDataTable.setTableOptions(options);

        this.#dataTable = this.#coreDataTable.renderTable();
    }

    reload() {
        this.#coreDataTable.table.DataTable().ajax.reload(null, false);
    }
}

const officeContactDataTables = new OfficeContactDataTables();

Object.freeze(officeContactDataTables);

export default officeContactDataTables;
