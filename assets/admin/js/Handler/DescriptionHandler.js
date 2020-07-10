import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import DescriptionDataTables from "../Services/DataTables/DescriptionDataTables";
import descriptionEditMapper from "../Mapper/DescriptionEditMapper";

class DescriptionHandler {
    constructor() {
        this.mapper = descriptionEditMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = Routing.generate('admin.set_description_api');
        let type = 'POST';
        const data = $(this.mapper.form).serializeArray();

        if (IS_EDIT) {
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(Routing.generate('admin.descriptions'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    remove(type) {
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: Routing.generate('admin.remove_description_api', {type}),
            dataType: 'json',
            success: () => {
                DescriptionDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default DescriptionHandler;