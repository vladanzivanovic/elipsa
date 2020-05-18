import AppHelperService from "../../../js/Helper/AppHelperService";
import NotificationService from "../../../js/NotificationService";

class SettingsPageController {
    constructor() {
        this.notification = NotificationService();

        this.registerEvents();
    }

    registerEvents() {
        $('#settings_submit').on('click touchend', e => {
            const data = $('#edit_form').serializeArray();

            this.notification.showLoadingMessage();

            $.ajax({
                type:   'POST',
                url:    AppHelperService.generateLocalizedUrl('admin.update_settings_api'),
                data,
                dataType: 'json',
                success: e => {
                    AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.settings_page'));
                },
                error: () => {
                    this.notification.show('error', Translator.trans('generic_error', null, 'message', LOCALE), true);
                }
            })
        });
    }
}

export default SettingsPageController;