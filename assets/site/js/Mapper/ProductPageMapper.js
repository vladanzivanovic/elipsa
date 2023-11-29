class ProductPageMapper {
    constructor() {
        if (!ProductPageMapper.instance) {
            this.color = '.color-btn';
            this.colorActive = '.color-btn.active';
            this.size = '.size-btn';
            this.sizeActive = '.size-btn.active';
            this.quantity = '#product-quantity';
            this.addBtn = '.add-to-cart';
            this.cartBtnWrapper = '.cart-btn-wrapper';
            this.notifyMeBtnWrapper = '.notify-me-wrapper';
            this.notifyMeInput = '#notify-email';
            this.notifyMeBtn = '.notify-btn';

            ProductPageMapper.instance = this;
        }

        return ProductPageMapper.instance;
    }
}

const instance = new ProductPageMapper();

Object.freeze(instance);

export default instance;
