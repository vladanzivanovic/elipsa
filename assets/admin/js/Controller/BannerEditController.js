import DropZoneService from "../../../js/Services/DropZoneService";
import BannerEditMapper from "../Mapper/BannerEditMapper";
import BannerHandler from "../Handler/BannerHandler";

class BannerEditController {
    constructor() {
        this.mapper = new BannerEditMapper();

        this.dropZone = DropZoneService();
        this.dropZone.init(this.mapper.form);

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES, 'banner');
        }

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.submitBtn.on('click touchend', e => {
            const handler = new BannerHandler();

            handler.save(this.mapper);
        });
    }
}

export default BannerEditController;