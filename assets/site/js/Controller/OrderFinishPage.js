import orderStorageManipulator from "../Manipulator/OrderStorageManipulator";

class OrderFinishPage {
    #orderStorageManipulator;

    constructor(params) {
        this.#orderStorageManipulator = orderStorageManipulator;

        if (params.isSuccessful) {
            this.#orderStorageManipulator.removeOrder();
        }
    }
}

export default OrderFinishPage;
