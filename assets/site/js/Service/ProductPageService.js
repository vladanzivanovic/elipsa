import ProductPageMapper from "../Mapper/ProductPageMapper";

class ProductPageService {
    constructor() {
        this.mapper = ProductPageMapper;
    }

    showImagesByColor(colorElm) {
        const colorId = colorElm.data('color');

        $.each(this.mapper.largeImage, (i, elm) => {
            const image = $(elm);

            image.removeClass('in');
            image.removeClass('active');
        });

        let isSetActiveImage = false;

        $.each(this.mapper.thumbImage, (i, elm) => {
            const image = $(elm);
            const imageColorId = image.data('color');

            if (imageColorId == colorId) {
                image.removeClass('hide');

                if (!isSetActiveImage) {
                    image.addClass('active');
                    $(`#images-${image.data('image')}`).addClass('in active');
                    isSetActiveImage = true;
                }

                return;
            }

            image.addClass('hide');
            image.removeClass('active');
        });

        $('.color-btn.active').removeClass('active');
        colorElm.addClass('active');
    }
}

export default ProductPageService;