import baseEvents from "./BaseEvents";
import OfficeContactEditMapper from "../Mapper/OfficeContactEditMapper";
import OfficeContactHandler from "../Handler/OfficeContactHandler";
import OfficeContactEditValidator from "../Validators/OfficeContactEditValidator";
import countrySelectionEvents from "../Event/CountrySelectionEvents";

class OfficeContactEditController{
    #baseEvents;
    #mapper;
    #handler;
    #validator;
    #countrySelectionEvents;

    constructor() {
        this.#baseEvents = baseEvents;
        this.#mapper = OfficeContactEditMapper;
        this.#handler = new OfficeContactHandler();
        this.#validator = OfficeContactEditValidator;
        this.#countrySelectionEvents = countrySelectionEvents;


        this.#initForm();

        this.registerEvents();
    }

    #initForm()
    {
        $(`${this.#mapper.form} select`).select2();

        this.#validator.validate(this.#mapper.form);
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click touchend', e => {
            e.stopPropagation();
            e.preventDefault();

            this.#handler.save();
        });

        this.#baseEvents.events();
        this.#countrySelectionEvents.registerEvents();
    }
}

export default OfficeContactEditController;
