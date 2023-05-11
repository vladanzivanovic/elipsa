import DropZoneService from "../../../js/Services/DropZoneService";
import BannerEditMapper from "../Mapper/BannerEditMapper";
import BannerHandler from "../Handler/BannerHandler";
import bannerEditValidator from "../Validators/BannerEditValidator";

class BannerEditController {
    constructor() {
        this.mapper = new BannerEditMapper();
        this.validator = bannerEditValidator;

        this.dropZoneBanner = DropZoneService();
        this.dropZoneBannerMobile = DropZoneService();
        this.dropZoneBanner.init($('[data-files="banner"]'));
        this.dropZoneBannerMobile.init($('[data-files="banner_mobile"]'));

        if (IS_EDIT) {
            this.dropZoneBanner.setFiles(IMAGES.desktop, 'banner');
            if (IMAGES.mobile) {
                this.dropZoneBannerMobile.setFiles(IMAGES.mobile, 'banner_mobile');
            }
        }

        this.validator.validate(this.mapper.form);

        this.registerEvents();

        $('#banner-select-box').trigger('change');
    }

    registerEvents() {
        this.mapper.submitBtn.on('click touchend', e => {
            const handler = new BannerHandler();

            handler.save(this.mapper);
        });

        $(document).on('change', '#banner-select-box', e => {
            const type = parseInt($(e.currentTarget).val());

            if (type === BANNER_TYPES.TYPE_LOYALTY || type === BANNER_TYPES.TYPE_NEWS_LETTER) {
                this.#removeLinks();
                this.#showMobileDropZone();

                return;
            }

            if (type === BANNER_TYPES.TYPE_POP_UP) {
                this.#removeButtons();
                this.#showLinks();
                this.#showMobileDropZone();

                return;
            }

            if (type === BANNER_TYPES.TYPE_MENU || type === BANNER_TYPES.TYPE_SEASON) {
                this.#showLinks();
                this.#removeButtons();
                this.#removeMobileDropzone();

                return;
            }

            this.#showButtons();
            this.#showLinks();
            this.#showMobileDropZone();

        });
    }

    #removeLinks()
    {
        $('.links').fadeOut();
        $('.links').addClass('hide');
    }

    #showLinks()
    {
        $('.links').fadeIn();
        $('.links').removeClass('hide');
    }

    #removeButtons()
    {
        $('.btn-text').fadeOut();
        $('.btn-text').addClass('hide');
    }

    #showButtons()
    {
        $('.btn-text').fadeIn();
        $('.btn-text').removeClass('hide');
    }

    #removeMobileDropzone()
    {
        $('.mobile-image').fadeOut();
        $('.mobile-image').addClass('hide');
    }

    #showMobileDropZone()
    {
        $('.mobile-image').fadeIn();
        $('.mobile-image').removeClass('hide');
    }
}

export default BannerEditController;
