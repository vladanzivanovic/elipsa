import careerPageMapper from "../Mapper/CareerPageMapper";
import careerPageValidation from "../Validators/CareerPageValidation";
import CareerPageHandler from "../Handler/CareerPageHandler";

class CareerPageController {
    constructor() {
        this.mapper = careerPageMapper;
        this.validator = careerPageValidation;
        this.handler = new CareerPageHandler();

        this.validator.validate();

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

export default CareerPageController;