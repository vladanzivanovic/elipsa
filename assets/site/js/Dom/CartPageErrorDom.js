import cartPageMapper from "../Mapper/CartPageMapper";
import toastrService from "../../../js/Services/ToastrService";

class CartPageErrorDom {
    #pageMapper;
    #toastr;

    constructor() {
        if (!CartPageErrorDom.instance) {
            this.#pageMapper = cartPageMapper;
            this.#toastr = toastrService;

            CartPageErrorDom.instance = this;
        }

        return CartPageErrorDom.instance;
    }

    setCoupon(couponError)
    {
        this.#pageMapper.promoCouponErrorText.text(
            Translator.trans(couponError, null, 'validators', LOCALE)
        );
    }
}

const cartPageErrorDom = new CartPageErrorDom();

Object.freeze(cartPageErrorDom);

export default cartPageErrorDom;
