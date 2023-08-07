import orderApiProvider from "../Provider/OrderApiProvider";
import cartPageDom from "../Dom/CartPageDom";
import cartPageMapper from "../Mapper/CartPageMapper";
import cartPageErrorDom from "../Dom/CartPageErrorDom";

class CartPageManipulator {
    #orderApiProvider;
    #cartPageDom;
    #mapper;
    #cartPageErrorDom;

    constructor() {
        if (!CartPageManipulator.instance) {
            this.#orderApiProvider = orderApiProvider;
            this.#cartPageDom = cartPageDom;
            this.#mapper = cartPageMapper;
            this.#cartPageErrorDom = cartPageErrorDom;

            CartPageManipulator.instance = this;
        }

        return CartPageManipulator.instance;
    }

    setCartPage()
    {
        if (!localStorage.getItem('order')) {
            this.#cartPageDom.resetCartPage();

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
            this.#cartPageDom.resetCartPage();

            return;
        }
        localStorage.setItem('orderData', JSON.stringify(order));
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
