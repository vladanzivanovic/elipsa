class ProductEditDom {
    constructor() {
        if(!ProductEditDom.instance) {
            ProductEditDom.instance = this;
        }

        return ProductEditDom.instance;
    }

    getSizeRowHtml(countryCode, sizeValue = null, quantity = null)
    {
        let sizeOptions = '';

        for (let size of SIZES) {
            sizeOptions += `<option value="${size.value}" ${sizeValue === size.value ? 'selected="selected"' : '""'}>${size.title}</option>`;
        }

        return `<tr>
                    <td class="col-sm-4">
                        <select name="options[${countryCode}][sizes][slug][]" class="sizes">
                            <option value="-1">Izaberite...</option>
                            ${sizeOptions}
                        </select>
                    </td>
                    <td class="col-sm-4">
                        <input type="number" name="options[${countryCode}][sizes][quantity][]" class="form-control" value="${null !== quantity ? quantity : 0}" min="0">
                    </td>
                    <td class="col-sm-4 text-center"><button class="btn btn-danger size-remove-btn-${ countryCode }" type="button">Ukloni</button></td>
                </tr>`;

    }
}

const productEditDom = new ProductEditDom();

Object.freeze(productEditDom);

export default productEditDom;
