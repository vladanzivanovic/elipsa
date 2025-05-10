import couponsEditMapper from "../Mapper/CouponsEditMapper";
import CouponHandler from "../Handler/Coupon/CouponHandler";
import countrySelectionEvents from "./CountrySelectionEvents";
import promotionEditDom from "../Dom/PromotionEditDom";


class PromotionEditEvents {
    #mapper;
    #handler;
    #countrySelectionEvents;
    #dom;

    constructor() {
        if(!PromotionEditEvents.instance) {
            this.#mapper = couponsEditMapper;
            this.#handler = new CouponHandler();
            this.#countrySelectionEvents = countrySelectionEvents;
            this.#dom = promotionEditDom;

            PromotionEditEvents.instance = this;
        }

        return PromotionEditEvents.instance;
    }

    registerEvents()
    {
        $(this.#mapper.submitBtn).on('click', e => {
            this.#handler.save();
        });

        $(this.#mapper.fields.type).on('change', e => {
            this.#dom.manageFieldsByPromotionType();
        });

        this.#countrySelectionEvents.registerEvents();
    }
}

const promotionEditEvents = new PromotionEditEvents();

Object.freeze(promotionEditEvents);

export default promotionEditEvents;
