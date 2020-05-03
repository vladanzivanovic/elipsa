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

    toggleActiveSize(elm) {
        $('.size-btn.active').removeClass('active');

        $(elm).addClass('active');
    }

    toggleActivationShopButton() {
        const color = $('.color-btn.active');
        const size = $('.size-btn.active');
        const quantity = this.mapper.quantity.val();

        if (color.length > 0 && size.length > 0 && quantity > 0) {
            this.mapper.addBtn.removeClass('disabled');

            return true;
        }

        this.mapper.addBtn.addClass('disabled');
    }
}

export default ProductPageService;