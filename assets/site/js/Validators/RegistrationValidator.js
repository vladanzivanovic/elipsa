import registrationMapper from "../Mapper/RegistrationMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class RegistrationValidator {
    #mapper;

    constructor() {
        if (!RegistrationValidator.instance) {
            this.#mapper = registrationMapper;

            RegistrationValidator.instance = this;
        }

        return RegistrationValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                first_name: {
                    required: true,
                },
                last_name: {
                    required: true,
                },
                email:{
                    required: true,
                    email: true,
                    checkEmail: true,
                },
                password: {
                    required: true,
                    minlength: 5
                },
                re_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                },
                'address[street]': {
                    required: true,
                },
                'address[city]': {
                    required: true,
                },
                'address[country]': {
                    required: true,
                },
                'address[zip_code]': {
                    required: true,
                    digits: true,
                },
                'address[mobile_phone]': {
                    required: true,
                }
            },
        };

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const registrationValidator = new RegistrationValidator();

Object.freeze(registrationValidator);

export default registrationValidator;
