require ('../../../js/Validators/ValidationRuleHelper');

class ProductEditValidator {
    constructor() {
        if (!ProductEditValidator.instance) {
            ProductEditValidator.instance = this;
        }

        return ProductEditValidator.instance;
    }

    validate(form) {
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

        return form.validate(options);
    }
}

const productEditValidator = new ProductEditValidator();

Object.freeze(productEditValidator);

export default productEditValidator;