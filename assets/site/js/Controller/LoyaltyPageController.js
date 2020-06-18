import loyaltyPageMapper from "../Mapper/LoyaltyPageMapper";
import LoyaltyPageDom from "../Dom/LoyaltyPageDom";
import LoyaltyPageHandler from "../Handler/LoyaltyPageHandler";
import loyaltyPageValidation from "../Validators/LoyaltyPageValidation";

class LoyaltyPageController {
    constructor() {
        this.mapper = loyaltyPageMapper;
        this.dom = new LoyaltyPageDom();
        this.validator = loyaltyPageValidation;
        this.handler = new LoyaltyPageHandler();

        this.validator.validate();

        this.dom.generateYears();
        this.dom.generateDays();

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.monthField).on('change', e => {
            this.dom.generateDays();
        });
        $(this.mapper.yearField).on('change', e => {
            this.dom.generateDays();
        });
        $(this.mapper.submitBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.handler.save();
        });
    }
}

export default LoyaltyPageController;