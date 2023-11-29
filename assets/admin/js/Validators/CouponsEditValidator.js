import couponsEditMapper from "../Mapper/CouponsEditMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class CouponsEditValidator {
    #mapper;

    constructor() {
        if (!CouponsEditValidator.instance) {
            this.#mapper = couponsEditMapper;

            CouponsEditValidator.instance = this;
        }

        return CouponsEditValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                code: 'required',
                valid_from: 'required',
                valid_to: 'required',
                discount: 'required',
            },
        };

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const couponsEditValidator = new CouponsEditValidator();

Object.freeze(couponsEditValidator);

export default couponsEditValidator;
