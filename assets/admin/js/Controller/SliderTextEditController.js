import SliderTextHandler from "../Handler/SliderTextHandler";
import sliderTextEditMapper from "../Mapper/SliderTextEditMapper";
import sliderTextEditValidator from "../Validators/SliderTextEditValidator";
import baseEvents from "./BaseEvents";

class SliderTextEditController {
    constructor() {
        this.baseEvents = baseEvents;
        this.mapper = sliderTextEditMapper;
        this.handler = new SliderTextHandler();
        this.validator = sliderTextEditValidator;

        this.validator.validate(this.mapper.form);

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.submitBtn).on('click touchend', e => {
            e.stopPropagation();
            e.preventDefault();

            this.handler.save();
        });

        this.baseEvents.events();
    }
}

export default SliderTextEditController;
