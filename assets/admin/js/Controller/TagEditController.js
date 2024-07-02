import TagHandler from "../Handler/Product/TagHandler";
import tagEditValidator from "../Validators/TagEditValidator";
import tagEditMapper from "../Mapper/TagEditMapper";
import baseEvents from "./BaseEvents";

class TagEditController {
    #mapper;
    #validator;
    #baseEvents;

    constructor() {
        this.#mapper = tagEditMapper;
        this.#validator = tagEditValidator;
        this.#baseEvents = baseEvents;

        this.#initForm();

        this.registerEvents();
    }

    #initForm()
    {
        this.#validator.validate();
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click touchend', e => {
            const handler = new TagHandler();

            handler.save();
        });

        this.#baseEvents.events();
    }
}

export default TagEditController;
