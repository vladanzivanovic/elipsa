import cartPageMapper from "../Mapper/CartPageMapper";
import orderApiProvider from "../Provider/OrderApiProvider";
import checkoutPageDom from "../Dom/CheckoutPageDom";
import OrderPageRelatedManipulator from "./OrderPageRelatedManipulator";

class CheckoutPageManipulator extends OrderPageRelatedManipulator{
    #orderApiProvider;
    #pageMapper;
    #pageDom;

    constructor() {
        if(!CheckoutPageManipulator.instance) {
            super();

            this.#orderApiProvider = orderApiProvider;
            this.#pageMapper = cartPageMapper;
            this.#pageDom = checkoutPageDom;

            CheckoutPageManipulator.instance = this;
        }

        return CheckoutPageManipulator.instance;
    }

    updatePage(order)
    {
        if (this.shouldRemoveOrder(order)) {
            this.removeOrder(order);

            return;
        }

        this.#pageDom.manageOrderData(order)
        this.#pageDom.toggleStoreShipping(order);

        this.showPriceChangeDialog(order);
    }
}

const checkoutPageManipulator = new CheckoutPageManipulator();

Object.freeze(checkoutPageManipulator);

export default checkoutPageManipulator;
