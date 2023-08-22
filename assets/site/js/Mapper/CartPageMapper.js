class CartPageMapper {
    constructor() {
        if (!CartPageMapper.instance) {
            this.quatityInput = '.product-quantity-t input';
            this.productPrice = $('.product_price_value');
            this.productTotalPrice = $('.product_price_total');
            this.removeProduct = '.remove-product';
            this.total = '.total-price';
            this.totalShipping = '.total-price-shipping';
            this.updateBtn = '.update-products';
            this.promoCouponInput = '#promo_coupon';
            this.promoCouponErrorText = $('#coupon-error-text');
            this.promoCouponBoxText = '.view-coupon-wrapper p';
            this.promoCouponPrice = '.promo-coupon-discount';
            this.shippingPrice = '.shipping-price';
            this.productTable = '.shopping-cart-table table';
            this.nextStepBtn = '.next-step-btn';
            this.promoCouponHolder = '.promo-coupon-amount-holder';
            this.promoCouponAddBox = '.add-coupon-wrapper';
            this.promoCouponViewBox = '.view-coupon-wrapper';
            this.promoCouponRemoveBtn = '.promo-coupon-remove-btn';
            this.promoCouponAddBtn = '.promo-coupon-btn';

            CartPageMapper.instance = this;
        }

        return CartPageMapper.instance;
    }
}

const cartPageMapper = new CartPageMapper();

Object.freeze(cartPageMapper);

export default cartPageMapper;
