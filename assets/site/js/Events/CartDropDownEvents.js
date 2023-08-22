import orderApiProvider from "../Provider/OrderApiProvider";
import cartDropDownDom from "../Dom/CartDropDownDom";
import orderApiHandler from "../Handler/Order/OrderApiHandler";

class CartDropDownEvents {
    #orderApiProvider;
    #cartDom;
    #orderApiHandler;

    constructor() {
        this.#orderApiProvider = orderApiProvider;
        this.#cartDom = cartDropDownDom;
        this.#orderApiHandler = orderApiHandler;

        this.#setCartDropDown();

        this.#registerEvents();
    }

    #registerEvents() {
        $(document).on('click touchend', '.top-cart .mcp-pro-delete', e => {
            e.preventDefault();
            e.stopPropagation();

            const productId = $(e.currentTarget).parent('.single-product').data('id');

            this.#orderApiHandler.removeProduct(productId)
                .then(order => {
                    this.#cartDom.removeProduct(productId);
                    this.#cartDom.setOrderData(order);
                });
        });
    }

    #setCartDropDown()
    {
        if (!localStorage.getItem('order')) {
            return;
        }

        this.#orderApiProvider.getOrder(localStorage.getItem('order'))
            .then(order => this.#cartDom.manageDropDown(order));
    }
}

export default CartDropDownEvents;
