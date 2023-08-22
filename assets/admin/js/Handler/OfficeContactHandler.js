import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import sliderTextEditMapper from "../Mapper/SliderTextEditMapper";
import SliderTextDataTables from "../Services/DataTables/SliderTextDataTables";
import OfficeContactEditMapper from "../Mapper/OfficeContactEditMapper";
import OfficeContactDataTables from "../Services/DataTables/OfficeContactDataTables";
import officeContactDataTables from "../Services/DataTables/OfficeContactDataTables";

class OfficeContactHandler {
    #mapper;
    #notification;
    #dataTable;

    constructor() {
        this.#mapper = OfficeContactEditMapper;
        this.#notification = NotificationService();
        this.#dataTable = officeContactDataTables;
    }

    save() {
        let urlRoute = Routing.generate('admin.add_office_contact_api');
        let type = 'POST';
        const data = $(this.#mapper.form).serializeArray();

        if (IS_EDIT) {
            urlRoute = Routing.generate('admin.edit_office_contact_api', {id: ID});
            type = 'PUT';
        }

        if (! $(this.#mapper.form).valid()) {
            return false;
        }

        this.#notification.remove();
        this.#notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(Routing.generate('admin.office_contacts'));
            },
            error: error => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    remove(id) {
        this.#notification.remove();
        this.#notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_office_contact_api', {id}),
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

export default OfficeContactHandler;
