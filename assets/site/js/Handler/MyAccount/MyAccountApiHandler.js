class MyAccountApiHandler {
    constructor() {
        if (!MyAccountApiHandler.instance) {

            MyAccountApiHandler.instance = this;
        }

        return MyAccountApiHandler.instance;
    }

    async send(data)
    {
        const urlRoute = Routing.generate(`site_api.user.update`, {id: USER.id});

        return $.ajax({
            type: 'PUT',
            url: urlRoute,
            data: JSON.stringify(data),
        });
    }
}

const myAccountApiHandler = new MyAccountApiHandler();

Object.freeze(myAccountApiHandler);

export default myAccountApiHandler;
