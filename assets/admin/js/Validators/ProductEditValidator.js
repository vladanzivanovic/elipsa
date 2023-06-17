import productEditMapper from "../Mapper/ProductEditMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class ProductEditValidator {
    #mapper;

    constructor() {
        if (!ProductEditValidator.instance) {
            this.#mapper = productEditMapper;

            ProductEditValidator.instance = this;
        }

        return ProductEditValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                rs_title: 'required',
                rs_short_description: 'required',
                rs_description: 'required',
                en_title: 'required',
                en_short_description: 'required',
                en_description: 'required',
                en_link: 'required',
                'categories[]': 'isMultiSelectBoxEmpty',
                'tags[]': 'isMultiSelectBoxEmpty',
                'sizes[]': 'isMultiSelectBoxEmpty',
                code: 'required',
                price: 'required',
                discount: 'integer',
                'cleaning[]': 'required',
                mainImages: {
                    dropZoneHasImage: true,
                    dropZoneHasMainImage: true,
                }
            },
        };

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const productEditValidator = new ProductEditValidator();

Object.freeze(productEditValidator);

export default productEditValidator;
