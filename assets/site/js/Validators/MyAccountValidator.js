import myAccountPageMapper from "../Mapper/MyAccountPageMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class MyAccountValidator {
    #mapper;

    constructor() {
        if (!MyAccountValidator.instance) {
            this.#mapper = myAccountPageMapper;

            MyAccountValidator.instance = this;
        }

        return MyAccountValidator.instance;
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
                profile_password: {
                    required: true,
                    minlength: 5
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

        return $(this.#mapper.personalForm).validate(options);
    }
}

const myAccountValidator = new MyAccountValidator();

Object.freeze(myAccountValidator);

export default myAccountValidator;
