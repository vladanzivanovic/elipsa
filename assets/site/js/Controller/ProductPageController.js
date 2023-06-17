import ProductPageHandler from "../Handler/ProductPageHandler";
import Tipped from "@staaky/tipped";
require('@fancyapps/fancybox');

import ProductPageMapper from "../Mapper/ProductPageMapper";
import ProductPageService from "../Service/ProductPageService";

class ProductPageController {
    #mapper
    constructor() {
        this.#mapper = ProductPageMapper;
        this.service = new ProductPageService();
        this.handler = new ProductPageHandler();

        this.service.showImagesByColor($('.color-btn.active'));

        Tipped.create('.cleaning-icons');

        this.registerEvents();
    }

    registerEvents() {
        this.#mapper.color.on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.showImagesByColor($(e.currentTarget));
        });

        this.#mapper.size.on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.toggleActiveSize(e.currentTarget);
        });

        this.#mapper.addBtn.on('click touchend', e => {
            this.handler.save();
        });

        $(this.#mapper.youtubeModal).on('shown.bs.modal', e => {
            const videoWidth = window.outerWidth * 0.8;

            $(this.#mapper.youtubeCarousel).owlCarousel({
                items: 1,
                merge: true,
                loop: true,
                margin: 10,
                video: true,
                lazyLoad: false,
                center: true,
                responsiveClass: true,
                navigation: true,
                navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
                nav: true,
                smartSpeed: 2000,
                dots: true,
                videoWidth: 740,
                videoHeight: 400,
                autoWidth: true,
                responsive: {
                    0: {
                        items: 1,
                        videoWidth: videoWidth,
                        videoHeight: 100,
                        center: false,
                    },
                    300: {
                        items: 1,
                        videoWidth: videoWidth,
                        videoHeight: 200,
                        center: false,
                    },
                    480: {
                        items: 1,
                        videoWidth: 300,
                        videoHeight: 200,
                    },
                    760: {
                        items: 1,
                        videoWidth: 600,
                        videoHeight: 400,
                    }
                },
                onTranslate: event => {
                    const players = $('.owl-item').find('iframe');

                    for (const player of players) {
                        player.remove();
                    }
                },
            });
        })
    }
}

export default ProductPageController;
