import resetPasswordPageMapper from "../Mapper/ResetPasswordPageMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class ResetPasswordPageValidation {
    constructor() {
        if (!ResetPasswordPageValidation.instance) {
            this.mapper = resetPasswordPageMapper;

            ResetPasswordPageValidation.instance = this;
        }

        return ResetPasswordPageValidation.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                password: {
                    required: true,
                    minlength: 5
                },
                repeat_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                },
            },
        };
        $.extend(options, window.helpBlock);

        return $(this.mapper.form).validate(options);
    }
}

const resetPasswordPageValidation = new ResetPasswordPageValidation();

Object.freeze(resetPasswordPageValidation);

export default resetPasswordPageValidation;