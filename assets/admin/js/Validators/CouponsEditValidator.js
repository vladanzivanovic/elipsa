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
                code: {
                    required: true,
                    remote: () => {
                            return Routing.generate('admin.promotion_code_checker_api', {id: ID})
                        },
                },
                valid_from: {
                    required: true,
                },
                valid_to: {
                    required: true,
                },
                discount: {
                    required: true,
                },
            },
        };

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const couponsEditValidator = new CouponsEditValidator();

Object.freeze(couponsEditValidator);

export default couponsEditValidator;
