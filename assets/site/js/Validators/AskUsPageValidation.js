import askUsPageMapper from "../Mapper/AskUsPageMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class AskUsPageValidation {
    constructor() {
        if (!AskUsPageValidation.instance) {
            this.mapper = askUsPageMapper;

            AskUsPageValidation.instance = this;
        }

        return AskUsPageValidation.instance;
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

const askUsPageValidation = new AskUsPageValidation();

Object.freeze(askUsPageValidation);

export default askUsPageValidation;