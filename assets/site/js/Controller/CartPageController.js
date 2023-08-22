import cartPageMapper from "../Mapper/CartPageMapper";
import CartHandler from "../Handler/CartHandler";
import orderApiProvider from "../Provider/OrderApiProvider";
import cartPageDom from "../Dom/CartPageDom";
import cartPageManipulator from "../Manipulator/CartPageManipulator";
import cartPageEvents from "../Events/CartPageEvents";

class CartPageController {
    #pageManipulator;
    #pageEvents;

    constructor() {
        this.mapper = cartPageMapper;
        this.hander = new CartHandler();
        this.#pageManipulator = cartPageManipulator;
        this.#pageEvents = cartPageEvents;

        this.#pageManipulator.setCartPage();

        this.#pageEvents.registerEvents();
    }
}

export default CartPageController
