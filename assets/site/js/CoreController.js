import BaseCoreController from "../../js/CoreController";
import CartHandler from "./Handler/CartHandler";
import loader from "./Dom/LoaderDom";
import coreMapper from "./Mapper/CoreMapper";
import UserHandler from "./Handler/UserHandler";
import registrationValidator from "./Validators/RegistrationValidator";
import WishListHandler from "./Handler/WishListHandler";
import NewsLetterHandler from "./Handler/NewsLetterHandler";

class CoreController {
    constructor() {
        this.baseCore = new BaseCoreController();
        this.handler = new CartHandler();
        this.mapper = coreMapper;
        this.registrationValidator = registrationValidator;

        loader;

        this.registerEvents();
    }

    siteMobileMenu() {
        $('nav#mobile_menu_active').meanmenu({
            meanScreenWidth: "991",
            meanMenuContainer: '.mobile-menu-area .container',
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

            handler.addUser();
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
    }
}

export default CoreController;