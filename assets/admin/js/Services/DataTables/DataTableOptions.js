import dt from 'datatables.net-dt';
import 'datatables.net-select-bs4';

window.DATATABLE_OPTIONS = {
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Serbian_latin.json',
    },
    serverSide: true,
    pageLength: 100,
};


class DataTableOptions {
    #dataTableDefaultOptions;
    #tableOptions;

    constructor() {
        this.#dataTableDefaultOptions = {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Serbian_latin.json',
            },
            serverSide: true,
            pageLength: 100,
        };
    }

    setTableOptions(customOptions)
    {
        this.#tableOptions = Object.assign({}, this.#dataTableDefaultOptions, customOptions);

        return this;
    }

    withCheckBoxSelection()
    {
        this.#tableOptions = Object.assign(
            {},
            this.#tableOptions,
            {
                columnDefs: [
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    }
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
            }
        );

        return this;
    }

    withCheckBoxButtons()
    {
        if (!this.#tableOptions.buttons) {
            this.#tableOptions.buttons = [];
        }

        this.#tableOptions.buttons.push({extend: 'selectAll', text: 'Označi sve'});
        this.#tableOptions.buttons.push({extend: 'selectNone', text: 'Poništi označeno'});

        return this;
    }

    getOptions()
    {
        return this.#tableOptions;
    }
}

export default DataTableOptions;
