import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import DescriptionDataTables from "../Services/DataTables/DescriptionDataTables";
import DescriptionHandler from "../Handler/DescriptionHandler";

const Private = Symbol('private');

class DescriptionController {
    constructor() {
        if (CAN_VIEW) {
            DescriptionDataTables().init();
        }
        this.notification = NotificationService();

        this[Private]().registerEvents();
    }

    [Private]() {
        let Private = {};

         Private.registerEvents = () => {
             $(document).on('click touchend', '.remove-item-button', e => {
                 const type = e.currentTarget.dataset.type;
                 const buttons = [
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-description', 'data-type': type, 'data-dismiss': "modal"},
                 ];
                 const title = 'Da li ste sigurni da želite obrišete tekst?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-description', e => {
                 const type = e.currentTarget.dataset.type;
                 const handler = new DescriptionHandler();

                 handler.remove(type);
             });
         }

         return Private;
    }
};

export default DescriptionController;