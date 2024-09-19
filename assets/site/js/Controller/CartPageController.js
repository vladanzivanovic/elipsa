import cartPageMapper from "../Mapper/CartPageMapper";
import cartPageEvents from "../Events/CartPageEvents";

class CartPageController {
    #pageEvents;

    constructor() {
        this.mapper = cartPageMapper;
        this.#pageEvents = cartPageEvents;

        this.#pageEvents.registerEvents();
    }
}

export default CartPageController
