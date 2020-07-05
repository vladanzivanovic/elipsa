import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import DropZoneService from "../../../js/Services/DropZoneService";
import HomeBannersDataTables from "../Services/DataTables/HomeBannersDataTables";
import locationEditMapper from "../Mapper/LocationEditMapper";
import LocationDataTables from "../Services/DataTables/LocationDataTables";

class LocationHandler {
    constructor() {
        this.mapper = locationEditMapper;

        this.notification = NotificationService();
    }

    save() {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_location_api');
        let type = 'POST';
        const data = $(this.mapper.form).serializeArray();

        data.push({
            name: 'images',
            value: JSON.stringify(DropZoneService().getFilesArray('location')),
        });

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_location_api', {id: ID});
            type = 'PUT';
        }

        if (! $(this.mapper.form).valid()) {
            return false;
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.locations'));
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
            url: AppHelperService.generateLocalizedUrl('admin.remove_location_api', {id}),
            dataType: 'json',
            success: () => {
                LocationDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default LocationHandler;