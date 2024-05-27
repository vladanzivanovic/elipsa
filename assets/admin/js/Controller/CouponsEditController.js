import datePicker from 'bootstrap-datepicker';
import couponsEditMapper from "../Mapper/CouponsEditMapper";
import CouponHandler from "../Handler/Coupon/CouponHandler";
import couponsEditValidator from "../Validators/CouponsEditValidator";
import promotionEditDom from "../Dom/PromotionEditDom";
import countrySelectionEvents from "../Event/CountrySelectionEvents";

class CouponsEditController {
    #mapper;
    #handler;
    #validator;
    #dom;
    #countrySelectionEvents;

    constructor() {
        this.#mapper = couponsEditMapper;
        this.#handler = new CouponHandler();
        this.#validator = couponsEditValidator;
        this.#dom = promotionEditDom;
        this.#countrySelectionEvents = countrySelectionEvents;

        this.#dom.preparePage();

        this.#validator.validate();

        this.registerEvents();
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click', e => {
            this.#handler.save();
        });

        $(this.#mapper.fields.type).on('change', e => {
            this.#dom.manageFieldsByPromotionType();
        });

        this.#countrySelectionEvents.registerEvents();
    }
}

export default CouponsEditController;
