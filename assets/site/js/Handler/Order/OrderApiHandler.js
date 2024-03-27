import AppHelperService from "../../../../js/Helper/AppHelperService";
import orderApiChecker from "../../Checker/OrderApiChecker";
import loader from "../../Dom/LoaderDom";
import orderStorageManipulator from "../../Manipulator/OrderStorageManipulator";

class OrderApiHandler {
    #checker;
    #orderStorageManipulator;

    constructor() {
        if (!OrderApiHandler.instance) {
            this.#checker = orderApiChecker;
            this.#orderStorageManipulator = orderStorageManipulator;

            OrderApiHandler.instance = this;
        }

        return OrderApiHandler.instance;
    }

    create()
    {
        return $.ajax({
            type: 'POST',
            url: AppHelperService.generateLocalizedUrl('site_api.create_order'),
            data: null,
        });
    }

    async manageProduct(color, size, quantity, slug = SLUG)
    {
        const urlRoute = Routing.generate('site_api.set_product_order', {
            token: this.#orderStorageManipulator.getOrderToken(),
            slug
        });

        let result;

        const data = {color, size, quantity};

        try {
            this.#checker.checkManageProduct(data);

            result = await $.ajax({
                type: 'POST',
                url: urlRoute,
                data: JSON.stringify(data),
            });

        } catch (error) {
            console.log(error);
            result = error;
        }

        return result;
    }

    /**
     *
     * @param {JSON} formData
     * @returns {Promise<*>}
     */
    async completeOrder(formData)
    {
        const urlRoute = Routing.generate('site_api.order_complete', {
            token: this.#orderStorageManipulator.getOrderToken(),
        });

        let result;

        try {
            result = await $.ajax({
                type: 'POST',
                url: urlRoute,
                data: JSON.stringify(formData),
            });

        } catch (error) {
            result = error;
        }

        return result;
    }

    async removeOrder()
    {
        const urlRoute = Routing.generate('site_api.remove_order', {
            token: this.#orderStorageManipulator.getOrderToken(),
        });

        let result;

        try {
            result = await $.ajax({
                type: 'DELETE',
                url: urlRoute,
                data: null,
            });
        } catch (error) {
            result = error;
        }

        return result;
    }

    async removeProduct(orderProductId)
    {
        const urlRoute = Routing.generate('site_api.remove_order_product', {
            token: this.#orderStorageManipulator.getOrderToken(),
            orderProductId: orderProductId
        });

        let result;

        try {
            result = await $.ajax({
                type: 'DELETE',
                url: urlRoute,
                data: null,
            });

        } catch (error) {
            result = error;
        }

        return result;
    }

    async setCoupon(couponCode)
    {
        const urlRoute = Routing.generate('site_api.add_order_coupon_code', {
            token: this.#orderStorageManipulator.getOrderToken(),
            code: couponCode
        })

        let result;

        try {
            result = await $.ajax({
                type: 'PUT',
                url: urlRoute,
                data: null,
            });

        } catch (error) {
            result = error;
        }

        return result;
    }

    async removeCoupon(couponCode)
    {
        const urlRoute = Routing.generate('site_api.remove_order_coupon_code', {
            token: this.#orderStorageManipulator.getOrderToken(),
            code: couponCode
        })

        let result;

        try {
            result = await $.ajax({
                type: 'DELETE',
                url: urlRoute,
                data: null,
            });

        } catch (error) {
            result = error;
        }

        return result;
    }
}

const orderApiHandler = new OrderApiHandler();

Object.freeze(orderApiHandler);

export default orderApiHandler;
