import datePicker from 'bootstrap-datepicker';
import couponsEditMapper from "../Mapper/CouponsEditMapper";
import CouponHandler from "../Handler/Coupon/CouponHandler";
import couponsEditValidator from "../Validators/CouponsEditValidator";
import promotionEditDom from "../Dom/PromotionEditDom";

class CouponsEditController {
    #mapper;
    #handler;
    #validator;
    #dom;

    constructor() {
        this.#mapper = couponsEditMapper;
        this.#handler = new CouponHandler();
        this.#validator = couponsEditValidator;
        this.#dom = promotionEditDom;

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
    }
}

export default CouponsEditController;
