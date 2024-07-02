import SummerNote from "../Services/SummerNote";
import DropZone from "../../../js/Services/DropZoneService";
import JobsHandler from "../Handler/JobsHandler";
import jobEditMapper from "../Mapper/JobEditMapper";
import jobEditValidator from "../Validators/JobEditValidator";
import countrySelectionEvents from "../Event/CountrySelectionEvents";
import baseEvents from "./BaseEvents";
require ('select2/dist/js/select2.full.min');

class JobEditController {
    #baseEvents;
    #mapper;
    #validator;
    #dropZone;
    #countrySelectionEvents;
    #summernote;
    
    constructor() {
        this.#baseEvents = baseEvents;
        this.#mapper = jobEditMapper;
        this.#validator = jobEditValidator;
        this.#dropZone = DropZone($(this.#mapper.form));
        this.#countrySelectionEvents = countrySelectionEvents;
        this.#summernote = new SummerNote();

        this.#initForm();

        this.registerEvents();
    }
    
    #initForm()
    {
        $(`${this.#mapper.form} select`).select2({
            minimumResultsForSearch: -1
        });

        this.#dropZone.init($('[data-files="job"]'));

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            const element = $(this.#mapper.fields[`description_${locale}`]);

            this.#summernote.initialize(
                element,
                this.#summernote.createCallBacksSummernote(element, 'job')
            );
        }

        $('.dropdown-toggle').dropdown();

        if (IS_EDIT) {
            this.#dropZone.setFiles(IMAGES, 'job');
        }

        this.#validator.validate(this.#mapper.form);
    }

    registerEvents()
    {
        $(this.#mapper.submitBtn).on('click', (e) => {
            const handler = new JobsHandler();

            handler.save();
        });

        this.#baseEvents.events();
        this.#countrySelectionEvents.registerEvents();
    }
}

export default JobEditController;
