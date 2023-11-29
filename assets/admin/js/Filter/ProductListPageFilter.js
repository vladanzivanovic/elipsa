import productListPageMapper from "../Mapper/ProductListPageMapper";

require ('select2/dist/js/select2.full.min');

class ProductListPageFilter {
    #mapper;

    constructor() {
        this.#mapper = productListPageMapper;

        $(`${this.#mapper.form} select`).select2();
    }
}

const productListPageFilter = new ProductListPageFilter();

Object.freeze(productListPageFilter);

export default productListPageFilter;
