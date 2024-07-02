import categoryEditMapper from "../Mapper/CategoryEditMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class CategoryEditValidator {
    #mapper;

    constructor() {
        if (!CategoryEditValidator.instance) {
            this.#mapper = categoryEditMapper;

            CategoryEditValidator.instance = this;
        }

        return CategoryEditValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {},
        };

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            options.rules[`translations[${locale}][title]`] = 'required';
        }

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const categoryEditValidator = new CategoryEditValidator();

Object.freeze(categoryEditValidator);

export default categoryEditValidator;
