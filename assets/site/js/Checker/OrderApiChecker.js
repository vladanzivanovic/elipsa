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

    checkManageProduct(productData)
    {
        for (let prop in productData) {
            const value = productData[prop];

            if (!value || value < 1) {
                this.#showErrorAndThrowException(`product.${prop}`);
            }
        }
    }

    isSizeAvailable(size)
    {
        for (const productSize of SIZES) {
            if (size === productSize.size && productSize.quantity <= 0) {
                this.#showErrorAndThrowException('product.size_unavailable');
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

    #showErrorAndThrowException(key)
    {
        this.#notification.show('error', Translator.trans(key, null, 'validators', LOCALE), true);

        throw Error('Validation failed.');
    }
}

const orderApiChecker = new OrderApiChecker();

Object.freeze(orderApiChecker);

export default orderApiChecker;
