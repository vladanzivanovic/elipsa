import DropZoneService from "../../../js/Services/DropZoneService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import NotificationService from "../../../js/NotificationService";
import blogEditMapper from "../Mapper/BlogEditMapper";
import FormHelperService from "../../../js/Helper/FormHelperService";
import blogDataTables from "../Services/DataTables/BlogDataTables";

class BlogEditHandler {
    #mapper;
    #notification;
    #dataTable;

    constructor() {
        this.#mapper = blogEditMapper;
        this.#notification = NotificationService();
        this.#dataTable = blogDataTables;
    }

    save()
    {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_blog_api');
        let type = 'POST';
        const data = FormHelperService.formToJson($(this.#mapper.form));
        const images = DropZoneService().getFilesArray('blog');

        if (!$(this.#mapper.form).valid()) {
            return false;
        }

        data.images = [];

        for (const index in images) {
            data.images.push(images[index]);
        }

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_blog_api', {id: ID});
            type = 'PUT';
        }

        this.#notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.blog'));
            },
            error: error => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
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
                this.#dataTable.reload();
                this.#notification.remove();
            },
            error(error) {
                notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE));
            }
        })
    }
}

export default BlogEditHandler;
