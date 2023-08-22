import AppHelperService from "../../../../js/Helper/AppHelperService";
import NotificationService from "../../../../js/NotificationService";
import TagsDataTables from "../../Services/DataTables/TagsDataTables";

class TagHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper) {
        let urlRoute = AppHelperService.generateLocalizedUrl(`admin.add_${ROUTE_SUB_NAME}_tag_api`);
        let type = 'POST';
        const data = mapper.form.serializeArray();

        if (! mapper.form.valid()) {
            return false;
        }

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl(`admin.edit_${ROUTE_SUB_NAME}_tag_api`, {slug: SLUG});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl(`admin.${ROUTE_SUB_NAME}_tags`));
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
            url: AppHelperService.generateLocalizedUrl(`admin.remove_${ROUTE_SUB_NAME}_tag_api`, {slug}),
            success: () => {
                TagsDataTables().reload();
                this.notification.remove();
            },
            error: (error) => {
                const errors = error.responseJSON;

                if (errors.hasOwnProperty('message')) {
                    this.notification.show('error', errors.message, true);

                    return;
                }

                this.notification.show('error', Translator.trans('generic_error', null, 'messages'. LOCALE), true);
            }
        })
    }
}

export default TagHandler;
