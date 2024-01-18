import blogEditMapper from "../Mapper/BlogEditMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class BlogEditValidator {
    #mapper;

    constructor() {
        if (!BlogEditValidator.instance) {
            this.#mapper = blogEditMapper;

            BlogEditValidator.instance = this;
        }

        return BlogEditValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                'tags[]': 'isMultiSelectBoxEmpty',
                main_images: {
                    dropZoneHasImage: true,
                    dropZoneHasMainImage: true,
                }
            },
        };

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            options.rules[`${locale}[title]`] = {required: true};
            options.rules[`${locale}[short_description]`] = {required: true};
            options.rules[`${locale}[description]`] = {setErrorIfSummernoteIsEmpty: true};
        }

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const blogEditValidator = new BlogEditValidator();

Object.freeze(blogEditValidator);

export default blogEditValidator;
