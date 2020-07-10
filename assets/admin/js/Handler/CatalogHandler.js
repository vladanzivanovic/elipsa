import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import DropZoneService from "../../../js/Services/DropZoneService";
import HomeBannersDataTables from "../Services/DataTables/HomeBannersDataTables";
import catalogMapper from "../Mapper/CatalogMapper";

class CatalogHandler {
    constructor() {
        this.mapper = catalogMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.set_catalog');
        let type = 'POST';
        const data = [];

        data.push({
            name: 'images',
            value: JSON.stringify(DropZoneService().getFilesArray('catalog')),
        });

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
            url: AppHelperService.generateLocalizedUrl('admin.remove_banner_api', {id}),
            dataType: 'json',
            success: () => {
                HomeBannersDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default CatalogHandler;