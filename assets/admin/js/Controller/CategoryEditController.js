import CategoryHandler from "../Handler/CategoryHandler";
import categoryEditValidator from "../Validators/CategoryEditValidator";
import categoryEditMapper from "../Mapper/CategoryEditMapper";
import baseEvents from "./BaseEvents";

class CategoryEditController {
    #mapper;
    #validator;
    #baseEvents

    constructor() {
        this.#mapper = categoryEditMapper;
        this.#validator = categoryEditValidator;
        this.#baseEvents = baseEvents;

        this.#initForm();

        this.#registerEvents();
    }

    #initForm()
    {
        $(`${this.#mapper.form} select`).select2();

        this.#validator.validate();
    }

    #registerEvents() {
        $(this.#mapper.submitBtn).on('click', e => {
            const handler = new CategoryHandler();

            handler.save();
        });

        this.#baseEvents.events();
    }
}

export default CategoryEditController;
