import registrationMapper from "../Mapper/RegistrationMapper";
import registrationHandler from "../Handler/Registration/RegistrationHandler";

class RegistrationEvents {
    #mapper;
    #handler;

    constructor() {
        if (!RegistrationEvents.instance) {
            this.#mapper = registrationMapper;
            this.#handler = registrationHandler;

            RegistrationEvents.instance = this;
        }

        return RegistrationEvents.instance;
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click', e => {
            e.preventDefault();
            e.stopPropagation();

            this.#handler.save();
        });
    }
}
const registrationEvents = new RegistrationEvents();

Object.freeze(registrationEvents);

export default registrationEvents;
