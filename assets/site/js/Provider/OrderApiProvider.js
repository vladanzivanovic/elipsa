import AppHelperService from "../../../js/Helper/AppHelperService";
import orderStorageManipulator from "../Manipulator/OrderStorageManipulator";

class OrderApiProvider {
    constructor() {
        if (!OrderApiProvider.instance) {
            OrderApiProvider.instance = this;
        }

        return OrderApiProvider.instance;
    }

    async getOrder(token)
    {
        const urlRoute = AppHelperService.generateLocalizedUrl('site_api.get_order', {
            token: token
        });

        let result;

        try {
            result = await $.ajax({
                type: 'GET',
                url: urlRoute,
                data: null,
            });
        } catch (error) {
            result = error;
        }

        return result;
    }

    async getPayment(token)
    {
        const urlRoute = Routing.generate('site_api.get_order_payment', {
            token: token
        });

        let result;

        try {
            result = await $.ajax({
                type: 'GET',
                url: urlRoute,
                data: null,
            });

        } catch (error) {
            result = error;
        }

        return result;
    }

}

const orderApiProvider = new OrderApiProvider();

Object.freeze(orderApiProvider);

export default orderApiProvider;
