import AppHelperService from "../../../../site/js/AppHelperService";
import NotificationService from "../../../../js/NotificationService";
import ColorsDataTables from "../../Services/DataTables/ColorsDataTables";

class ColorHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper) {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_color_api');
        let type = 'POST';
        const data = mapper.form.serializeArray();

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_color_api', {slug: SLUG});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.colors'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages'. LOCALE), true);
            }
        })
    }

    remove(slug) {
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_color_api', {slug}),
            success: () => {
                ColorsDataTables().reload();
                this.notification.remove();
            },
            error: (error) => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages'. LOCALE), true);
            }
        })
    }
}

export default ColorHandler;