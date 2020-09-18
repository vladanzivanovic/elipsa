import checkoutPageMapper from "../Mapper/CheckoutPageMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class CheckoutValidation {
    constructor() {
        if (!CheckoutValidation.instance) {
            this.mapper = checkoutPageMapper;

            CheckoutValidation.instance = this;
        }

        return CheckoutValidation.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                country     : {
                    required: true,
                },
                first_name   : 'required',
                last_name    : 'required',
                address     : 'required',
                city        : 'required',
                zip_code     : {
                    required: true,
                    number  : true
                },
                email       : {
                    required: true,
                    email   : true
                },
                mobile_phone : 'required',
                password    : {
                    minlength: 5,
                    requiredOnDemand: {
                        selector: '[name="create_account"]',
                    }
                },
                payment_type: 'required',
                terms_and_conditions: 'required',
            },
        };
        $.extend(options, window.helpBlock);

        return this.mapper.form.validate(options);
    }
}

const checkoutValidation = new CheckoutValidation();

Object.freeze(checkoutValidation);

export default checkoutValidation;