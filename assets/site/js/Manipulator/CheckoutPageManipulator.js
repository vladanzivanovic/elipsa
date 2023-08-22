import cartPageMapper from "../Mapper/CartPageMapper";
import orderApiProvider from "../Provider/OrderApiProvider";
import checkoutPageDom from "../Dom/CheckoutPageDom";

class CheckoutPageManipulator {
    #orderApiProvider;
    #pageMapper;
    #pageDom;

    constructor() {
        if(!CheckoutPageManipulator.instance) {
            this.#orderApiProvider = orderApiProvider;
            this.#pageMapper = cartPageMapper;
            this.#pageDom = checkoutPageDom;

            CheckoutPageManipulator.instance = this;
        }

        return CheckoutPageManipulator.instance;
    }

    setPage()
    {
        if (!localStorage.getItem('order')) {
            return;
        }

        this.#orderApiProvider.getOrder(localStorage.getItem('order'))
            .then(order => {
                this.updatePage(order);
            });
    }

    updatePage(order)
    {
        if (null === order) {
            return;
        }

        localStorage.setItem('orderData', JSON.stringify(order));
        this.#pageDom.manageOrderData(order)
    }

    completeOrder()
    {
        if (!localStorage.getItem('order')) {
            return;
        }

        this.#orderApiProvider.completeOrder()
            .then(order => {
                if (order.payment_type === 1) {

                }
            });
    }
}

const checkoutPageManipulator = new CheckoutPageManipulator();

Object.freeze(checkoutPageManipulator);

export default checkoutPageManipulator;
