import invoicePaymentEvents from "../Event/InvoicePaymentEvents";
import orderStateEvents from "../Event/OrderStateEvents";

class OrderSinglePageController {
    constructor() {
        invoicePaymentEvents.registerEvents();
        orderStateEvents.registerEvents();
    }
}

export default OrderSinglePageController;
