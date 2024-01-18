class RegistrationApiHandler {
    constructor() {
        if (!RegistrationApiHandler.instance) {

            RegistrationApiHandler.instance = this;
        }

        return RegistrationApiHandler.instance;
    }

    async register(data)
    {
        const urlRoute = Routing.generate(`site_api.user.register`);

        return $.ajax({
            type: 'POST',
            url: urlRoute,
            data: JSON.stringify(data),
        });
    }
}

const registrationApiHandler = new RegistrationApiHandler();

Object.freeze(registrationApiHandler);

export default registrationApiHandler;
