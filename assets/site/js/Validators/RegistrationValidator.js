require ('../../../js/Validators/ValidationRuleHelper');

class RegistrationValidator {
    constructor() {
        if (!RegistrationValidator.instance) {
            RegistrationValidator.instance = this;
        }

        return RegistrationValidator.instance;
    }

    validate(form) {
        let options;

        options = {
            rules: {
                registration_first_name: 'required',
                registration_last_name: 'required',
                registration_email:{
                    required: true,
                    email: true,
                },
                registration_password: {
                    required: true,
                    minlength: 5
                },
                registration_re_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#registration_password"
                },
            },
        };

        $.extend(options, window.helpBlock);

        return $(form).validate(options);
    }
}

const registrationValidator = new RegistrationValidator();

Object.freeze(registrationValidator);

export default registrationValidator;