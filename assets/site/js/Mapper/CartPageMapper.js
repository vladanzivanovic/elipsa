class CartPageMapper {
    constructor() {
        if (!CartPageMapper.instance) {
            this.quatityInput = '.product-quantity-t input';
            this.productPrice = $('.product_price_value');
            this.productTotalPrice = $('.product_price_total');
            this.removeProduct = $('.remove-product');
            this.total = $('.total-price');
            this.totalShipping = $('.total-price-shipping');
            this.updateBtn = $('.scb-update');
            this.promoCouponInput = $('#promo_coupon');
            this.promoCouponBtn = $('.promo-coupon-btn');
            this.promoCouponErrorText = $('#coupon-error-text');
            this.promoCouponPrice = $('.promo-coupon-discount');
            this.shippingPrice = $('.shipping-price');

            CartPageMapper.instance = this;
        }

        return CartPageMapper.instance;
    }
}

const cartPageMapper = new CartPageMapper();

Object.freeze(cartPageMapper);

export default cartPageMapper;