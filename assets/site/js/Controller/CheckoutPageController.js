import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import CheckoutHandler from "../Handler/CheckoutHandler";

class CheckoutPageController {
    constructor() {
        this.mapper = checkoutPageMapper;
        this.handler = new CheckoutHandler();

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.btn.on('click touchend', e => {
            this.handler.save();
        });
    }
}

export default CheckoutPageController;