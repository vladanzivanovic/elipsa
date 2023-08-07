import NotificationService from "../../../js/NotificationService";

class OrderApiChecker {
    #notification;

    constructor() {
        if (!OrderApiChecker.instance) {
            this.#notification = NotificationService();

            OrderApiChecker.instance = this;
        }

        return OrderApiChecker.instance;
    }

    checkManageProduct(productData) {
        for (let prop in productData) {
            const value = productData[prop];

            if (!value || value < 1) {
                this.#notification.show('error', Translator.trans(`product.${prop}`, null, 'validators', LOCALE), true);

                throw 'Validation failed.';
            }
        }
    }

    hasAvailableProducts(orderProducts)
    {
        let productsCounter = 0;

        for (let product of orderProducts) {
            if (product.is_sold) {
                continue;
            }

            productsCounter++;
        }

        return 0 < productsCounter;
    }
}

const orderApiChecker = new OrderApiChecker();

Object.freeze(orderApiChecker);

export default orderApiChecker;
