import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import DropZoneService from "../../../js/Services/DropZoneService";
import HomeBannersDataTables from "../Services/DataTables/HomeBannersDataTables";
import catalogMapper from "../Mapper/CatalogMapper";
import CatalogDataTables from "../Services/DataTables/CatalogDataTables";

class CatalogHandler {
    constructor() {
        this.mapper = catalogMapper;
        this.notification = NotificationService();
    }

    save(id) {
        let urlRoute = Routing.generate('admin.add_catalog_api');
        let type = 'POST';
        const data = $(this.mapper.form).serializeArray();

        data.push({
            name: 'images',
            value: JSON.stringify(DropZoneService().getFilesArray('catalog')),
        });

        if (IS_EDIT) {
            urlRoute = Routing.generate('admin.edit_catalog_api', {id: ID});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.catalog_page'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    remove(id) {
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_catalog_api', {id}),
            dataType: 'json',
            success: () => {
                CatalogDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    changeStatus(checkbox, id, status) {
        $.ajax({
            type: 'PATCH',
            'url': Routing.generate('admin.api_toggle_catalog_status', {id, status}),
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

export default CatalogHandler;