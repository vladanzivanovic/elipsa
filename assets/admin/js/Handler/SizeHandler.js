import AppHelperService from "../../../js/Helper/AppHelperService";
import NotificationService from "../../../js/NotificationService";
import SizesDataTables from "../Services/DataTables/SizesDataTables";
import sizeEditMapper from "../Mapper/SizeEditMapper";
import FormHelperService from "../../../js/Helper/FormHelperService";

class SizeHandler {
    #mapper;
    #notification;
    
    constructor() {
        this.#mapper = sizeEditMapper;
        this.#notification = NotificationService();
    }

    save() {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_size_api');
        let type = 'POST';
        const data = FormHelperService.formToJson($(this.#mapper.form));

        if (! $(this.#mapper.form).valid()) {
            return false;
        }

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_size_api', {id: ID});
            type = 'PUT';
        }

        this.#notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.sizes'));
            },
            error: error => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    remove(id) {
        this.#notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: Routing.generate('admin.remove_size_api', {id}),
            dataType: 'json',
            success: () => {
                SizesDataTables().reload();
                this.#notification.remove();
            },
            error: jxHR => {
                const errors = jxHR.responseJSON;

                if (errors.hasOwnProperty('error')) {
                    this.#notification.show('error', errors.error.message, true);

                    return;
                }

                this.#notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default SizeHandler;
