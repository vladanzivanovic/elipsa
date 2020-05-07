import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import CheckoutHandler from "../Handler/CheckoutHandler";
import checkoutValidation from "../Validators/CheckoutValidation";

class CheckoutPageController {
    constructor() {
        this.mapper = checkoutPageMapper;
        this.handler = new CheckoutHandler();
        this.validator = checkoutValidation;

        this.validator.validate();

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.btn.on('click touchend', e => {
            this.handler.save();
        });

        $('.open-login').on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            $('#login_register').click();
        })
    }
}

export default CheckoutPageController;