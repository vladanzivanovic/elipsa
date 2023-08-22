import orderApiProvider from "../Provider/OrderApiProvider";
import cartDropDownDom from "../Dom/CartDropDownDom";
import orderApiHandler from "../Handler/Order/OrderApiHandler";
import orderStorageManipulator from "../Manipulator/OrderStorageManipulator";

class CartDropDownEvents {
    #orderApiProvider;
    #cartDom;
    #orderApiHandler;
    #orderStorageManipulator;

    constructor() {
        this.#orderApiProvider = orderApiProvider;
        this.#cartDom = cartDropDownDom;
        this.#orderApiHandler = orderApiHandler;
        this.#orderStorageManipulator = orderStorageManipulator;

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

                    if (0 === order.products.length) {
                        this.#orderStorageManipulator.removeOrder();

                        this.#cartDom.removeOrderData();
                    }
                });
        });

        $(document).on('cart:update', (e, order) => {
            this.#cartDom.setOrderData(order);
        })
    }

    #setCartDropDown()
    {
        const orderToken = this.#orderStorageManipulator.getOrderToken();

        if (!orderToken) {
            return;
        }

        this.#orderApiProvider.getOrder(orderToken)
            .then(order => {
                if (null !== order.checkout_completed_at) {
                    this.#orderStorageManipulator.removeOrder();

                    return;
                }

                this.#cartDom.manageDropDown(order)
            });
    }
}

export default CartDropDownEvents;
