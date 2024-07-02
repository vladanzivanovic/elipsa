class AskUsApiHandler {
    constructor() {
        if (!AskUsApiHandler.instance) {

            AskUsApiHandler.instance = this;
        }

        return AskUsApiHandler.instance;
    }

    async send(data)
    {
        const urlRoute = Routing.generate(`site_api.ask_us.${LOCALE}`);

        return $.ajax({
            type: 'POST',
            url: urlRoute,
            data: JSON.stringify(data),
        });
    }
}

const askUsApiHandler = new AskUsApiHandler();

Object.freeze(askUsApiHandler);

export default askUsApiHandler;
