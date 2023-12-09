import ProductEditHandler from "../Handler/Product/ProductEditHandler";
import productListPageFilter from "../Filter/ProductListPageFilter";
import ProductDataTableEvents from "../Event/ProductDataTableEvents";
import productListFilterEvents from "../Event/ProductListFilterEvents";
import productListModal from "../Dom/ProductListModal";

class DashboardController {
    #filter;
    #productDataTable;
    #filterEvents;
    #productEditHandler;
    #productModal;

    constructor() {
        this.#filter = productListPageFilter;

        if (CAN_VIEW) {
            this.#productDataTable = new ProductDataTableEvents();
            this.#filterEvents = productListFilterEvents;
            this.#productEditHandler = new ProductEditHandler();
            this.#productModal = productListModal;

            this.#productDataTable.registerEvents();
            this.#filterEvents.registerEvents();
        }

        this.#registerEvents();
    }

    #registerEvents()
    {
        $(document).on('click', '.remove-item-button', e => {
            const productSlug = e.currentTarget.dataset.alias;
            const productTitle = e.currentTarget.dataset.title;

            this.#productModal.removeProduct(productSlug, productTitle);
        });

        $(document).on('click', '.change-product-status', e => {
            const slug = e.currentTarget.dataset.slug;
            const status = e.currentTarget.dataset.status;

            this.#productEditHandler.changeStatus(slug, status);
        });

        $(document).on('change', '.set-home-page', e => {
            const slug = e.currentTarget.dataset.slug;
            const value = e.currentTarget.value;
            const status = e.currentTarget.checked ? value  : 0;

            for (const checkbox of $('.set-home-page', $(e.currentTarget).parent().parent())) {
                if (value !== $(checkbox).val()) {
                    $(checkbox).prop('checked', false);
                }
            }

            this.#productEditHandler.changeHomePagePosition($(e.currentTarget), slug, status);
        });

        $(document).on('click', '.remove-product', e => {
            const alias = e.currentTarget.dataset.alias;

            this.#productEditHandler.remove(alias);
        });
    }
}

export default DashboardController;
