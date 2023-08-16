import localStorageManipulator from "./LocalStorageManipulator";

class OrderStorageManipulator {
    #localStorageManipulator;

    constructor() {
        if(!OrderStorageManipulator.instance) {
            this.#localStorageManipulator = localStorageManipulator;

            OrderStorageManipulator.instance = this;
        }

        return OrderStorageManipulator.instance;
    }

    setOrder(token, data)
    {
        this.#localStorageManipulator.setItem('order', token);

        this.setOrderData(data);
    }

    setOrderData(data)
    {
        this.#localStorageManipulator.setItem(
            'orderData',
            JSON.stringify(data)
        );
    }

    getOrderToken()
    {
        return this.#localStorageManipulator.getItem('order');
    }

    getOrderData()
    {
        const data = this.#localStorageManipulator.getItem('orderData');

        return JSON.parse(data);
    }

    removeOrder()
    {
        this.#localStorageManipulator.removeItem('order');

        this.removeOrderData();
    }

    removeOrderData()
    {
        this.#localStorageManipulator.removeItem('orderData');
    }

}

const orderStorageManipulator = new OrderStorageManipulator();

Object.freeze(orderStorageManipulator);

export default orderStorageManipulator;
