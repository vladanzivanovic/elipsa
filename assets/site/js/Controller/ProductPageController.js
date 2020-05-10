import ProductPageHandler from "../Handler/ProductPageHandler";

require('@fancyapps/fancybox');

import ProductPageMapper from "../Mapper/ProductPageMapper";
import ProductPageService from "../Service/ProductPageService";

class ProductPageController {
    constructor() {
        this.mapper = ProductPageMapper;
        this.service = new ProductPageService();
        this.handler = new ProductPageHandler();

        this.service.showImagesByColor($('.color-btn.active'));

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.color.on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.showImagesByColor($(e.currentTarget));
        });

        this.mapper.size.on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.toggleActiveSize(e.currentTarget);
        });

        this.mapper.addBtn.on('click touchend', e => {
            this.handler.save();
        });
    }
}

export default ProductPageController;