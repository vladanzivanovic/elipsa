import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import SliderDataTables from "../Services/DataTables/SliderDataTables";
import SliderHandler from "../Handler/SliderHandler";

const Private = Symbol('private');

class SliderController {
    constructor() {
        if (CAN_VIEW) {
            SliderDataTables().init();
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
                 const title = 'Da li ste sigurni da želite obrišete slider?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-slider', e => {
                 const id = e.currentTarget.dataset.id;
                 const handler = new SliderHandler();

                 handler.remove(id);
             });


             $(document).on('change', '.set-active-slider', e => {
                 const id = e.currentTarget.dataset.id;
                 const status = e.currentTarget.checked ? 1 : 0;
                 const handler = new SliderHandler();

                 handler.changeStatus(e.currentTarget, id, status);
             });
         }

         return Private;
    }
};

export default SliderController;
