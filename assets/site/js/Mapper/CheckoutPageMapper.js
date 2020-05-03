class CheckoutPageMapper {
    constructor() {
        if (!CheckoutPageMapper.instance) {
            this.form = $('#checkout-form');
            this.btn = $('.checkout-page-button');

            CheckoutPageMapper.instance = this;
        }

        return CheckoutPageMapper.instance;
    }
}

const checkoutPageMapper = new CheckoutPageMapper();

Object.freeze(checkoutPageMapper);

export default checkoutPageMapper;