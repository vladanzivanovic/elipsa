import DropZoneService from "../../../js/Services/DropZoneService";
import BannerEditMapper from "../Mapper/BannerEditMapper";
import BannerHandler from "../Handler/BannerHandler";

class BannerEditController {
    constructor() {
        this.mapper = new BannerEditMapper();

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

        this.registerEvents();

        $('#banner-select-box').trigger('change');
    }

    registerEvents() {
        this.mapper.submitBtn.on('click touchend', e => {
            const handler = new BannerHandler();

            handler.save(this.mapper);
        });

        $(document).on('change', '#banner-select-box', e => {
            const type = $(e.currentTarget).val();

            if (type == 2 || type == 3) {
                $('.links').fadeOut();

                return;
            }

            $('.links').fadeIn();
        });
    }
}

export default BannerEditController;