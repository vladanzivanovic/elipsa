import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import SliderTextDataTables from "../Services/DataTables/SliderTextDataTables";
import SliderTextHandler from "../Handler/SliderTextHandler";

const Private = Symbol('private');

class SliderTextController {
    constructor() {
        this.sliderTextTable = new SliderTextDataTables();

        if (CAN_VIEW) {
            this.sliderTextTable.init();
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
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-slider', 'data-id': id, 'data-dismiss': "modal"},
                 ];
                 const title = 'Da li ste sigurni da želite obrišete tekst?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-slider', e => {
                 const id = e.currentTarget.dataset.id;
                 const handler = new SliderTextHandler();

                 handler.remove(id);
             });


             $(document).on('change', '.set-active-slider', e => {
                 const id = e.currentTarget.dataset.id;
                 const status = e.currentTarget.checked ? ENTITY_STATUSES.STATUS_ACTIVE : ENTITY_STATUSES.STATUS_PENDING;
                 const handler = new SliderTextHandler();

                 handler.changeStatus(e.currentTarget, id, status);
             });
         }

         return Private;
    }
};

export default SliderTextController;
