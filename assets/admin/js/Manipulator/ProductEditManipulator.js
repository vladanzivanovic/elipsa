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

    setOnEdit()
    {
        for (const size of PRODUCT_SIZES) {
            this.addSizeRow(size.size, size.quantity);
        }
    }

    addSizeRow(sizeValue, quantity)
    {
        const row = this.#productEditDom.getSizeRowHtml(sizeValue, quantity);

        $(`${this.#productEditMapper.sizes} tbody`).append(row);

        $('.sizes:last').select2();
    }

    removeSizeRow(rowElement)
    {
        rowElement.remove();
    }
}

const productEditManipulator = new ProductEditManipulator();

Object.freeze(productEditManipulator);

export default productEditManipulator;
