import productDataTables from "../Services/DataTables/ProductDataTables";
import productListPageMapper from "../Mapper/ProductListPageMapper";
import FormHelperService from "../../../js/Helper/FormHelperService";
import toastrService from "../../../js/Services/ToastrService";

class ProductListFilterHandler {
    #dataTable;
    #mapper;
    #toastr;

    constructor() {
        if (!ProductListFilterHandler.instance) {
            this.#dataTable = productDataTables;
            this.#mapper = productListPageMapper;
            this.#toastr = toastrService;

            ProductListFilterHandler.instance = this;
        }

        return ProductListFilterHandler.instance;
    }

    search()
    {
        const data = FormHelperService.formToJson($(this.#mapper.form));

        if (data.home_page_show === '-1') {
            delete data.home_page_show;
        }

        this.#toastr.showLoadingMessage();

        this.#dataTable.getDataTable()
            .search(JSON.stringify(data))
            .draw();
    }
}

const productListFilterHandler = new ProductListFilterHandler();

Object.freeze(productListFilterHandler);

export default productListFilterHandler;
