import toastrService from "../../../js/Services/ToastrService";

class ProductDataTableEvents {
    dataTable;
    toastr;

    constructor(dataTable) {
        this.dataTable = dataTable;
        this.toastr = toastrService;

        this.dataTable.init();
    }

    getDataTable()
    {
        return this.dataTable.getDataTable();
    }

    registerEvents()
    {
        this.dataTable.getDataTable()
            .on('search.dt', () => {
                this.toastr.showLoadingMessage();
            });

        this.dataTable.getDataTable()
            .on('draw', () => {
                this.toastr.remove();
            });
    }
}

export default ProductDataTableEvents;
