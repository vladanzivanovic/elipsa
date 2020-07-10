require ('../../../js/Validators/ValidationRuleHelper');

class ResetPasswordValidator {
    constructor() {
        if (!ResetPasswordValidator.instance) {
            ResetPasswordValidator.instance = this;
        }

        return ResetPasswordValidator.instance;
    }

    validate(form) {
        let options;

        options = {
            rules: {
                reset_email:{
                    required: true,
                    email: true,
                },
            },
        };

        $.extend(options, window.helpBlock);

        return $(form).validate(options);
    }
}

const resetPasswordValidator = new ResetPasswordValidator();

Object.freeze(resetPasswordValidator);

export default resetPasswordValidator;