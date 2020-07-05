import DropZoneService from "../../../js/Services/DropZoneService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import NotificationService from "../../../js/NotificationService";
import BlogDataTables from "../Services/DataTables/BlogDataTables";

class BlogEditHandler {
    constructor() {
        this.notification = NotificationService();
    }

    save(mapper)
    {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_blog_api');
        let type = 'POST';
        const data = mapper.form.serializeArray();

        if (!mapper.form.valid()) {
            return false;
        }

        data.push({
            name: 'images',
            value: JSON.stringify(DropZoneService().getFilesArray('blog')),
        });

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_blog_api', {id: ID});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.blog'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    changeStatus(checkbox, id, status) {
        const notification = NotificationService();

        $.ajax({
            type: "PATCH",
            url: Routing.generate('admin.set_blog_status_api', {id, status}),
            dataType: 'json',
            success(response) {
                checkbox.parentElement.firstElementChild.innerText = Translator.trans(response.text, null, 'messages', LOCALE);
            },
            error(error) {
                notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE));
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

export default BlogEditHandler;