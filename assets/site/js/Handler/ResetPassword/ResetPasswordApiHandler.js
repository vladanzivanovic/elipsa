class ResetPasswordApiHandler {
    constructor() {
        if (!ResetPasswordApiHandler.instance) {

            ResetPasswordApiHandler.instance = this;
        }

        return ResetPasswordApiHandler.instance;
    }

    async send(data)
    {
        const urlRoute = Routing.generate(`site_api.user_ask_for_reset_password`);

        return $.ajax({
            type: 'PATCH',
            url: urlRoute,
            data: JSON.stringify(data),
        });
    }

    async sendNewPassword(data)
    {
        const urlRoute = Routing.generate(`site_api.user_reset_password`);

        return $.ajax({
            type: 'PUT',
            url: urlRoute,
            data: JSON.stringify(data),
        });
    }
}

const resetPasswordApiHandler = new ResetPasswordApiHandler();

Object.freeze(resetPasswordApiHandler);

export default resetPasswordApiHandler;
