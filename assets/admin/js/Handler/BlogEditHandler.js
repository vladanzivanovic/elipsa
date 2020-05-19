import DropZoneService from "../../../js/Services/DropZoneService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import BackendValidator from "../../../js/Validation/BackendValidator";
import NotificationService from "../../../js/Services/NotificationService";
import i18n from "../../../js/Translation";
import BlogDataTables from "../Services/DataTables/BlogDataTables";

class BlogEditHandler {
    static save(mapper, ajaxObject)
    {
        const data = mapper.form.serializeArray();
        const notification = NotificationService();

        if (false === mapper.form.valid()) {
            return false;
        }

        data.push({
            name: 'main_image',
            value: JSON.stringify(DropZoneService().getFilesArray('mainImages'))
        });

        ajaxObject.beforeSend = () => {
            notification.showLoadingMessage();
        };
        ajaxObject.data = data;
        ajaxObject.success = response => {
            notification.show('success', i18n.trans('data.success_send', null, 'messages'), true);
            notification.setOptions({
                onHidden: AppHelperService.redirect(Routing.generate('admin.blog_page'))
            });

            return true;
        };
        ajaxObject.error = (jqXHR, textStatus, errorThrow) => {
            var errors = jqXHR.responseJSON.error;

            if (!AppHelperService.isObject(errors)) {
                notification.show('error', i18n.trans('generic_error', null, 'messages'));

                return true;
            }

            BackendValidator().validate(mapper.form, errors);
        };

        $.ajax(ajaxObject);
    }

    static changeStatus(checkbox, slug, status) {
        const notification = NotificationService();

        $.ajax({
            type: "PATCH",
            url: Routing.generate('admin.api_set_status_blog', {slug, status}),
            dataType: 'json',
            success(response) {
                checkbox.parentElement.firstElementChild.innerText = i18n.trans(response.text, null, 'messages');
            },
            error(error) {
                notification.show('error', i18n.trans('generic_error', null, 'messages'));
            }
        })
    }

    static remove(slug) {
        const notification = NotificationService();

        $.ajax({
            type: 'DELETE',
            url: Routing.generate('admin.api_delete_blog', {slug}),
            dataType: 'json',
            success() {
                BlogDataTables().reload();
            },
            error(error) {
                notification.show('error', i18n.trans('generic_error', null, 'messages'));
            }
        })
    }
}

export default BlogEditHandler;