import DropZoneService from "../../../js/Services/DropZoneService";
import catalogMapper from "../Mapper/CatalogMapper";
import CatalogHandler from "../Handler/CatalogHandler";

class CatalogEditController {
    constructor() {
        this.mapper = catalogMapper;
        this.handler = new CatalogHandler();

        this.dropZone = DropZoneService();
        this.dropZone.init($('[data-files="catalog"]'));

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES, 'catalog');
        }

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.submitBtn).on('click touchend', e => {
            this.handler.save();
        });
    }
}

export default CatalogEditController;