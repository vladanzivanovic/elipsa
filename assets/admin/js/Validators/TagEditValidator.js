import tagEditMapper from "../Mapper/TagEditMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class TagEditValidator {
    #mapper;

    constructor() {
        if (!TagEditValidator.instance) {
            this.#mapper = tagEditMapper;

            TagEditValidator.instance = this;
        }

        return TagEditValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {},
        };

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            options.rules[`translations[${locale}][title]`] = 'required';
        }

        if (TAG_TYPE === 'product') {
            options.rules.product_type = {isSelectBoxEmpty: true};
        }

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const tagEditValidator = new TagEditValidator();

Object.freeze(tagEditValidator);

export default tagEditValidator;
