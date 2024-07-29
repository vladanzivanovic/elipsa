import 'jquery-ui-sortable-npm';
import productHomePageEvents from "../Event/ProductHomePageEvents";

class ProductHomePageController {
    constructor() {
        productHomePageEvents.registerEvents();
    }
}

export default ProductHomePageController;
