import orderApiProvider from "../Provider/OrderApiProvider";
import cartPageDom from "../Dom/CartPageDom";
import cartPageMapper from "../Mapper/CartPageMapper";
import cartPageErrorDom from "../Dom/CartPageErrorDom";
import orderStorageManipulator from "./OrderStorageManipulator";

class CartPageManipulator {
    #orderApiProvider;
    #cartPageDom;
    #mapper;
    #cartPageErrorDom;
    #orderStorageManipulator;

    constructor() {
        if (!CartPageManipulator.instance) {
            this.#orderApiProvider = orderApiProvider;
            this.#cartPageDom = cartPageDom;
            this.#mapper = cartPageMapper;
            this.#cartPageErrorDom = cartPageErrorDom;
            this.#orderStorageManipulator = orderStorageManipulator;

            CartPageManipulator.instance = this;
        }

        return CartPageManipulator.instance;
    }

    setCartPage()
    {
        const orderToken = this.#orderStorageManipulator.getOrderToken();

        if (!orderToken) {
            this.#cartPageDom.resetCartPage();

            return;
        }

        this.#orderApiProvider.getOrder(orderToken)
            .then(order => {
                this.updatePage(order);
            });
    }

    updatePage(order)
    {
        if (null === order || null !== order.checkout_completed_at) {
            this.#cartPageDom.resetCartPage();

            this.#orderStorageManipulator.removeOrder();

            return;
        }

        this.#orderStorageManipulator.setOrderData(order);

        this.#cartPageDom.manageOrderData(order)
    }

    showError(error, type)
    {
        const capitalize = type[0].toUpperCase() + type.slice(1);
        const fnName = `set${capitalize}`;

        this.#cartPageErrorDom[fnName](error);
    }
}

const cartPageManipulator = new CartPageManipulator();

Object.freeze(cartPageManipulator);

export default cartPageManipulator;
