import orderSinglePageMapper from "../Mapper/OrderSinglePageMapper";
import invoicePageHandler from "../Handler/Invoice/InvoicePageHandler";
import invoicePageModal from "../Dom/InvoicePageModal";

class OrderStateEvents
{
    #mapper;
    #handler;
    #invoiceModal;

    constructor() {
        if(!OrderStateEvents.instance) {
            this.#mapper = orderSinglePageMapper;
            this.#handler = invoicePageHandler;
            this.#invoiceModal = invoicePageModal;

            OrderStateEvents.instance = this;
        }

        return OrderStateEvents.instance;
    }

    registerEvents() {
        $(this.#mapper.state.changeBtn).on('click', e => {
            e.preventDefault();
            e.stopPropagation();

            this.#invoiceModal.invoiceStatusModal();
        });

        $(document).on('click', this.#mapper.state.applyBtn, async e => {
            e.preventDefault();
            e.stopPropagation();

            await this.#handler.setState();
        });
    }
}

const orderStateEvents = new OrderStateEvents();

Object.freeze(orderStateEvents);

export default orderStateEvents;
