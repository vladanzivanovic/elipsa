import ColorHandler from "../Handler/Product/ColorHandler";
import colorEditValidator from "../Validators/ColorEditValidator";
import colorEditMapper from "../Mapper/ColorEditMapper";
import baseEvents from "./BaseEvents";
import('bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css');
require('bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min');

class ColorEditController {
    #mapper;
    #validator;
    #baseEvents;

    constructor() {
        this.#mapper = colorEditMapper;
        this.#validator = colorEditValidator;
        this.#baseEvents = baseEvents;

        this.#initForm();

        this.registerEvents();
    }

    #initForm()
    {
        $(this.#mapper.color).colorpicker();

        this.#validator.validate();
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click touchend', e => {
            const handler = new ColorHandler();

            handler.save();
        });

        this.#baseEvents.events();
    }
}

export default ColorEditController;
