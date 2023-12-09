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
    }
}

export default PromotionDataTableEvents;
