class OrderSinglePageMapper {
    constructor() {
        if (!OrderSinglePageMapper.instance) {
            this.payment = {
                postAuthBtn: '#post_auth_btn',
                refundBtn: '#refund_btn',
                voidBtn: '#void_btn'
            };

            this.state = {
                form: '#order_tracking_info_form',
                changeBtn: '.state-btn-change',
                applyBtn: '.invoice-state-apply',
                trackingInfoInput: '#tracking-url-input',
                stateSelect: '.state-select',
            };

            OrderSinglePageMapper.instance = this;
        }

        return OrderSinglePageMapper.instance;
    }
}

const orderSinglePageMapper = new OrderSinglePageMapper();

Object.freeze(orderSinglePageMapper);

export default orderSinglePageMapper;
