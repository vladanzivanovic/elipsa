import NotificationService from "../../../js/NotificationService";
import loyaltyPageMapper from "../Mapper/LoyaltyPageMapper";
import loader from "../Dom/LoaderDom";
import AppHelperService from "../../../js/Helper/AppHelperService";
import FormHelperService from "../../../js/Helper/FormHelperService";

class UserHandler {
    constructor() {
        this.mapper = loyaltyPageMapper;
        this.notification = NotificationService();
    }

    doRegistration(form) {
        let urlRoute = Routing.generate(`site_api.user_registration.${LOCALE}`);
        let type = 'POST';
        const data = $(form).serializeArray();

        if (! $(form).valid()) {
            return false;
        }

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                AppHelperService.redirect('reload');
                this.notification.show('success', Translator.trans(`registration.success.message`, null, 'messages', LOCALE), true);
                loader.hide();
            },
            error: (error) => {
                this.notification.show('error', Translator.trans(`registration.error.user_exists`, null, 'messages', LOCALE), true);
                loader.hide();
            }
        })
    }

    doLogin(mapper) {
        const urlRoute = Routing.generate(`site_api.login`);
        const type = 'POST';
        const data = {
            username: $(mapper.loginEmail).val(),
            password: $(mapper.loginPassword).val(),
        };

        loader.show();

        $.ajax({
            type,
            contentType: 'application/json',
            url: urlRoute,
            data: JSON.stringify(data),
            dataType: 'json',
            success: (response) => {
                AppHelperService.redirect('reload');
            },
            error: (error) => {
                this.notification.show('error', Translator.trans(error.responseJSON.message, null, 'messages', LOCALE), true);
                loader.hide();
            }
        })
    }

    doResetPassword(mapper) {
        const urlRoute = Routing.generate(`site_api.user_ask_for_reset_password`);
        const type = 'PATCH';
        const data = $(mapper.resetForm).serializeArray();

        if (! $(mapper.resetForm).valid()) {
            return false;
        }

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                AppHelperService.redirect('reload');
            },
            error: (error) => {
                this.notification.show('error', Translator.trans(error.responseJSON.message, null, 'messages', LOCALE), true);
                loader.hide();
            }
        })
    }

    doUpdate(form) {
        let urlRoute = Routing.generate(`site_api.user_update.${LOCALE}`, {id: USER_ID});
        let type = 'PUT';
        const data = FormHelperService.sanitize($(form).serializeArray());

        if (! $(form).valid()) {
            return false;
        }

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                AppHelperService.redirect('reload');
            },
            error: (error) => {
                this.notification.show('error', Translator.trans(error.error, null, 'messages', LOCALE), true);
                loader.hide();
            }
        })
    }
}

export default UserHandler;