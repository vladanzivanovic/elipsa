import ConfirmationModalService from "../../../admin/js/Services/ConfirmationModalService";
import orderStorageManipulator from "./OrderStorageManipulator";

class OrderPageRelatedManipulator {
    #orderStorageManipulator;

    constructor() {
        this.#orderStorageManipulator = orderStorageManipulator;
    }

    setPage(order)
    {
        const orderToken = this.#orderStorageManipulator.getOrderToken();

        if (!orderToken) {
            this.removeOrder()
            return;
        }

        this.#orderStorageManipulator.setOrderData(order);

        this.updatePage(order ?? this.#orderStorageManipulator.getOrderData());
    }

    showPriceChangeDialog(order)
    {
        if (true === order.isPriceChanged) {
            const confirmModal = new ConfirmationModalService(
                Translator.trans('order.price_has_changed', null, 'messages', LOCALE),
            );

            confirmModal.trigger('show');
        }
    }

    removeOrder(order)
    {
        this.#orderStorageManipulator.removeOrder();
    }

    shouldRemoveOrder(order)
    {
        return (null === order || null !== order.checkout_completed_at);
    }
}
export default OrderPageRelatedManipulator;
