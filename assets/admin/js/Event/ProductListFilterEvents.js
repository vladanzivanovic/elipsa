import productDataTables from "../Services/DataTables/ProductDataTables";
import toastrService from "../../../js/Services/ToastrService";
import productListPageMapper from "../Mapper/ProductListPageMapper";
import productListFilterHandler from "../Handler/ProductListFilterHandler";

class ProductListFilterEvents {
    #dataTable;
    #toastr;
    #mapper;
    #filterHandler;

    constructor() {
        if (!ProductListFilterEvents.instance) {
            this.#dataTable = productDataTables;
            this.#toastr = toastrService;
            this.#mapper = productListPageMapper;
            this.#filterHandler = productListFilterHandler;

            ProductListFilterEvents.instance = this;
        }

        return ProductListFilterEvents.instance;
    }

    registerEvents()
    {
        $(this.#mapper.submitBtn).on('click', e => {
            this.#filterHandler.search();
        });

        $(this.#mapper.resetBtn).on('click', e => {
            $(this.#mapper.form)[0].reset();
            $(this.#mapper.filter.categories).val(null).trigger('change');
            $(this.#mapper.filter.tags).val(null).trigger('change');
            $(this.#mapper.filter.homePageShow).val('-1').trigger('change');
            $(this.#mapper.filter.productStatus).val(`${PRODUCT_CONSTANTS.STATUS_ACTIVE},${PRODUCT_CONSTANTS.STATUS_PENDING}`).trigger('change');

            this.#filterHandler.search();
        });
    }
}

const productListFilterEvents = new ProductListFilterEvents();

Object.freeze(productListFilterEvents);

export default productListFilterEvents;
