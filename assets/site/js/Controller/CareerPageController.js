import careerPageMapper from "../Mapper/CareerPageMapper";
import careerPageValidation from "../Validators/CareerPageValidation";
import CareerPageHandler from "../Handler/CareerPageHandler";
import DateTimeService from "../Service/DateTimeService";

class CareerPageController {
    constructor() {
        this.mapper = careerPageMapper;
        this.validator = careerPageValidation;
        this.handler = new CareerPageHandler();
        this.dateTime = new DateTimeService();

        this.validator.validate();

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.submitBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.handler.save(this.dateTime);
        });
    }
}

export default CareerPageController;