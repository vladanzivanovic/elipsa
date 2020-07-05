import DropZoneService from "../../../../js/Services/DropZoneService";
import AppHelperService from "../../../../js/Helper/AppHelperService";
import NotificationService from "../../../../js/NotificationService";
import TagsDataTables from "../../Services/DataTables/TagsDataTables";
import ProductDataTables from "../../Services/DataTables/ProductDataTables";

class ProductEditHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper) {
        let urlRoute = Routing.generate('admin.add_product_api');
        let type = 'POST';
        const data = mapper.form.serializeArray();

        data.push({
            name: 'images',
            value: JSON.stringify(DropZoneService().getFilesArray('mainImages')),
        });

        if (IS_EDIT) {
            urlRoute = Routing.generate('admin.edit_product_api', {slug: SLUG});
            type = 'PUT';
        }

        if (! mapper.form.valid()) {
            return false;
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: () => {
                AppHelperService.redirect(Routing.generate('admin.dashboard'));
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
            'url': Routing.generate('admin.api_product_change_status', {slug, status}),
            dataType: 'json',
            success: (response) => {
                checkbox.parentElement.firstElementChild.innerText = Translator.trans(response.text, null, 'messages', LOCALE);
            },
            error: () => {
                this.notification.show('error', Translator.trans('generic_error', null, 'message', LOCALE), true);
            }
        })
    }

    remove(slug) {
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: Routing.generate(`admin.remove_product_api`, {slug}),
            success: () => {
                ProductDataTables().reload();
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

export default ProductEditHandler;