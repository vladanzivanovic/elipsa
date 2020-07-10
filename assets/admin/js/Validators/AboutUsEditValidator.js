require ('../../../js/Validators/ValidationRuleHelper');

class AboutUsEditValidator {
    constructor() {
        if (!AboutUsEditValidator.instance) {
            AboutUsEditValidator.instance = this;
        }

        return AboutUsEditValidator.instance;
    }

    validate(form) {
        let options;

        options = {
            ignore: '',
            rules: {
                rs_description: 'setErrorIfSummernoteIsEmpty',
                en_description: 'setErrorIfSummernoteIsEmpty',
            },
        };

        $.extend(options, window.helpBlock);

        return $(form).validate(options);
    }
}

const aboutUsEditValidator = new AboutUsEditValidator();

Object.freeze(aboutUsEditValidator);

export default aboutUsEditValidator;