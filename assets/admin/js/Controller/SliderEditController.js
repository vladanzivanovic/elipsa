import DropZoneService from "../../../js/Services/DropZoneService";
import sliderEditMapper from "../Mapper/SliderEditMapper";
import SliderHandler from "../Handler/SliderHandler";
import sliderEditValidator from "../Validators/SliderEditValidator";
import SummerNote from "../Services/SummerNote";
import countrySelectionEvents from "../Event/CountrySelectionEvents";

class SliderEditController {
    #mapper;
    #countrySelectionEvents;

    constructor() {
        this.#mapper = sliderEditMapper;
        this.validator = sliderEditValidator;
        this.#countrySelectionEvents = countrySelectionEvents;
        this.summernote = new SummerNote();

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            this.summernote.initialize(
                $(this.#mapper[`description_${locale}`]),
                this.summernote.createCallBacksSummernote($(this.#mapper[`description_${locale}`]), 'slider')
            );
        }

        $('.dropdown-toggle').dropdown();

        this.dropZone = DropZoneService();
        this.dropZoneMobile = DropZoneService();
        this.dropZone.init($('[data-files="slider"]'));
        this.dropZoneMobile.init($('[data-files="slider_mobile"]'));

        $(`${this.#mapper.form} select`).select2({
            minimumResultsForSearch: -1
        });

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES.desktop, 'slider');
            if (IMAGES.mobile) {
                this.dropZoneMobile.setFiles(IMAGES.mobile, 'slider_mobile');
            }
        }

        this.validator.validate($(this.#mapper.form));

        this.registerEvents();
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click touchend', e => {
            const handler = new SliderHandler();

            handler.save(this.#mapper);
        });

        this.#countrySelectionEvents.registerEvents();
    }
}

export default SliderEditController;
