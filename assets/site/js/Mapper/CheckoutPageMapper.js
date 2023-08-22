class CheckoutPageMapper {
    constructor() {
        if (!CheckoutPageMapper.instance) {
            this.form = '#checkout-form';
            this.email = '#email';
            this.paymentType = '[name="payment_type"]';
            this.accountCreateChk = '#createAccount';
            this.accountCreateError = '#createAccountError';
            this.password = '#checkoutPassword';
            this.btn = '.checkout-page-button';
            this.productList = '.product-list';
            this.productsTotal = '#products-total';
            this.promoPrice = '#promo-price';
            this.shippingPrice = '#shipping-price';
            this.totalWithShipping = '#total-with-shipping';

            CheckoutPageMapper.instance = this;
        }

        return CheckoutPageMapper.instance;
    }
}

const checkoutPageMapper = new CheckoutPageMapper();

Object.freeze(checkoutPageMapper);

export default checkoutPageMapper;
