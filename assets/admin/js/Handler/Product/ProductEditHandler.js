import DropZoneService from "../../../../js/Services/DropZoneService";
import AppHelperService from "../../../../js/Helper/AppHelperService";
import toastrService from "../../../../js/Services/ToastrService";
import productEditMapper from "../../Mapper/ProductEditMapper";
import productDataTables from "../../Services/DataTables/ProductDataTables";

class ProductEditHandler {
    #mapper;
    #notification;
    #youtube;
    #dataTable;

    constructor(youtube) {
        this.#mapper = productEditMapper;
        this.#notification = toastrService;
        this.#youtube = youtube;
        this.#dataTable = productDataTables;
    }

    save() {
        let urlRoute = Routing.generate('admin.add_product_api');
        let type = 'POST';
        const data = $(this.#mapper.form).serializeArray();

        data.push({
            name: 'images',
            value: JSON.stringify(DropZoneService().getFilesArray('mainImages')),
        });

        if (IS_EDIT) {
            urlRoute = Routing.generate('admin.edit_product_api', {slug: SLUG});
            type = 'PUT';
        }

        if (! $(this.#mapper.form).valid()) {
            return false;
        }

        for (const youtube of this.#youtube.getLists()) {
            if (!youtube.isDeleted) {
                data.push({
                    name: 'youtube[]',
                    value: JSON.stringify(youtube)
                });
            }
        }

        this.#notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: () => {
                AppHelperService.redirect(Routing.generate('admin.dashboard'));
            },
            error: () => {
                let errors = error.responseJSON;

                if (!AppHelperService.isJsonString(errors.error)) {
                    this.#notification.error(Translator.trans('generic_error', null, 'messages', LOCALE));
                }
            }
        })
    }

    changeStatus(slug, status) {
        $.ajax({
            type: 'PATCH',
            'url': Routing.generate('admin.api_product_change_status', {slug, status}),
            dataType: 'json',
            success: (response) => {
                this.#dataTable.reload();
            },
            error: () => {
                this.#notification.error(Translator.trans('generic_error', null, 'message', LOCALE));
            }
        })
    }

    changeHomePagePosition(checkbox, slug, status) {
        $.ajax({
            type: 'PATCH',
            'url': Routing.generate('admin.api_product_home_page_position', {slug, status}),
            dataType: 'json',
            success: (response) => {},
            error: () => {
                checkbox.prop('checked', false);

                this.#notification.error(Translator.trans('generic_error', null, 'message', LOCALE));
            }
        })
    }

    remove(slug) {
        this.#notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: Routing.generate(`admin.remove_product_api`, {slug}),
            success: () => {
                this.#dataTable.reload();
                this.#notification.remove();
            },
            error: (error) => {
                const errors = error.responseJSON;

                if (errors.hasOwnProperty('message')) {
                    this.#notification.error(errors.message);

                    return;
                }

                this.#notification.error(Translator.trans('generic_error', null, 'messages'. LOCALE));
            }
        })
    }
}

export default ProductEditHandler;
