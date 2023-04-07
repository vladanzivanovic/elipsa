import homePageMapper from "../Mapper/HomePageMapper";

require('nivo-slider/jquery.nivo.slider');

class HomePageController {
    #mapper;
    #owlOptions = {
        loop: true,
        margin: 0,
        responsiveClass: true,
        navigation: true,
        navText: ["<i class='fa fa-long-arrow-left'></i>", "<i class='fa fa-long-arrow-right'></i>"],
        nav: true,
        items: 1,
        smartSpeed: 2000,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        center: false,
        responsive: {
            0: {
                items: 2,
                center: true,
            },
            480: {
                items: 2,
                center: true,
            },
            760: {
                items: 3
            }
        }
    };

    constructor() {
        this.#mapper = homePageMapper;

        $(this.#mapper.topCarousel).owlCarousel(this.#owlOptions);
        $(this.#mapper.bottomCarousel).owlCarousel(this.#owlOptions);

        $(this.#mapper.slider).nivoSlider({
            directionNav: false,
            animSpeed: 1000,
            slices: 18,
            pauseTime: 6000,
            pauseOnHover: false,
            controlNav: false,
            controlNavThumbs: false,
            prevText: '<i class="fa fa-chevron-left nivo-prev-icon"></i>',
            nextText: '<i class="fa fa-chevron-right nivo-next-icon"></i>'
        });
    }
}

export default HomePageController;
