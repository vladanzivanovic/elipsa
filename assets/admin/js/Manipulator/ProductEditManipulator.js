import productEditDom from "../Dom/ProductEditDom";
import productEditMapper from "../Mapper/ProductEditMapper";
require ('select2/dist/js/select2.full.min');

class ProductEditManipulator {
    #productEditDom;
    #productEditMapper;

    constructor() {
        if(!ProductEditManipulator.instance) {
            this.#productEditDom = productEditDom;
            this.#productEditMapper = productEditMapper;

            ProductEditManipulator.instance = this;
        }

        return ProductEditManipulator.instance;
    }

    setSizes()
    {
        for (const countryCode in PRODUCT_SIZES) {
            const sizes = PRODUCT_SIZES[countryCode];

            for (const size of sizes) {
                this.addSizeRow(countryCode, size.slug, size.quantity);
            }
        }
    }

    addSizeRow(countryCode, sizeValue, quantity)
    {
        const row = this.#productEditDom.getSizeRowHtml(countryCode, sizeValue, quantity);

        $(`${this.#productEditMapper.sizes[countryCode].table} tbody`).append(row);

        $('.sizes').select2({width: '100%'});
    }

    removeSizeRow(rowElement)
    {
        rowElement.remove();
    }
}

const productEditManipulator = new ProductEditManipulator();

Object.freeze(productEditManipulator);

export default productEditManipulator;
