import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import DropZoneService from "../../../js/Services/DropZoneService";
import SizesDataTables from "../Services/DataTables/SizesDataTables";
import SliderDataTables from "../Services/DataTables/SliderDataTables";

class SliderHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper) {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_slider_api');
        let type = 'POST';
        const data = mapper.form.serializeArray();

        if (! mapper.form.valid()) {
            return false;
        }

        data.push({
            name: 'images',
            value: JSON.stringify(DropZoneService().getFilesArray('slider')),
        });

        data.push({
            name: 'images_mobile',
            value: JSON.stringify(DropZoneService().getFilesArray('slider_mobile')),
        });

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_slider_api', {id: ID});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.sliders'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    changeStatus(checkbox, id, status) {
        $.ajax({
            type: 'PATCH',
            'url': AppHelperService.generateLocalizedUrl('admin.api_toggle_slider_status', {id, status}),
            dataType: 'json',
            success: (response) => {
                checkbox.parentElement.firstElementChild.innerText = Translator.trans(response.text, null, 'messages', LOCALE);
            },
            error: () => {
                this.notification.show('error', Translator.trans('generic_error', null, 'message', LOCALE), true);
            }
        })
    }

    remove(id) {
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_slider_api', {id}),
            dataType: 'json',
            success: () => {
                SliderDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default SliderHandler;