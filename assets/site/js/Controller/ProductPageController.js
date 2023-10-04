import productPageEvents from "../Events/ProductPageEvents";
import productPageService from "../Service/ProductPageService";

require('@fancyapps/fancybox');

class ProductPageController {
    #productPageEvents;
    #productPageService;

    constructor() {
        this.#productPageService = productPageService;
        this.#productPageEvents = productPageEvents;

        this.#productPageService.init();

        this.#productPageEvents.registerEvents();
    }
}

export default ProductPageController;
