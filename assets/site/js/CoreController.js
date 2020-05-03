import BaseCoreController from "../../js/CoreController";
import CartHandler from "./Handler/CartHandler";

class CoreController {
    constructor() {
        this.baseCore = new BaseCoreController();
        this.handler = new CartHandler();

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
        })
    }
}

export default CoreController;