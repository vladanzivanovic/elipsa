import dt from 'datatables.net-bs4';
import 'datatables.net-responsive-bs4';
import 'datatables.net-buttons-bs4';
import NotificationService from "../../../../js/NotificationService";

class CoreDataTable {
    constructor() {
        this.table = $('#data-table');
        this.notification = NotificationService();
        // this.displayInChildRow = IS_MOBILE ? 'none' : '';
        this.displayInChildRow = '';


        this.options = {
            // dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'bottom'p>",
            responsive: {
                details: {
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            let label = '';

                            if ('' !== col.title) {
                                label = `<label>${col.title}</label>`;
                            }

                            return col.hidden ?
                                `
                                    <li data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                        ${label}
                                        ${col.data}
                                    </li>
                                ` : '';
                        }).join('');

                        return data ?
                            $('<ul/>')
                                .addClass('details')
                                .append(data) :
                            false;
                    },
                },
            },
            language: {
                "emptyTable": "Nema podataka u tabeli",
                "info": "Prikaz _START_ do _END_ od ukupno _TOTAL_ zapisa",
                "infoEmpty": "Prikaz 0 do 0 od ukupno 0 zapisa",
                "infoFiltered": "(filtrirano od ukupno _MAX_ zapisa)",
                "infoThousands": ".",
                "lengthMenu": "Prikaži _MENU_ zapisa",
                "loadingRecords": "Učitavanje...",
                "processing": "Obrada...",
                "search": "Pretraga:",
                "zeroRecords": "Nisu pronađeni odgovarajući zapisi",
                "paginate": {
                    "first": "Početna",
                    "last": "Poslednja",
                    "next": "Sledeća",
                    "previous": "Prethodna"
                },
                "aria": {
                    "sortAscending": ": aktivirajte da sortirate kolonu uzlazno",
                    "sortDescending": ": aktivirajte da sortirate kolonu silazno"
                }
            },
            serverSide: true,
            order: [[0, 'asc']],
            pageLength: 100
        };
    }

    /**
     * @param {string} url
     */
    setLanguage(url) {
        this.options.language = {url};
    }

    /**
     * @param {number} length
     */
    setPageLength(length) {
        this.options.pageLength = length;
    }

    setTableOptions(options)
    {
        this.options = Object.assign({}, this.options, options);
    }

    /**
     * @param {number} index
     * @param {string} direction
     */
    setOrder(index, direction) {
        this.options.order = [[index, direction]];
    }

    setSearchPerColumn(dataTable)
    {
        $('thead tr', this.table)
            .clone(true)
            .addClass('filters')
            .appendTo('#data-table thead');

        dataTable.on('init.dt', function(e, settings) {
            const api = new $.fn.dataTable.Api( settings );

            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    const isSearchable = api.column(colIdx).settings()[0].aoColumns[colIdx].bSearchable;

                    let cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );

                    const title = $(cell).text();

                    if (true === isSearchable) {
                        $(cell).html('<input type="text" class="form-control" placeholder="' + title + '" />')
                            .removeClass('sorting')
                            .addClass('sorting_disabled')
                            .off('click')
                            .removeAttr('aria-controls')
                            .removeAttr('aria-sort');
                    } else {
                        $(cell).text('')
                            .removeClass('sorting')
                            .addClass('sorting_disabled')
                            .off('click')
                            .removeAttr('aria-controls')
                            .removeAttr('aria-sort');

                        return;
                    }

                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup')
                        .on('keyup', function (e) {
                            e.stopPropagation();

                            // Get the search value
                            // $(this).attr('title', $(this).val());

                            var cursorPosition = this.selectionStart;
                            // Search the column for that value
                            api
                                .column(colIdx)
                                .search(
                                    this.value,
                                    this.value != '',
                                    this.value == ''
                                )
                                .draw();

                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
                        });
                });
        })
    }

    /**
     * @returns {Object}
     */
    getOptions() {
        return this.options;
    }

    /**
     * @return {string}
     */
    getDisplayClassInChildRow()
    {
        return this.displayInChildRow;
    }

    renderTable()
    {
        const dataTableInstance = this.table.DataTable(this.options)
            .on('search.dt', () => {
                if (null !== dataTableInstance.context[0].jqXHR) {
                    dataTableInstance.context[0].jqXHR.abort();
                }

                this.notification.showLoadingMessage();
            })
            .on('draw', () => {
                this.notification.remove();
            });

        return dataTableInstance;
    }
}

export default CoreDataTable;
