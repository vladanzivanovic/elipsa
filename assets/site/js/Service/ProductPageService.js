import ProductPageMapper from "../Mapper/ProductPageMapper";
import Fotorama from "./Fotorama";

class ProductPageService {
    #mapper;
    #fotorama;

    constructor() {
        this.#mapper = ProductPageMapper;
        this.#fotorama = new Fotorama($('.photo-gallery'));
    }

    showImagesByColor(colorElm) {
        const colorId = colorElm.data('color');
        const data = [];

        for (const image of MEDIA.images) {
            if (colorId === image.color_id) {
                data.push({
                    img: image.file,
                    thumb: image.file_thumb,
                    full: image.file,
                });
            }
        }

        for (const youtube of MEDIA.youtubes) {
            data.push({
                video: youtube.link,
                img: youtube.Thumbnails.standard.url,
                full: youtube.Thumbnails.maxres.url,
                thumb: youtube.Thumbnails.default.url,
            });
        }

        this.#fotorama.load(data);
        //
        // $.each(MEDIA, (i, elm) => {
        //     const image = $(elm);
        //
        //     image.removeClass('in');
        //     image.removeClass('active');
        // });
        //
        // let isSetActiveImage = false;
        //
        // $.each(this.mapper.thumbImage, (i, elm) => {
        //     const image = $(elm);
        //     const imageColorId = image.data('color');
        //
        //     if (imageColorId == colorId) {
        //         image.removeClass('hide');
        //
        //         if (!isSetActiveImage) {
        //             image.addClass('active');
        //             $(`#images-${image.data('image')}`).addClass('in active');
        //             isSetActiveImage = true;
        //         }
        //
        //         return;
        //     }
        //
        //     image.addClass('hide');
        //     image.removeClass('active');
        // });
        //
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
        const quantity = this.#mapper.quantity.val();

        if (color.length > 0 && size.length > 0 && quantity > 0) {
            this.#mapper.addBtn.removeClass('disabled');

            return true;
        }

        this.mapper.addBtn.addClass('disabled');
    }
}

export default ProductPageService;
