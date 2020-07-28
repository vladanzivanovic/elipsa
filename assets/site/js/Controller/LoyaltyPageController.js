import loyaltyPageMapper from "../Mapper/LoyaltyPageMapper";
import DateTimeDom from "../Dom/DateTimeDom";
import LoyaltyPageHandler from "../Handler/LoyaltyPageHandler";
import loyaltyPageValidation from "../Validators/LoyaltyPageValidation";
import DateTimeService from "../Service/DateTimeService";

class LoyaltyPageController {
    constructor() {
        this.mapper = loyaltyPageMapper;
        this.dateTimeService = new DateTimeService();
        this.validator = loyaltyPageValidation;
        this.handler = new LoyaltyPageHandler();

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

export default LoyaltyPageController;