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
                // { data: 'price', name: 'price', title: 'Cena', type: "num" },
                // { data: 'discount', name: 'discount', title: 'Popust', type: "num" },
                // { data: 'sizes', name: 'sizes', title: 'Veličine', render: function (sizes, type, row) {
                //         let html = '';
                //
                //         for (const countryCode in sizes) {
                //             const size = sizes[countryCode];
                //
                //             html += `<p><label style="font-weight: bold">${countryCode}</label> - ${size}</p>`;
                //         }
                //
                //         return html;
                //     }
                // },
                { data: 'prices', name: 'prices', title: 'Cena', render: function (prices, type, row) {
                        let html = '';

                        for (const countryCode in prices) {
                            const countryName = Translator.trans(COUNTRIES[countryCode].translation, null, 'messages', LOCALE);

                            html +=
                                `<div class="row">
                                    <div class="col-md-12">
                                        <p class="status-text">${countryName} - ${prices[countryCode]}</p>
                                    </div>
                                </div>
                            `;
                        }

                        return html;
                    }
                },
                { data: 'discounts', name: 'discounts', title: 'Popust', render: function (discounts, type, row) {
                        let html = '';

                        for (const countryCode in discounts) {
                            const countryName = Translator.trans(COUNTRIES[countryCode].translation, null, 'messages', LOCALE);

                            html +=
                                `<div class="row">
                                    <div class="col-md-12">
                                        <p class="status-text">${countryName} - ${discounts[countryCode]}</p>
                                    </div>
                                </div>
                            `;
                        }

                        return html;
                    }
                },
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
                    data: 'sold',
                    name: 'is_sold',
                    title: 'Rasprodato',
                    width: '200px',
                    render: function (sold, type, row, meta) {
                        let html = '<div class="row">';

                        for (const countryCode in sold) {
                            const isSold = true === sold[countryCode] ? 'Da' : 'Ne';
                            const countryName = Translator.trans(COUNTRIES[countryCode].translation, null, 'messages', LOCALE)

                            html += `<div class="col-md-12">
                                    <p>${countryName} - ${isSold}</p>
                                </div>`;
                        }

                        html += '</div>';

                        return html;
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
        dom.append(`<option value="0" data-action-type="home_page_status" data-country="rs">Početna strana - ukloni - RS</option>`);
        dom.append(`<option value="0" data-action-type="home_page_status" data-country="ba">Početna strana - ukloni - BiH</option>`);
        dom.append(`<option value="${PRODUCT_OPTIONS_CONSTANTS.HOME_PAGE_UP}" data-action-type="home_page_status" data-country="rs">Početna strana - gore - RS</option>`);
        dom.append(`<option value="${PRODUCT_OPTIONS_CONSTANTS.HOME_PAGE_DOWN}" data-action-type="home_page_status" data-country="rs">Početna strana - dole - RS</option>`);
        dom.append(`<option value="${PRODUCT_OPTIONS_CONSTANTS.HOME_PAGE_UP}" data-action-type="home_page_status" data-country="ba">Početna strana - gore - BiH</option>`);
        dom.append(`<option value="${PRODUCT_OPTIONS_CONSTANTS.HOME_PAGE_DOWN}" data-action-type="home_page_status" data-country="ba">Početna strana - dole - BiH</option>`);
        dom.append(`<option value="discount-modal" data-action-type="set_bulk_discount">Popust na izabranim proizvodima</option>`);

        return dom;
    }
}
const productDataTables = new ProductDataTables();

export default productDataTables;
