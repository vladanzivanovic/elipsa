import cartPageMapper from "../Mapper/CartPageMapper";
import orderApiProvider from "../Provider/OrderApiProvider";
import checkoutPageDom from "../Dom/CheckoutPageDom";
import orderStorageManipulator from "./OrderStorageManipulator";

class CheckoutPageManipulator {
    #orderApiProvider;
    #pageMapper;
    #pageDom;
    #orderStorageManipulator;

    constructor() {
        if(!CheckoutPageManipulator.instance) {
            this.#orderApiProvider = orderApiProvider;
            this.#pageMapper = cartPageMapper;
            this.#pageDom = checkoutPageDom;
            this.#orderStorageManipulator = orderStorageManipulator;

            CheckoutPageManipulator.instance = this;
        }

        return CheckoutPageManipulator.instance;
    }

    setPage()
    {
        const orderToken = this.#orderStorageManipulator.getOrderToken();

        if (!orderToken) {
            return;
        }

        this.#orderApiProvider.getOrder(orderToken)
            .then(order => {
                this.updatePage(order);
            });
    }

    updatePage(order)
    {
        if (null === order) {
            return;
        }

        this.#orderStorageManipulator.setOrderData(order);
        this.#pageDom.manageOrderData(order)
    }
}

const checkoutPageManipulator = new CheckoutPageManipulator();

Object.freeze(checkoutPageManipulator);

export default checkoutPageManipulator;
