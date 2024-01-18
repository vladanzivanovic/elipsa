import NotificationService from "../../../js/NotificationService";
import loader from "../Dom/LoaderDom";
import AppHelperService from "../../../js/Helper/AppHelperService";
import resetPasswordPageMapper from "../Mapper/ResetPasswordPageMapper";
import FormHelperService from "../../../js/Helper/FormHelperService";

class ResetPasswordPageHandler {
    constructor() {
        this.mapper = resetPasswordPageMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = Routing.generate(`site_api.user_reset_password.${LOCALE}`);
        let type = 'PUT';
        const data = FormHelperService.sanitize($(this.mapper.form).serializeArray());

        if (! $(this.mapper.form).valid()) {
            return false;
        }

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                this.notification.show('success', Translator.trans(`reset_password.page.success`, null, 'messages', LOCALE), true);
                AppHelperService.redirect(Routing.generate(`site.home_page`));
                loader.hide();
            },
            error: (error) => {
                loader.hide();
            }
        })
    }
}

export default ResetPasswordPageHandler;
