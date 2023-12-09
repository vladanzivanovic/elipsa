import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import CouponsDataTables from "../Services/DataTables/PromotionDataTables";
import CouponHandler from "../Handler/Coupon/CouponHandler";
import PromotionDataTableEvents from "../Event/PromotionDataTableEvents";

const Private = Symbol('private');

class CouponsController {
    #dataTableEvent;

    constructor() {
        if (CAN_VIEW) {
            this.#dataTableEvent = new PromotionDataTableEvents();

            this.#dataTableEvent.registerEvents();

        }
        this.notification = NotificationService();

        this[Private]().registerEvents();
    }

    [Private]() {
        let Private = {};

         Private.registerEvents = () => {
             $(document).on('click touchend', '.remove-item-button', e => {
                 const id = e.currentTarget.dataset.id;
                 const buttons = [
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-coupon', 'data-id': id, 'data-dismiss': "modal"},
                 ];
                 const title = 'Da li ste sigurni da želite obrišete kupon?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-coupon', e => {
                 const id = e.currentTarget.dataset.id;
                 const handler = new CouponHandler();

                 handler.remove(id);
             });
         }

         return Private;
    }
};

export default CouponsController;
