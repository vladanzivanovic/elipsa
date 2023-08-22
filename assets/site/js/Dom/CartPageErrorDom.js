import cartPageMapper from "../Mapper/CartPageMapper";

class CartPageErrorDom {
    #pageMapper;

    constructor() {
        if (!CartPageErrorDom.instance) {
            this.#pageMapper = cartPageMapper;

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
