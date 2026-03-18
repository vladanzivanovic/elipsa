import homePageMapper from "../Mapper/HomePageMapper";

require('nivo-slider/jquery.nivo.slider');
require('jquery-countdown/src/countdown');

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
            pauseTime: 2000,
            pauseOnHover: false,
            controlNav: false,
            controlNavThumbs: false,
            prevText: '<i class="fa fa-chevron-left nivo-prev-icon"></i>',
            nextText: '<i class="fa fa-chevron-right nivo-next-icon"></i>'
        });

        if (IS_MOBILE) {
            this.#setFontSizeForMobile();
        }

        this.#setPromotionCountDown();
    }

    #setFontSizeForMobile()
    {
        const divider = 1000/window.innerWidth;

        $('.slide3-text *').each((i, e) => {
            const currentFontSize = $(e).prop("style")["font-size"];

            if (currentFontSize != '') {
                const sizeNumber = $(e).prop("style")["font-size"].match(/\d+/g);
                const newFontSize = sizeNumber[0] / divider;

                $(e).css('font-size', `${newFontSize}px`);
            }
        });
    }

    #setPromotionCountDown()
    {
        $('#countdown').countdown($('#promotion-bar').data('validTo'))
            .on('update.countdown', function(event) {
                let weekLabel = event.offset.weeks > 1 || event.offset.weeks === 0 ?
                    Translator.trans('promotion.timer.weeks', null, 'messages', LOCALE) :
                    Translator.trans('promotion.timer.week', null, 'messages', LOCALE);
                let dayLabel = event.offset.days > 1 || event.offset.days === 0 ?
                    Translator.trans('promotion.timer.days', null, 'messages', LOCALE) :
                    Translator.trans('promotion.timer.day', null, 'messages', LOCALE);
                let hourLabel = event.offset.hours > 1 || event.offset.hours === 0 ?
                    Translator.trans('promotion.timer.hours', null, 'messages', LOCALE) :
                    Translator.trans('promotion.timer.hour', null, 'messages', LOCALE);
                let minuteLabel = event.offset.minutes > 1 || event.offset.minutes === 0 ?
                    Translator.trans('promotion.timer.minutes', null, 'messages', LOCALE) :
                    Translator.trans('promotion.timer.minute', null, 'messages', LOCALE);
                let secondLabel = event.offset.seconds > 1 || event.offset.seconds === 0 ?
                    Translator.trans('promotion.timer.seconds', null, 'messages', LOCALE) :
                    Translator.trans('promotion.timer.second', null, 'messages', LOCALE);

                const $this = $(this).html(event.strftime(''
                    + `<p><span>%-w</span> <span>${weekLabel}</span> </p>`
                    + `<p><span>%-d</span> <span>${dayLabel}</span> </p>`
                    + `<p><span>%H</span> <span>${hourLabel}</span> </p>`
                    + `<p><span>%M</span> <span>${minuteLabel}</span> </p>`
                    + `<p><span>%S</span> <span>${secondLabel}</span> </p>`));
            });

        ;
    }
}

export default HomePageController;
