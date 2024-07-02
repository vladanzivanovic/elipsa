import productEditDom from "../Dom/ProductEditDom";
import productEditMapper from "../Mapper/ProductEditMapper";

class BannerEditManipulator {
    constructor() {
        if(!BannerEditManipulator.instance) {
            BannerEditManipulator.instance = this;
        }

        return BannerEditManipulator.instance;
    }

    removeLinks()
    {
        $('.links').fadeOut();
    }

    showLinks()
    {
        $('.links').fadeIn();
    }

    removeButtons()
    {
        $('.btn-text').fadeOut();
    }

    showButtons()
    {
        $('.btn-text').fadeIn();
    }

    removeMobileDropzone()
    {
        $('.mobile-image').fadeOut();
    }

    showMobileDropZone()
    {
        $('.mobile-image').fadeIn();
    }
}

const bannerEditManipulator = new BannerEditManipulator();

Object.freeze(bannerEditManipulator);

export default bannerEditManipulator;
