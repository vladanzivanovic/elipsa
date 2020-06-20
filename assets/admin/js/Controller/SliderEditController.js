import DropZoneService from "../../../js/Services/DropZoneService";
import SliderEditMapper from "../Mapper/SliderEditMapper";
import SliderHandler from "../Handler/SliderHandler";

class SliderEditController {
    constructor() {
        this.mapper = new SliderEditMapper();

        this.dropZone = DropZoneService();
        this.dropZoneMobile = DropZoneService();
        this.dropZone.init($('[data-files="slider"]'));
        this.dropZoneMobile.init($('[data-files="slider_mobile"]'));

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES.desktop, 'slider');
            if (IMAGES.mobile) {
                this.dropZoneMobile.setFiles(IMAGES.mobile, 'slider_mobile');
            }
        }

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.submitBtn.on('click touchend', e => {
            const handler = new SliderHandler();

            handler.save(this.mapper);
        });
    }
}

export default SliderEditController;