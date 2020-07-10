import DropZoneService from "../../../js/Services/DropZoneService";
import ProductEditMapper from "../Mapper/ProductEditMapper";
import ProductEditHandler from "../Handler/Product/ProductEditHandler";
import ProductDropZoneService from "../../../js/Services/ProductDropZoneService";
import productEditValidator from "../Validators/ProductEditValidator";
import Tipped from "@staaky/tipped";
require ('select2/dist/js/select2.full.min');


class ProductEditController {
    constructor() {
        this.mapper = new ProductEditMapper();
        this.dropZone = new ProductDropZoneService(DropZoneService());
        this.validator = productEditValidator;

        this.initializeForm();

        this.registerEvents();
    }

    initializeForm()
    {
        this.dropZone.init($('[data-files="mainImages"]'), {'colors': COLORS});
        this.initializeSelect();

        Tipped.create('.cleaning-icons');

        this.validator.validate(this.mapper.form);
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