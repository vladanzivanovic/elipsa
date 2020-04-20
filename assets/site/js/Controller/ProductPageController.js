require('@fancyapps/fancybox');

import ProductPageMapper from "../Mapper/ProductPageMapper";
import ProductPageService from "../Service/ProductPageService";

class ProductPageController {
    constructor() {
        this.mapper = ProductPageMapper;
        this.service = new ProductPageService();

        this.service.showImagesByColor($('.color-btn.active'));

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.color.on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.showImagesByColor($(e.currentTarget));
        });
    }
}

export default ProductPageController;