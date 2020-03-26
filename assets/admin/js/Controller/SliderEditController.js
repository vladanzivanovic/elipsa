import DropZoneService from "../../../js/Services/DropZoneService";
import SliderEditMapper from "../Mapper/SliderEditMapper";
import SliderHandler from "../Handler/SliderHandler";

class SliderEditController {
    constructor() {
        this.mapper = new SliderEditMapper();

        this.dropZone = DropZoneService();
        this.dropZone.init(this.mapper.form);

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES, 'slider');
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