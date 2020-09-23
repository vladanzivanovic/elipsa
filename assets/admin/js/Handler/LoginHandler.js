import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import loginPageMapper from "../Mapper/LoginPageMapper";

class LoginHandler {
    constructor() {
        this.mapper = loginPageMapper;
        this.notification = NotificationService();
    }

    doLogin() {
        const urlRoute = Routing.generate(`admin_api.login`);
        const type = 'POST';
        const data = {
            username: $(this.mapper.loginEmail).val(),
            password: $(this.mapper.loginPassword).val(),
        };

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            contentType: 'application/json',
            url: urlRoute,
            data: JSON.stringify(data),
            dataType: 'json',
            success: (response) => {
                AppHelperService.redirect(Routing.generate('admin.dashboard'));
            },
            error: (error) => {
                this.notification.show('error', Translator.trans(error.responseJSON.message, null, 'messages', LOCALE), true);
            }
        })
    }
}

export default LoginHandler;