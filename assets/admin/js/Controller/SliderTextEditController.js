import SliderTextHandler from "../Handler/SliderTextHandler";
import sliderTextEditMapper from "../Mapper/SliderTextEditMapper";
import sliderTextEditValidator from "../Validators/SliderTextEditValidator";
import baseEvents from "./BaseEvents";
import countrySelectionEvents from "../Event/CountrySelectionEvents";

class SliderTextEditController {
    #baseEvents;
    #mapper;
    #handler;
    #validator;
    #countrySelectionEvents;
    constructor() {
        this.#baseEvents = baseEvents;
        this.#mapper = sliderTextEditMapper;
        this.#handler = new SliderTextHandler();
        this.#validator = sliderTextEditValidator;
        this.#countrySelectionEvents = countrySelectionEvents;

        this.#initForm();

        this.registerEvents();
    }

    #initForm()
    {
        $(`${this.#mapper.form} select`).select2({
            minimumResultsForSearch: -1
        });

        this.#validator.validate(this.#mapper.form);
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click', e => {
            e.stopPropagation();
            e.preventDefault();

            this.#handler.save();
        });

        this.#baseEvents.events();
        this.#countrySelectionEvents.registerEvents();
    }
}

export default SliderTextEditController;
