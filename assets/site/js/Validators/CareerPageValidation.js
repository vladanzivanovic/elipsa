import careerPageMapper from "../Mapper/CareerPageMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class CareerPageValidation {
    constructor() {
        if (!CareerPageValidation.instance) {
            this.mapper = careerPageMapper;

            CareerPageValidation.instance = this;
        }

        return CareerPageValidation.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                first_name   : 'required',
                last_name    : 'required',
                email       : {
                    required: true,
                    email   : true,
                },
                cv: 'required',
                mobile_phone: 'required',
                address: 'required',
                city: 'required',
                school: 'required',
                school_level: 'required',
                school_title: 'required',
                position: 'isSelectBoxEmpty',
                policy: 'required',
                date: {
                    fieldsGroupValidation: {
                        group: 'date',
                    }
                }
            },
        };
        $.extend(options, window.helpBlock);

        return $(this.mapper.form).validate(options);
    }
}

const careerPageValidation = new CareerPageValidation();

Object.freeze(careerPageValidation);

export default careerPageValidation;