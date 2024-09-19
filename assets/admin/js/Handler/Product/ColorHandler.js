import AppHelperService from "../../../../js/Helper/AppHelperService";
import NotificationService from "../../../../js/NotificationService";
import ColorsDataTables from "../../Services/DataTables/ColorsDataTables";
import colorEditMapper from "../../Mapper/ColorEditMapper";
import FormHelperService from "../../../../js/Helper/FormHelperService";

class ColorHandler {
    #notification;
    #mapper;

    constructor() {
        this.#notification = NotificationService();
        this.#mapper = colorEditMapper;
    }

    save(mapper) {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_color_api');
        let type = 'POST';
        const data = FormHelperService.formToJson($(this.#mapper.form));

        if (! $(this.#mapper.form).valid()) {
            return false;
        }

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_color_api', {id: ID});
            type = 'PUT';
        }

        this.#notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.colors'));
            },
            error: error => {
                const errors = error.responseJSON;

                if (errors.hasOwnProperty('message')) {
                    this.#notification.show('error', errors.message, true);

                    return;
                }

                this.#notification.show('error', Translator.trans('generic_error', null, 'messages'. LOCALE), true);
            }
        })
    }

    remove(id) {
        this.#notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_color_api', {id}),
            success: () => {
                ColorsDataTables().reload();
                this.#notification.remove();
            },
            error: (error) => {

                const errors = error.responseJSON;

                if (errors.hasOwnProperty('error')) {
                    this.#notification.show('error', errors.error.message, true);

                    return;
                }

                this.#notification.show('error', Translator.trans('generic_error', null, 'messages'. LOCALE), true);
            }
        })
    }
}

export default ColorHandler;
