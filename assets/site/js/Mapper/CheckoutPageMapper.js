class CheckoutPageMapper {
    constructor() {
        if (!CheckoutPageMapper.instance) {
            this.form = $('#checkout-form');
            this.email = $('#email');
            this.paymentType = '[name="payment_type"]';
            this.accountCreateChk = $('#createAccount');
            this.accountCreateError = $('#createAccountError');
            this.password = $('#checkoutPassword');
            this.btn = $('.checkout-page-button');

            CheckoutPageMapper.instance = this;
        }

        return CheckoutPageMapper.instance;
    }
}

const checkoutPageMapper = new CheckoutPageMapper();

Object.freeze(checkoutPageMapper);

export default checkoutPageMapper;