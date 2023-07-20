require ('../../../js/Validators/ValidationRuleHelper');

class TagEditValidator {
    constructor() {
        if (!TagEditValidator.instance) {
            TagEditValidator.instance = this;
        }

        return TagEditValidator.instance;
    }

    validate(form) {
        let options;

        options = {
            rules: {
                rs_title: {required: true},
                en_title: {required: true},
            },
        };

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            options.rules[`${locale}[title]`] = 'required';
        }

        if (ROUTE_SUB_NAME === 'product') {
            options.rules.product_type = {isSelectBoxEmpty: true};
        }

        $.extend(options, window.helpBlock);

        return form.validate(options);
    }
}

const tagEditValidator = new TagEditValidator();

Object.freeze(tagEditValidator);

export default tagEditValidator;
