import collaboratorPageMapper from "../Mapper/CollaboratorPageMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class CollaboratorPageValidation {
    constructor() {
        if (!CollaboratorPageValidation.instance) {
            this.mapper = collaboratorPageMapper;

            CollaboratorPageValidation.instance = this;
        }

        return CollaboratorPageValidation.instance;
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
            },
        };
        $.extend(options, window.helpBlock);

        return $(this.mapper.form).validate(options);
    }
}

const collaboratorPageValidation = new CollaboratorPageValidation();

Object.freeze(collaboratorPageValidation);

export default collaboratorPageValidation;