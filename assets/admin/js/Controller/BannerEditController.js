import DropZoneService from "../../../js/Services/DropZoneService";
import bannerEditValidator from "../Validators/BannerEditValidator";
import bannerEditMapper from "../Mapper/BannerEditMapper";
import bannerEditEvents from "../Event/BannerEditEvents";

require ('select2/dist/js/select2.full.min');

class BannerEditController {
    #mapper;
    #dropZoneBanner;
    #dropZoneBannerMobile;
    constructor() {
        this.#mapper = bannerEditMapper;
        this.validator = bannerEditValidator;

        this.#dropZoneBanner = DropZoneService();
        this.#dropZoneBannerMobile = DropZoneService();

        this.#initializeForm();

        bannerEditEvents.registerEvents();
    }

    #initializeForm()
    {
        this.#dropZoneBanner.init($('[data-files="banner"]'));
        this.#dropZoneBannerMobile.init($('[data-files="banner_mobile"]'));

        $(`${this.#mapper.form} select`).select2({
            minimumResultsForSearch: -1
        });

        this.#initializeEdit();

        this.validator.validate();
    }

    #initializeEdit()
    {
        if (IS_EDIT) {
            this.#dropZoneBanner.setFiles(IMAGES.desktop, 'banner');
            if (IMAGES.mobile) {
                this.#dropZoneBannerMobile.setFiles(IMAGES.mobile, 'banner_mobile');
            }
        }
    }
}

export default BannerEditController;
