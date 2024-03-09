import checkoutPageMapper from "../Mapper/CheckoutPageMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class CheckoutValidation {
    #mapper;

    constructor() {
        if (!CheckoutValidation.instance) {
            this.#mapper = checkoutPageMapper;

            CheckoutValidation.instance = this;
        }

        return CheckoutValidation.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                'billing_address[country]': {
                    required: true,
                },
                'billing_address[first_name]': {
                    required: true,
                },
                'billing_address[last_name]': {
                    required: true,
                },
                'billing_address[street]': {
                    required: true,
                },
                'billing_address[city]': {
                    required: true,
                },
                'billing_address[zip_code]': {
                    required: true,
                    number  : true
                },
                'billing_address[email]': {
                    required: true,
                    email   : true,
                    checkEmail: true,
                },
                'billing_address[mobile_phone]': {
                    required: true,
                },
                password    : {
                    minlength: 5,
                    requiredOnDemand: {
                        selector: '[name="create_account"]',
                    }
                },
                payment_type: {
                    required: true,
                },
                shipping_type: {
                    required: true,
                },
                store_location: {
                    isSelectBoxEmpty: true,
                },
                terms_and_conditions: {
                    required: true,
                },
            },
        };
        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const checkoutValidation = new CheckoutValidation();

Object.freeze(checkoutValidation);

export default checkoutValidation;
