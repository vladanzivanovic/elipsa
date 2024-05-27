import DataTableOptions from "./DataTableOptions";

require('./DataTableOptions');

class BlogDataTables {
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
        const route = Routing.generate('admin.blog_list');

        const tableOptions = {
            ajax: {
                url: route,
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id', title: 'Id' },
                { data: 'title', name: 'title', title: 'Naslov' },
                { data: 'status_text', name: 'status', title: 'Status', width: '200px', render: function (data, type, row, meta) {
                        const checkedAttr = row.status === 1 ? 'checked' : '';

                        let html = CAN_EDIT ? `<p class="status-text">${Translator.trans(data, null, 'messages', LOCALE)}</p><input type="checkbox" class="set-active-blog" data-id="${row.id}" ${checkedAttr}/>` : `<p class="status-text">${Translator.trans(data, null, 'messages', LOCALE)}</p>`;
                        return type === 'display' ? html : data;
                    } },
                { data: 'id', orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-link" href="${Routing.generate('admin.edit_blog_page', {id: data})}">Izmeni</a> ` : '';
                        const removeButton = CAN_REMOVE ?`<button class="btn btn-danger remove-item-button" data-id="${data}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
            ],
            order: [[0, 'desc']],
        }

        const options = this.#dataTableOptionGenerator
            .setTableOptions(tableOptions)
            .setAvailableCountries(3)
            .getOptions();

        this.#dataTable = $(this.#tableRef).DataTable(options);
    }
}

const blogDataTables = new BlogDataTables();

export default blogDataTables;
