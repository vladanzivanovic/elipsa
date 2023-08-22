class ProductsMapper {
    constructor() {
        if(!ProductsMapper.instance) {
            this.toggleWishListBtn = '.toggle-wish-list';

            ProductsMapper.instance = this;
        }

        return ProductsMapper.instance;
    }
}

const productMapper = new ProductsMapper();

Object.freeze(productMapper);

export default productMapper;
