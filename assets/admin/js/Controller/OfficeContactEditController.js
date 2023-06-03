import baseEvents from "./BaseEvents";
import OfficeContactEditMapper from "../Mapper/OfficeContactEditMapper";
import OfficeContactHandler from "../Handler/OfficeContactHandler";
import OfficeContactEditValidator from "../Validators/OfficeContactEditValidator";

class OfficeContactEditController{
    constructor() {
        this.baseEvents = baseEvents;
        this.mapper = OfficeContactEditMapper;
        this.handler = new OfficeContactHandler();
        this.validator = OfficeContactEditValidator;

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

export default OfficeContactEditController;
