import DropZoneService from "../../../js/Services/DropZoneService";
import ProductEditMapper from "../Mapper/ProductEditMapper";
require ('select2/dist/js/select2.full.min');

class ProductEditController {
    constructor() {
        this.mapper = new ProductEditMapper();
        this.dropZone = DropZoneService();

        this.initializeForm();
    }

    initializeForm()
    {
        this.dropZone.init(this.mapper.form);
        this.initializeSelect();
    }

    initializeSelect() {
        this.mapper.tags.select2();
        this.mapper.category.select2();
        this.mapper.badge.select2();
        this.mapper.sizes.select2();
    }
}

export default ProductEditController;