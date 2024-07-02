import sizeEditMapper from "../Mapper/SizeEditMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class SizeEditValidator {
    #mapper;

    constructor() {
        if (!SizeEditValidator.instance) {
            this.#mapper = sizeEditMapper;

            SizeEditValidator.instance = this;
        }

        return SizeEditValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                title: 'required',
            },
        };

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const sizeEditValidator = new SizeEditValidator();

Object.freeze(sizeEditValidator);

export default sizeEditValidator;
