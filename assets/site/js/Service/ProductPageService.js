import productPageMapper from "../Mapper/ProductPageMapper";
import Fotorama from "./Fotorama";
import Tipped from "@staaky/tipped";


require('flexslider');
require('jquery-cropper');

class ProductPageService {
    #mapper;
    #fotorama;

    constructor() {
        if (!ProductPageService.instance) {
            this.#mapper = productPageMapper;
            this.#fotorama = new Fotorama($('.photo-gallery'), 'vertical');

            ProductPageService.instance = this;
        }

        return ProductPageService.instance;
    }

    init() {
        this.showImagesByColor($(this.#mapper.colorActive));

        Tipped.create('.cleaning-icons');

        this.#fotorama.registerEvents();
    }

    showImagesByColor(colorElm) {
        const colorId = colorElm.data('color');
        const data = [];

        for (const image of MEDIA.images) {
            if (colorId === image.color_id) {
                data.push({
                    img: image.file,
                    thumb: image.file_thumb,
                    full: image.file_full,
                });
            }
        }

        for (const youtube of MEDIA.youtubes) {
            const $image = $('<img />', {
                src: youtube.Thumbnails.standard.url
            });

            $image.appendTo('body');


            $image.cropper({
                aspectRatio: 16 / 9,
                crop: function(event) {
                    console.log(event.detail.x);
                    console.log(event.detail.y);
                    console.log(event.detail.width);
                    console.log(event.detail.height);
                    console.log(event.detail.rotate);
                    console.log(event.detail.scaleX);
                    console.log(event.detail.scaleY);
                }
            });
            data.push({
                video: youtube.link,
                img: youtube.Thumbnails.standard.url,
                full: youtube.Thumbnails.maxres.url,
                thumb: youtube.Thumbnails.default.url,
            });
        }

        this.#fotorama.load(data);

        $(this.#mapper.colorActive).removeClass('active');
        colorElm.addClass('active');
    }

    toggleActiveSize(elm) {
        const selectedSize = $(elm);

        $(this.#mapper.sizeActive).removeClass('active');

        selectedSize.addClass('active');

        if (selectedSize.data('quantity') === 0) {
            $(this.#mapper.cartBtnWrapper).addClass('hide');

            $(this.#mapper.notifyMeBtnWrapper).removeClass('hide');

            $(this.#mapper.notifyMeInput).val('');

            return;
        }

        $(this.#mapper.cartBtnWrapper).removeClass('hide');

        $(this.#mapper.notifyMeBtnWrapper).addClass('hide');
    }
}

const productPageService = new ProductPageService();

Object.freeze(productPageService);

export default productPageService;
