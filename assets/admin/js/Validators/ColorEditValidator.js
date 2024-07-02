import colorEditMapper from "../Mapper/ColorEditMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class ColorEditValidator {
    #mapper;

    constructor() {
        if (!ColorEditValidator.instance) {
            this.#mapper = colorEditMapper;

            ColorEditValidator.instance = this;
        }

        return ColorEditValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                color: 'required',
                rs_title: 'required',
                en_title: 'required',
            },
        };

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const colorEditValidator = new ColorEditValidator();

Object.freeze(colorEditValidator);

export default colorEditValidator;
