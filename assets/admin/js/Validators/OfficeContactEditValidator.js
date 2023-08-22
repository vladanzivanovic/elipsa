require ('../../../js/Validators/ValidationRuleHelper');

class OfficeContactEditValidator {
    constructor() {
        if (!OfficeContactEditValidator.instance) {
            OfficeContactEditValidator.instance = this;
        }

        return OfficeContactEditValidator.instance;
    }

    validate(form) {
        let options;

        options = {
            rules: {
                telephone: 'required',
            },
        };

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            options.rules[`title_${locale}`] = 'required';
        }

        $.extend(options, window.helpBlock);

        return $(form).validate(options);
    }
}

const officeContactEditValidator = new OfficeContactEditValidator();

Object.freeze(officeContactEditValidator);

export default officeContactEditValidator;
