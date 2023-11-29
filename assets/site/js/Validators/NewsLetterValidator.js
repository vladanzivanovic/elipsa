import newsLetterMapper from "../Mapper/NewsLetterMapper";

class NewsLetterValidator {
    #mapper;

    constructor() {
        if (!NewsLetterValidator.instance) {
            this.#mapper = newsLetterMapper;

            NewsLetterValidator.instance = this;
        }

        return NewsLetterValidator.instance;
    }

    validate(form) {
        let options;

        options = {
            rules: {
                'newsletter_email': {
                    required: true,
                    email: true,
                },
                'newsletter_gender': {
                    isSelectBoxEmpty: true,
                },
                'newsletter_language': {
                    isSelectBoxEmpty: true,
                },
            },
        };
        $.extend(options, window.helpBlock);

        return $(form).validate(options);
    }
}

const newsLetterValidator = new NewsLetterValidator();

Object.freeze(newsLetterValidator);

export default newsLetterValidator;
