import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import checkoutValidation from "../Validators/CheckoutValidation";
import RecaptchaLoader from "../../../js/Services/RecaptchaLoader";
import checkoutPageEvents from "../Events/CheckoutPageEvents";
import checkoutPageManipulator from "../Manipulator/CheckoutPageManipulator";

class CheckoutPageController {
    #pageEvents;
    #pageManipulator;
    #validator;

    constructor() {
        this.mapper = checkoutPageMapper;
        this.#validator = checkoutValidation;
        this.#pageEvents = checkoutPageEvents;
        this.#pageManipulator = checkoutPageManipulator;

        RecaptchaLoader.loadRecaptcha();

        this.#pageManipulator.setPage();

        this.#validator.validate();

        this.#pageEvents.registerEvents();
    }
}

export default CheckoutPageController;
