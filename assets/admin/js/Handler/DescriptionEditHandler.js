import DropZoneService from "../../../js/Services/DropZoneService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import NotificationService from "../../../js/NotificationService";
import BlogDataTables from "../Services/DataTables/BlogDataTables";

class DescriptionEditHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper)
    {
        let urlRoute = Routing.generate('admin.add_description_api');
        let type = 'POST';
        const data = mapper.form.serializeArray();

        if (IS_EDIT) {
            urlRoute = Routing.generate('admin.edit_description_api', {id: ID});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(Routing.generatePaths('admin.descriptions'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    remove(id) {
        const notification = NotificationService();

        $.ajax({
            type: 'DELETE',
            url: Routing.generate('admin.remove_blog_api', {id}),
            dataType: 'json',
            success() {
                BlogDataTables().reload();
            },
            error(error) {
                notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE));
            }
        })
    }
}

export default DescriptionEditHandler;