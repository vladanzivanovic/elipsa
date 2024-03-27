import toastrService from "../../../js/Services/ToastrService";
import promotionDataTables from "../Services/DataTables/PromotionDataTables";

class PromotionDataTableEvents {
    #dataTable;
    #toastr;

    constructor() {
        this.#dataTable = promotionDataTables;
        this.#toastr = toastrService;

        this.#dataTable.init();
    }

    registerEvents()
    {
        this.#dataTable.getDataTable()
            .on('search.dt', () => {
                this.#toastr.showLoadingMessage();
            });

        this.#dataTable.getDataTable()
            .on('draw', () => {
                this.#toastr.remove();
            });

        $(document).on('change', '.action-box', e => {
            const value = e.currentTarget.value;

            if ('' === value) {
                this.#dataTable.getDataTable()
                    .draw();
            }

            this.#dataTable.getDataTable()
                .columns(2)
                .search(value)
                .draw();
        })
    }
}

export default PromotionDataTableEvents;
