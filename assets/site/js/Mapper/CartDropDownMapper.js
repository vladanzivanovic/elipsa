class CartDropDownMapper {
    constructor() {
        if (!CartDropDownMapper.instance) {
            this.productList = $('.my-cart-products');
            this.productLength = $('.cart-length');
            this.total = $('.cost__number');

            CartDropDownMapper.instance = this;
        }

        return CartDropDownMapper.instance;
    }
}
const instance = new CartDropDownMapper();

Object.freeze(instance);

export default instance;