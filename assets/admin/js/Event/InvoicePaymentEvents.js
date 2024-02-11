import orderSinglePageMapper from "../Mapper/OrderSinglePageMapper";
import invoicePageService from "../Services/InvoicePageService";

class InvoicePaymentEvents
{
    #mapper;
    #service;

    constructor() {
        if(!InvoicePaymentEvents.instance) {
            this.#mapper = orderSinglePageMapper;
            this.#service = invoicePageService;

            InvoicePaymentEvents.instance = this;
        }

        return InvoicePaymentEvents.instance;
    }

    registerEvents() {
        $(this.#mapper.payment.postAuthBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.#service.doRequest(Routing.generate('admin.intesa_post_auth_request', {token: ORDER_TOKEN}));
        });

        $(this.#mapper.payment.refundBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.#service.doRequest(Routing.generate('admin.intesa_refund_request', {token: ORDER_TOKEN}));
        });

        $(this.#mapper.payment.voidBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.#service.doRequest(Routing.generate('admin.intesa_void_request', {token: ORDER_TOKEN}));
        });
    }
}

const invoicePaymentEvents = new InvoicePaymentEvents();

Object.freeze(invoicePaymentEvents);

export default invoicePaymentEvents;
