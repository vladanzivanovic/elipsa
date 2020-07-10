import BaseCoreController from "../../js/CoreController";
import CartHandler from "./Handler/CartHandler";
import loader from "./Dom/LoaderDom";
import coreMapper from "./Mapper/CoreMapper";
import UserHandler from "./Handler/UserHandler";
import registrationValidator from "./Validators/RegistrationValidator";
import WishListHandler from "./Handler/WishListHandler";
import NewsLetterHandler from "./Handler/NewsLetterHandler";
import resetPasswordValidator from "./Validators/ResetPasswordValidator";

class CoreController {
    constructor() {
        this.baseCore = new BaseCoreController();
        this.handler = new CartHandler();
        this.mapper = coreMapper;
        this.registrationValidator = registrationValidator;
        this.resetPasswordValidator = resetPasswordValidator;

        loader;

        this.sliderText();

        this.registerEvents();
    }

    siteMobileMenu() {
        $('nav#mobile_menu_active').meanmenu({
            meanScreenWidth: "991",
            meanMenuContainer: '.mobile-menu-area .container',
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
        $(document).on('click touchend', '.top-cart .mcp-pro-delete', e => {
            e.preventDefault();
            e.stopPropagation();

            const productId = $(e.currentTarget).parent('.single-product').data('id');

            this.handler.remove(productId);
        });

        $(document).on('click touchend', this.mapper.registrationBtn, e => {
            const handler = new UserHandler();

            this.registrationValidator.validate(this.mapper.registrationForm);

            $(this.mapper.registrationForm).valid();

            handler.doRegistration(this.mapper.registrationForm);
        });

        $(document).on('click touchend', this.mapper.loginBtn, e => {
            const handler = new UserHandler();

            handler.doLogin(this.mapper);
        });

        $(document).on('click touchend', this.mapper.toggleWishListBtn, e => {
            e.preventDefault();
            e.stopPropagation();

            const handler = new WishListHandler();

            handler.toggle($(e.currentTarget));
        });

        $(document).on('click touchend', this.mapper.newsLetterSubmitBtn, e => {
            e.preventDefault();
            e.stopPropagation();

            const handler = new NewsLetterHandler();

            handler.addUser(this.mapper.newsLetterForm, true);
        });

        $(document).on('click touchend', this.mapper.newsLetterSubmitBtnFooter, e => {
            e.preventDefault();
            e.stopPropagation();

            const handler = new NewsLetterHandler();

            handler.addUser(this.mapper.newsLetterFormFooter);
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

        $('#reset_password_btn').on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            $('.lrc-login').fadeOut();
            $('.lrc-register').fadeOut();
            $('#reset_password').fadeOut();

            $('#reset_password_form_wrapper').fadeIn();
            $('#login_register_show').fadeIn();
        });

        $('#login_register_show_btn').on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            $('.lrc-login').fadeIn();
            $('.lrc-register').fadeIn();
            $('#reset_password').fadeIn();

            $('#reset_password_form_wrapper').fadeOut();
            $('#login_register_show').fadeOut();
        });

        $(document).on('click touchend', this.mapper.resetPasswordBtn, e => {
            const handler = new UserHandler();

            this.resetPasswordValidator.validate(this.mapper.resetForm);

            $(this.mapper.resetForm).valid();

            handler.doResetPassword(this.mapper);
        });
    }
}

export default CoreController;