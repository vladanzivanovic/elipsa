import DropZoneService from "../../../js/Services/DropZoneService";
import ProductEditMapper from "../Mapper/ProductEditMapper";
import ProductEditHandler from "../Handler/Product/ProductEditHandler";
import ProductDropZoneService from "../../../js/Services/ProductDropZoneService";
require ('select2/dist/js/select2.full.min');

class ProductEditController {
    constructor() {
        this.mapper = new ProductEditMapper();
        this.dropZone = new ProductDropZoneService(DropZoneService());

        this.initializeForm();

        this.registerEvents();
    }

    initializeForm()
    {
        this.dropZone.init($('[data-files="mainImages"]'), {'colors': COLORS});
        this.initializeSelect();
    }

    initializeSelect() {
        this.mapper.tags.select2();
        this.mapper.category.select2();
        this.mapper.badge.select2();
        this.mapper.sizes.select2();

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES, 'mainImages');
        }
    }

    registerEvents() {
        this.mapper.submitBtn.on('click touchend', e => {
            const handler = new ProductEditHandler();

            handler.save(this.mapper);
        })
    }
}

export default ProductEditController;