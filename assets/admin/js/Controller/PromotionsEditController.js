import couponsEditValidator from "../Validators/CouponsEditValidator";
import PromotionEditEvents from "../Event/PromotionEditEvents";
import promotionEditDom from "../Dom/PromotionEditDom";

class PromotionsEditController {
    #validator;
    #dom;

    constructor() {
        this.#validator = couponsEditValidator;
        this.#dom = promotionEditDom;

        this.#dom.preparePage();

        this.#validator.validate();

        PromotionEditEvents.registerEvents();
    }
}

export default PromotionsEditController;
