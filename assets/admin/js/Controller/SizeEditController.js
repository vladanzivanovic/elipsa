import SizeHandler from "../Handler/SizeHandler";
import sizeEditValidator from "../Validators/SizeEditValidator";
import sizeEditMapper from "../Mapper/SizeEditMapper";
import baseEvents from "./BaseEvents";

class SizeEditController {
    #mapper;
    #validator;
    #baseEvents;

    constructor() {
        this.#mapper = sizeEditMapper;
        this.#validator = sizeEditValidator;
        this.#baseEvents = baseEvents;

        this.#initForm();

        this.registerEvents();
    }

    #initForm()
    {
        this.#validator.validate();
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click', e => {
            const handler = new SizeHandler();

            handler.save();
        });

        this.#baseEvents.events();
    }
}

export default SizeEditController;
