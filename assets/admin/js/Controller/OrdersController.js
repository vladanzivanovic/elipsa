import OrdersDataTables from "../Services/DataTables/OrdersDataTables";

const Private = Symbol('private');

class OrdersController {
    constructor() {
        if (CAN_VIEW) {
            OrdersDataTables().init();
        }
    }
};

export default OrdersController;