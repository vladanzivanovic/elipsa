import DropZoneService from "../../../js/Services/DropZoneService";
import catalogMapper from "../Mapper/CatalogMapper";
import CatalogHandler from "../Handler/CatalogHandler";

class CatalogController {
    constructor() {
        this.mapper = catalogMapper;
        this.handler = new CatalogHandler();

        this.dropZone = DropZoneService();
        this.dropZone.init(this.mapper.form);

        if (IMAGES.length > 0) {
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

export default CatalogController;