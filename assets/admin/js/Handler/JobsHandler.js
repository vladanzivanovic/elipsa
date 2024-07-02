import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import DropZoneService from "../../../js/Services/DropZoneService";
import jobEditMapper from "../Mapper/JobEditMapper";
import FormHelperService from "../../../js/Helper/FormHelperService";
import jobsDataTables from "../Services/DataTables/JobsDataTables";

class JobsHandler {
    #mapper;
    #notification;
    #dataTable;
    constructor() {
        this.#mapper = jobEditMapper;
        this.#notification = NotificationService();
        this.#dataTable = jobsDataTables;
    }

    save() {
        let urlRoute = Routing.generate('admin.add_job_api');
        let type = 'POST';
        const data = FormHelperService.formToJson($(this.#mapper.form));
        const images = DropZoneService().getFilesArray('job');


        if (! $(this.#mapper.form).valid()) {
            return false;
        }

        data.images = [];

        for (const index in images) {
            data.images.push(images[index]);
        }

        if (IS_EDIT) {
            urlRoute = Routing.generate('admin.edit_job_api', {id: ID});
            type = 'PUT';
        }

        this.#notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(Routing.generate('admin.jobs'));
            },
            error: error => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    changeStatus(checkbox, id, status) {
        $.ajax({
            type: 'PATCH',
            'url': AppHelperService.generateLocalizedUrl('admin.set_job_status_api', {id, status}),
            dataType: 'json',
            success: (response) => {
                checkbox.parentElement.firstElementChild.innerText = Translator.trans(response.text, null, 'messages', LOCALE);
            },
            error: () => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'message', LOCALE), true);
            }
        })
    }

    remove(id) {
        this.#notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_job_api', {id}),
            dataType: 'json',
            success: () => {
                this.#dataTable.reload();
                this.#notification.remove();
            },
            error: jxHR => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default JobsHandler;
