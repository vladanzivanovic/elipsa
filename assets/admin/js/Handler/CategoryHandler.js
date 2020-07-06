import AppHelperService from "../../../js/Helper/AppHelperService";
import NotificationService from "../../../js/NotificationService";
import CategoryDataTables from "../Services/DataTables/CategoryDataTables";

class CategoryHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper) {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_category_api');
        let type = 'POST';
        const data = mapper.form.serializeArray();

        if (! mapper.form.valid()) {
            return false;
        }

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_category_api', {slug: SLUG});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.categories'));
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
            url: AppHelperService.generateLocalizedUrl('admin.remove_category_api', {slug}),
            dataType: 'json',
            success: () => {
                CategoryDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                const errors = jxHR.responseJSON;

                if (errors.hasOwnProperty('message')) {
                    this.notification.show('error', Translator.trans(errors.message, {item: 'Kategorija'}, 'messages', LOCALE), true);

                    return;
                }

                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE+'_RS'), true);
            }
        })
    }

    toggleShowHomePage(slug, status) {
        $.ajax({
            type: 'PATCH',
            'url': AppHelperService.generateLocalizedUrl('admin.api_category_change_home_page', {slug, status}),
            dataType: 'json',
            success: () => {},
            error: () => {
                const errors = error.responseJSON;

                if (errors.hasOwnProperty('message')) {
                    this.notification.show('error', errors.message, true);

                    return;
                }

                this.notification.show('error', Translator.trans('generic_error', null, 'message', LOCALE), true);
            }
        })
    }
}

export default CategoryHandler;