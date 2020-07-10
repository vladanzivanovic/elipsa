import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import sliderTextEditMapper from "../Mapper/SliderTextEditMapper";
import SliderTextDataTables from "../Services/DataTables/SliderTextDataTables";

class SliderTextHandler {
    constructor() {
        this.mapper = sliderTextEditMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = Routing.generate('admin.add_slider_text_api');
        let type = 'POST';
        const data = $(this.mapper.form).serializeArray();

        if (IS_EDIT) {
            urlRoute = Routing.generate('admin.edit_slider_text_api', {id: ID});
            type = 'PUT';
        }

        if (! $(this.mapper.form).valid()) {
            return false;
        }

        this.notification.remove();
        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(Routing.generate('admin.slider_text'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    changeStatus(checkbox, id, status) {
        this.notification.remove();
        $.ajax({
            type: 'PATCH',
            'url': Routing.generate('admin.api_toggle_slider_text_status', {id, status}),
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
        this.notification.remove();
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_slider_text_api', {id}),
            dataType: 'json',
            success: () => {
                SliderTextDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default SliderTextHandler;