import FormHelperService from "../../../../js/Helper/FormHelperService";
import couponsEditMapper from "../../Mapper/CouponsEditMapper";

class InvoiceApiHandler {
    #mapper;

    constructor() {
        if (!InvoiceApiHandler.instance) {
            this.#mapper = couponsEditMapper;

            InvoiceApiHandler.instance = this;
        }

        return InvoiceApiHandler.instance;
    }

    async saveState(data)
    {
        let result;
        let route = Routing.generate('admin.order_set_status', {token: ORDER_TOKEN});
        let type = 'PUT';

        try {
            result = await $.ajax({
                type,
                url: route,
                data: JSON.stringify(data),
            })
        }catch (error) {
            result = error;
        }

        return result;
    }

    async setAsVisited()
    {
        let result;
        let route = Routing.generate('admin.order_set_visited', {token: ORDER_TOKEN});
        let type = 'PUT';

        try {
            result = await $.ajax({
                type,
                url: route,
                data: null,
            })
        }catch (error) {
            result = error;
        }

        return result;
    }
}
const invoiceApiHandler = new InvoiceApiHandler();

Object.freeze(invoiceApiHandler);

export default invoiceApiHandler;

