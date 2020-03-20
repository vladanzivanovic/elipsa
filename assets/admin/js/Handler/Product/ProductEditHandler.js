import DropZoneService from "../../../../js/Services/DropZoneService";
import AppHelperService from "../../../../site/js/AppHelperService";
import NotificationService from "../../../../js/NotificationService";

class ProductEditHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper) {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_product_api');
        let type = 'POST';
        const data = mapper.form.serializeArray();

        data.push({
            name: 'images',
            value: JSON.stringify(DropZoneService().getFilesArray('mainImages')),
        });

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_product_api', {slug: SLUG});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: () => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.dashboard'));
            },
            error: () => {
                let errors = error.responseJSON;

                if (!AppHelperService.isJsonString(errors.error)) {
                    this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
                }
            }
        })
    }

    changeStatus(checkbox, slug, status) {
        $.ajax({
            type: 'PATCH',
            'url': AppHelperService.generateLocalizedUrl('admin.api_product_change_status', {slug, status}),
            dataType: 'json',
            success: (response) => {
                checkbox.parentElement.firstElementChild.innerText = Translator.trans(response.text, null, 'messages', LOCALE);
            },
            error: () => {
                this.notification.show('error', Translator.trans('generic_error', null, 'message', LOCALE), true);
            }
        })
    }
}

export default ProductEditHandler;