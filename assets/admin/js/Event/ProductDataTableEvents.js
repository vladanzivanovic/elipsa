import toastrService from "../../../js/Services/ToastrService";
import productDataTables from "../Services/DataTables/ProductDataTables";
import productBulkApiHandler from "../Handler/Product/ProductBulkApiHandler";
import productListPageMapper from "../Mapper/ProductListPageMapper";
import productListModal from "../Dom/ProductListModal";

class ProductDataTableEvents {
    #dataTable;
    #toastr;
    #bulkProductHandler;
    #mapper;
    #productModal;

    constructor() {
        this.#dataTable = productDataTables;
        this.#toastr = toastrService;
        this.#bulkProductHandler = productBulkApiHandler;
        this.#mapper = productListPageMapper;
        this.#productModal = productListModal;

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
            const position = e.currentTarget.value;
            const type = $(`.action-box option:selected`).data('actionType');

            if ('' === position) {
                return;
            }

            this.#productModal.actionBox({position}, type);
        })

        $(document).on('click', '.home-page-status-apply', async e => {
            const rows = this.#dataTable.getDataTable()
                .rows({ selected: true })
                .data();
            const productIds = [];

            for (const rowKey in rows) {
                productIds.push(rows[rowKey].id);
            }

            this.#toastr.showLoadingMessage();

            await this.#bulkProductHandler.changeProductsHomePositions(productIds, $(e.currentTarget).data('position'));

            $(this.#mapper.submitBtn).trigger('click');

            $(this.#mapper.dataTable.actionBox).val(null);
        })

        $(document).on('click', '.discount-apply', async e => {
            const rows = this.#dataTable.getDataTable()
                .rows({ selected: true })
                .data();
            const productIds = [];
            const discount = parseInt($('#discount-input').val());

            for (const rowKey in rows) {
                productIds.push(rows[rowKey].id);
            }

            this.#toastr.showLoadingMessage();

            await this.#bulkProductHandler.setProductsDiscount(productIds, discount);

            $(this.#mapper.submitBtn).trigger('click');

            $(this.#mapper.dataTable.actionBox).val(null);
        });
    }
}

export default ProductDataTableEvents;
