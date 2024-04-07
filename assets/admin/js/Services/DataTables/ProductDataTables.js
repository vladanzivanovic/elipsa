import DataTableOptions from "./DataTableOptions";

require('./DataTableOptions');

class ProductDataTables {
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
        const route = Routing.generate('admin.get_product_list');

        const tableOptions = {
            ajax: {
                url: route,
                type: 'POST'
            },
            dom:'<"row" <"col-md-3" B><"col-md-6 action-list"><"col-md-3" l>>rt<"row" <"col-md-6" i><"col-md-6" p>>',
            columns: [
                { data: null, name: 'code', title: '', defaultContent: '' },
                { data: 'code', name: 'code', title: 'Šifra' },
                { data: 'title', name: 'title', title: 'Naziv', width: '200px', render: function (title, type, row, meta) {
                        let html = `<a href="${row.link}" target="_blank">${title}</a>`;

                        return type === 'display' ? html : title;
                    } },
                { data: 'price', name: 'price', title: 'Cena', type: "num" },
                { data: 'discount', name: 'discount', title: 'Popust', type: "num" },
                { data: 'sizes', name: 'sizes', title: 'Veličine' },
                { data: 'status_text', name: 'status', title: 'Status', width: '200px', render: function (data, type, row, meta) {
                        const buttonText = row.status === PRODUCT_CONSTANTS.STATUS_ACTIVE ? 'Deaktiviraj' : 'Aktiviraj';
                        const newStatus = row.status !== PRODUCT_CONSTANTS.STATUS_ACTIVE ? PRODUCT_CONSTANTS.STATUS_ACTIVE : PRODUCT_CONSTANTS.STATUS_PENDING;

                        let html = CAN_EDIT ? `
                            <p class="status-text">${data}</p>
                            <button type="button" class="btn btn-outline-warning change-product-status" data-slug="${row.slug}" data-status="${newStatus}">${buttonText}</button>` :
                            `<p class="status-text">${data}</p>`;

                        return type === 'display' ? html : data;
                    }
                },
                {
                    data: 'position_text',
                    name: 'show_home_page',
                    title: 'Početna stranica',
                    width: '200px',
                    render: function (data, type, row, meta) {
                        let html = '';

                        const upChecked = row.show_home_page === PRODUCT_CONSTANTS.HOME_PAGE_UP ? 'checked' : '';
                        const downChecked = row.show_home_page === PRODUCT_CONSTANTS.HOME_PAGE_DOWN ? 'checked' : '';

                        html = CAN_EDIT ?
                            `<div class="row">
                                <div class="col-md-3">
                                    <p class="status-text">Gore</p><input type="checkbox" value="${PRODUCT_CONSTANTS.HOME_PAGE_UP}" class="set-home-page" data-slug="${row.slug}" ${upChecked}/>
                                </div>
                                <div class="col-md-3">
                                    <p class="status-text">Dole</p><input type="checkbox" value="${PRODUCT_CONSTANTS.HOME_PAGE_DOWN}" class="set-home-page" data-slug="${row.slug}" ${downChecked}/>
                                </div>
                            </div>
                            ` :
                            `<p class="status-text d-block letter-capitalize">${Translator.trans(data, null, 'messages', LOCALE)}</p>`;

                        return type === 'display' ? html : data;
                    }
                },
                {
                    data: 'is_sold',
                    name: 'is_sold',
                    title: 'Rasprodato',
                    width: '200px',
                    render: function (data, type, row, meta) {
                        let html = '';

                        const isSold = data ? 'checked' : '';

                        html = CAN_EDIT ?
                            `<div class="row">
                                <div class="col-md-3">
                                    <input type="checkbox" value="1" class="toggle-product-is-sold" data-slug="${row.slug}" ${isSold}/>
                                </div>
                            </div>
                            ` :
                            `<p class="status-text d-block letter-capitalize">${Translator.trans(data, null, 'messages', LOCALE)}</p>`;

                        return type === 'display' ? html : data;
                    }
                },
                { data: 'slug', searchable: false, orderable: false, render: function (data, type, row, meta) {
                        const editLink = CAN_EDIT ? `<a class="btn btn-link" target="_blank" href="${Routing.generate('admin.edit_product_page', {slug: data})}">Izmeni</a> ` : '';
                        const removeButton = CAN_REMOVE ?`<button class="btn btn-danger remove-item-button" data-alias="${data}" data-title="${row.title}">Ukloni</button>` : '';

                        return type === 'display' ?
                            editLink+removeButton :
                            data;
                    } },
            ],
            order: [[1, 'desc']],
            initComplete: () => {
                this.#generateActionBox().appendTo('.action-list');
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: 'Select all',
                            action: function () {
                                table.rows().select();
                            }
                        },
                        {
                            text: 'Select none',
                            action: function () {
                                table.rows().deselect();
                            }
                        }
                    ]
                }
            },
            select: true
        };

        const options = this.#dataTableOptionGenerator
            .setTableOptions(tableOptions)
            .withCheckBoxSelection()
            .withCheckBoxButtons()
            .getOptions();

        this.#dataTable = $(this.#tableRef).DataTable(options);
    }

    #generateActionBox()
    {
        let dom = $('<select class="action-box"></select>');

        dom.append('<option value="">Izaberite akciju...</option>');
        dom.append(`<option value="0" data-action-type="home_page_status">Početna strana - ukloni</option>`);
        dom.append(`<option value="${PRODUCT_CONSTANTS.HOME_PAGE_UP}" data-action-type="home_page_status">Početna strana - gore</option>`);
        dom.append(`<option value="${PRODUCT_CONSTANTS.HOME_PAGE_DOWN}" data-action-type="home_page_status">Početna strana - dole</option>`);
        dom.append(`<option value="discount-modal" data-action-type="set_bulk_discount">Popust na izabranim proizvodima</option>`);

        return dom;
    }
}
const productDataTables = new ProductDataTables();

export default productDataTables;
