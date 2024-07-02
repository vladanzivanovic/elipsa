import AppHelperService from "../../../../js/Helper/AppHelperService";
import NotificationService from "../../../../js/NotificationService";
import TagsDataTables from "../../Services/DataTables/TagsDataTables";
import tagEditMapper from "../../Mapper/TagEditMapper";
import FormHelperService from "../../../../js/Helper/FormHelperService";

class TagHandler {
    #mapper;
    #notification;
    
    constructor() {
        this.#mapper = tagEditMapper;
        this.#notification = NotificationService();
    }

    save(mapper) {
        let urlRoute = AppHelperService.generateLocalizedUrl(`admin.add_${TAG_TYPE}_tag_api`);
        let type = 'POST';
        const data = FormHelperService.formToJson($(this.#mapper.form));

        if (! $(this.#mapper.form).valid()) {
            return false;
        }

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl(`admin.edit_${TAG_TYPE}_tag_api`, {slug: SLUG});
            type = 'PUT';
        }

        this.#notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl(`admin.${TAG_TYPE}_tags`));
            },
            error: error => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'messages'. LOCALE), true);
            }
        })
    }

    remove(slug) {
        this.#notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl(`admin.remove_${TAG_TYPE}_tag_api`, {slug}),
            success: () => {
                TagsDataTables().reload();
                this.#notification.remove();
            },
            error: (error) => {
                const errors = error.responseJSON;

                if (errors.hasOwnProperty('message')) {
                    this.#notification.show('error', errors.message, true);

                    return;
                }

                this.#notification.show('error', Translator.trans('generic_error', null, 'messages'. LOCALE), true);
            }
        })
    }
}

export default TagHandler;
