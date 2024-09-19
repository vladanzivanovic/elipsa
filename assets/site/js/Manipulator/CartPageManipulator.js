import orderApiProvider from "../Provider/OrderApiProvider";
import cartPageDom from "../Dom/CartPageDom";
import cartPageMapper from "../Mapper/CartPageMapper";
import cartPageErrorDom from "../Dom/CartPageErrorDom";
import orderStorageManipulator from "./OrderStorageManipulator";
import OrderPageRelatedManipulator from "./OrderPageRelatedManipulator";

class CartPageManipulator extends OrderPageRelatedManipulator{
    #orderApiProvider;
    #cartPageDom;
    #mapper;
    #cartPageErrorDom;
    #orderStorageManipulator;

    constructor() {
        if (!CartPageManipulator.instance) {
            super();
            this.#orderApiProvider = orderApiProvider;
            this.#cartPageDom = cartPageDom;
            this.#mapper = cartPageMapper;
            this.#cartPageErrorDom = cartPageErrorDom;
            this.#orderStorageManipulator = orderStorageManipulator;

            CartPageManipulator.instance = this;
        }

        return CartPageManipulator.instance;
    }

    updatePage(order)
    {
        if (this.shouldRemoveOrder(order)) {
            this.#cartPageDom.resetCartPage();

            this.removeOrder();

            return;
        }

        this.#cartPageDom.manageOrderData(order);

        this.showPriceChangeDialog(order);
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
