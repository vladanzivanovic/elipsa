import askUsPageMapper from "../Mapper/AskUsFormMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class AskUsFormValidation {
    constructor() {
        if (!AskUsFormValidation.instance) {
            this.mapper = askUsPageMapper;

            AskUsFormValidation.instance = this;
        }

        return AskUsFormValidation.instance;
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
                email: {
                    required: true,
                    email   : true
                },
                subject: {
                    required: true,
                },
                note : {
                    required: true,
                },
                contact_via: {
                    isSelectBoxEmpty: true
                }
            },
        };
        $.extend(options, window.helpBlock);

        return $(this.mapper.form).validate(options);
    }
}

const askUsPageValidation = new AskUsFormValidation();

Object.freeze(askUsPageValidation);

export default askUsPageValidation;
