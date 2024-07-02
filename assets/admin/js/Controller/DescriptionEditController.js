import SummerNote from "../Services/SummerNote";
import descriptionEditMapper from "../Mapper/DescriptionEditMapper";
import DescriptionHandler from "../Handler/DescriptionHandler";
import baseEvents from "./BaseEvents";
require ('select2/dist/js/select2.full.min');

class DescriptionEditController {
    #baseEvents;
    #mapper;
    #summernote;

    constructor() {
        this.#baseEvents = baseEvents;
        this.#mapper = descriptionEditMapper;
        this.#summernote = new SummerNote();
        this.handler = new DescriptionHandler();

        this.#initForm();

        this.registerEvents();
    }

    #initForm()
    {
        for(const [locale, data] of Object.entries(LANGUAGES)) {
            const element = $(this.#mapper.fields[`description_${locale}`]);

            this.#summernote.initialize(
                element,
                this.#summernote.createCallBacksSummernote(element)
            );
        }

        $('.dropdown-toggle').dropdown();

        $(`${this.#mapper.form} select`).select2();
    }

    registerEvents()
    {
        $(this.#mapper.submitBtn).on('click touchend', (e) => {
            e.preventDefault();
            e.stopPropagation();

            this.handler.save(this.#mapper);
        });

        this.#baseEvents.events();
    }
}

export default DescriptionEditController;
