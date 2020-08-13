import SizeHandler from "../Handler/SizeHandler";
import orderSinglePageMapper from "../Mapper/OrderSinglePageMapper";
import OrderSinglePageService from "../Services/OrderSinglePageService";

class OrderSinglePageController {
    constructor() {
        this.mapper = orderSinglePageMapper;
        this.service = new OrderSinglePageService();

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.postAuthBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.doRequest(Routing.generate('admin.intesa_post_auth_request', {id: ID}));
        });

        $(this.mapper.refundBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.doRequest(Routing.generate('admin.intesa_refund_request', {id: ID}));
        });

        $(this.mapper.voidBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.doRequest(Routing.generate('admin.intesa_void_request', {id: ID}));
        });
    }
}

export default OrderSinglePageController;