import AppHelperService from "../../../js/Helper/AppHelperService";
import NotificationService from "../../../js/NotificationService";
import SizesDataTables from "../Services/DataTables/SizesDataTables";

class SizeHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper) {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_size_api');
        let type = 'POST';
        const data = mapper.form.serializeArray();

        if (! mapper.form.valid()) {
            return false;
        }

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_size_api', {slug: SLUG});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.sizes'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    remove(slug) {
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: Routing.generate('admin.remove_size_api', {slug}),
            dataType: 'json',
            success: () => {
                SizesDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                const errors = jxHR.responseJSON;

                if (errors.hasOwnProperty('message')) {
                    this.notification.show('error', Translator.trans(errors.message, {item: 'Veličina'}, 'messages', LOCALE), true);

                    return;
                }

                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default SizeHandler;