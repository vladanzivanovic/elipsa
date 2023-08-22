import DropZoneService from "../../../js/Services/DropZoneService";
import sliderEditMapper from "../Mapper/SliderEditMapper";
import SliderHandler from "../Handler/SliderHandler";
import sliderEditValidator from "../Validators/SliderEditValidator";
import SummerNote from "../Services/SummerNote";

class SliderEditController {
    constructor() {
        this.mapper = sliderEditMapper;
        this.validator = sliderEditValidator;
        this.summernote = new SummerNote();

        this.summernote.initialize(
            this.mapper.descriptionRs,
            this.summernote.createCallBacksSummernote(this.mapper.descriptionRs, 'slider')
        );

        this.summernote.initialize(
            this.mapper.descriptionEn,
            this.summernote.createCallBacksSummernote(this.mapper.descriptionEn, 'slider')
        );

        $('.dropdown-toggle').dropdown();

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

        this.validator.validate(this.mapper.form);

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
