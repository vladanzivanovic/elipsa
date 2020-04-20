class ProductPageMapper {
    constructor() {
        if (!ProductPageMapper.instance) {
            this.color = $('.color-btn');
            this.largeImage = $('.large-image');
            this.thumbImage = $('.thumb-image');

            ProductPageMapper.instance = this;
        }

        return ProductPageMapper.instance;
    }
}

const instance = new ProductPageMapper();

Object.freeze(instance);

export default instance;