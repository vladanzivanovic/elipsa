import askUsPageMapper from "../Mapper/AskUsPageMapper";
import askUsPageValidation from "../Validators/AskUsPageValidation";
import AskUsPageHandler from "../Handler/AskUsPageHandler";

class AskUsPageController {
    constructor() {
        this.mapper = askUsPageMapper;
        this.validator = askUsPageValidation;
        this.handler = new AskUsPageHandler();

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.submitBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.handler.save();
        });
    }
}

export default AskUsPageController;