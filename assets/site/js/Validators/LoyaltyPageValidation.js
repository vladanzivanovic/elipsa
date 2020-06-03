import loyaltyPageMapper from "../Mapper/LoyaltyPageMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class LoyaltyPageValidation {
    constructor() {
        if (!LoyaltyPageValidation.instance) {
            this.mapper = loyaltyPageMapper;

            LoyaltyPageValidation.instance = this;
        }

        return LoyaltyPageValidation.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                first_name   : 'required',
                last_name    : 'required',
                email       : {
                    required: true,
                    email   : true
                },
                occupation: 'required',
                mobile_phone : 'required',
                rate: 'required',
            },
        };
        $.extend(options, window.helpBlock);

        return $(this.mapper.form).validate(options);
    }
}

const loyaltyPageValidation = new LoyaltyPageValidation();

Object.freeze(loyaltyPageValidation);

export default loyaltyPageValidation;