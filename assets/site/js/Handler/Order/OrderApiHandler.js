import AppHelperService from "../../../../js/Helper/AppHelperService";
import orderApiChecker from "../../Checker/OrderApiChecker";
import loader from "../../Dom/LoaderDom";

class OrderApiHandler {
    #checker;

    constructor() {
        if (!OrderApiHandler.instance) {
            this.#checker = orderApiChecker;

            OrderApiHandler.instance = this;
        }

        return OrderApiHandler.instance;
    }

    create()
    {
        // let result;
        // try {
            return $.ajax({
                type: 'POST',
                url: AppHelperService.generateLocalizedUrl('site_api.create_order'),
                data: null,
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            });

            // localStorage.setItem('order', result.token);

        // } catch (error) {
        //     return error;
        // }
    }

    async manageProduct(color, size, quantity, slug = SLUG)
    {
        const urlRoute = AppHelperService.generateLocalizedUrl('site_api.set_product_order', {
            token: localStorage.getItem('order'),
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
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            });

        } catch (error) {
            result = error;
        }

        return result;
    }

    async completeOrder()
    {
        const urlRoute = AppHelperService.generateLocalizedUrl('site_api.add_order_complete_code",', {
            token: localStorage.getItem('order'),
        });

        let result;

        try {
            result = await $.ajax({
                type: 'POST',
                url: urlRoute,
                data: null,
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            });

        } catch (error) {
            result = error;
        }

        return result;
    }

    async removeOrder()
    {
        const urlRoute = AppHelperService.generateLocalizedUrl('site_api.remove_order', {
            token: localStorage.getItem('order'),
        });

        let result;

        try {
            result = await $.ajax({
                type: 'DELETE',
                url: urlRoute,
                data: null,
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            });
        } catch (error) {
            result = error;
        }

        return result;
    }

    async removeProduct(orderProductId)
    {
        const urlRoute = AppHelperService.generateLocalizedUrl('site_api.remove_order_product', {
            token: localStorage.getItem('order'),
            orderProductId: orderProductId
        });

        let result;

        try {
            result = await $.ajax({
                type: 'DELETE',
                url: urlRoute,
                data: null,
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            });

        } catch (error) {
            result = error;
        }

        return result;
    }

    async setCoupon(couponCode)
    {
        const urlRoute = AppHelperService.generateLocalizedUrl('site_api.add_order_coupon_code', {
            token: localStorage.getItem('order'),
            code: couponCode
        })

        let result;

        try {
            result = await $.ajax({
                type: 'PUT',
                url: urlRoute,
                data: null,
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            });

        } catch (error) {
            result = error;
        }

        return result;
    }

    async removeCoupon(couponCode)
    {
        const urlRoute = AppHelperService.generateLocalizedUrl('site_api.remove_order_coupon_code', {
            token: localStorage.getItem('order'),
            code: couponCode
        })

        let result;

        try {
            result = await $.ajax({
                type: 'DELETE',
                url: urlRoute,
                data: null,
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
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
