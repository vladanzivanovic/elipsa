import registrationMapper from "../Mapper/RegistrationMapper";
import registrationEvents from "../Events/RegistrationEvents";
import registrationValidator from "../Validators/RegistrationValidator";

require('inputmask/dist/jquery.inputmask.bundle');

class RegistrationController {
    #mapper;
    #event;
    #validator;

    constructor() {
        this.#mapper = registrationMapper;
        this.#event = registrationEvents;
        this.#validator = registrationValidator;

        this.init();

        this.#event.registerEvents();
    }

    init()
    {
        $(this.#mapper.fields.mobilePhone).inputmask({mask: '(999) 99 999-999[9]', autoUnmask: true, clearIncomplete: true});
        $(this.#mapper.fields.zipCode).inputmask({mask: '99999', autoUnmask: true, clearIncomplete: true});

        this.#validator.validate();
    }
}

export default RegistrationController;
