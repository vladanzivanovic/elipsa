class LoginApiHandler {
    constructor() {
        if (!LoginApiHandler.instance) {

            LoginApiHandler.instance = this;
        }

        return LoginApiHandler.instance;
    }

    async send(data)
    {
        const urlRoute = Routing.generate(`site_api.login.${LOCALE}`);

        return $.ajax({
            type: 'POST',
            url: urlRoute,
            data: JSON.stringify(data),
        });
    }
}

const loginApiHandler = new LoginApiHandler();

Object.freeze(loginApiHandler);

export default loginApiHandler;
