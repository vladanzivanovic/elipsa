import BaseCoreController from "../../js/CoreController";
import CartHandler from "./Handler/CartHandler";
import loader from "./Dom/LoaderDom";
import coreMapper from "./Mapper/CoreMapper";
import NewsLetterHandler from "./Handler/NewsLetterHandler";
import FooterEvents from "./Events/FooterEvents";
import HeaderEvents from "./Events/HeaderEvents";
import CartDropDownEvents from "./Events/CartDropDownEvents";
import ProductsEvents from "./Events/ProductsEvents";
import NewsLetterEvents from "./Events/NewsLetterEvents";

require('jquery-eu-cookie-law-popup/js/jquery-eu-cookie-law-popup');

class CoreController {
    #handler;
    #mapper;

    constructor() {
        this.baseCore = new BaseCoreController();
        this.#handler = new CartHandler();
        this.#mapper = coreMapper;

        loader;

        this.sliderText();

        new HeaderEvents();
        new FooterEvents();
        new CartDropDownEvents();
        new ProductsEvents();
        new NewsLetterEvents();
    }

    siteMobileMenu() {
        $('nav#mobile_menu_active').meanmenu({
            meanScreenWidth: "991",
            meanMenuContainer: '.mobile-menu-area .mobile_menu',
            meanMenuOpen: "<span></span><span></span><span></span>",
            meanRevealPosition: "left",
        });
    }

    sliderText() {
        $('#slider_text_carousel').owlCarousel({
            loop: true,
            margin: 0,
            responsiveClass: true,
            navigation: false,
            navText: false,
            nav: false,
            items: 1,
            smartSpeed: 2000,
            dots: false,
            autoplay: false,
            autoplayTimeout: 4000,
            center: false,
            responsive: {
                0: {
                    items: 1,
                    autoplay: true,
                },
                480: {
                    items: 1,
                    autoplay: true,
                },
                760: {
                    items: 3
                }
            }
        });
    }

    registerEvents() {

        // $(document).on('click touchend', this.#mapper.newsLetterSubmitBtn, e => {
        //     e.preventDefault();
        //     e.stopPropagation();
        //
        //     const handler = new NewsLetterHandler();
        //
        //     handler.addUser(this.#mapper.newsLetterForm, true);
        // });

        $(document).on('click touchend', this.#mapper.newsLetterSubmitBtnFooter, e => {
            e.preventDefault();
            e.stopPropagation();

            const handler = new NewsLetterHandler();

            handler.addUser(this.#mapper.newsLetterFormFooter);
        });

        $('#current_language').on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            const listElm = $(e.currentTarget).next();

            if (!$(listElm).hasClass('active-list')) {
                $(listElm).addClass('active-list');

                return;
            }

            $(listElm).removeClass('active-list');
        });

        // $(document).euCookieLawPopup().init({
        //     cookiePolicyUrl : Routing.generate(`site.cookie_policy.${LOCALE}`),
        //     popupTitle : Translator.trans('eu.cookies.accept.title', null, 'messages', LOCALE),
        //     popupText : Translator.trans('eu.cookies.accept.text', null, 'messages', LOCALE),
        //     buttonContinueTitle : Translator.trans('eu.cookies.accept.btn', null, 'messages', LOCALE),
        //     buttonLearnmoreTitle : Translator.trans('eu.cookies.learn_more.btn', null, 'messages', LOCALE),
        //     buttonLearnmoreOpenInNewWindow : true,
        //     agreementExpiresInDays : 30,
        //     autoAcceptCookiePolicy : false,
        //     htmlMarkup : null
        // });
    }
}

export default CoreController;
