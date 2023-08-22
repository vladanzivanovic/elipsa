import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import SliderTextDataTables from "../Services/DataTables/SliderTextDataTables";
import SliderTextHandler from "../Handler/SliderTextHandler";
import OfficeContactDataTables from "../Services/DataTables/OfficeContactDataTables";
import OfficeContactHandler from "../Handler/OfficeContactHandler";
import officeContactDataTables from "../Services/DataTables/OfficeContactDataTables";

class OfficeContactsController {
    #dataTable;
    #handler;

    constructor() {
        this.#dataTable = officeContactDataTables;
        this.#handler = new OfficeContactHandler();

        if (CAN_VIEW) {
            this.#dataTable.init();
        }

        this.notification = NotificationService();

        this.#registerEvents();
    }

    #registerEvents () {
         $(document).on('click touchend', '.remove-item-button', e => {
             const id = e.currentTarget.dataset.id;
             const buttons = [
                 {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-item', 'data-id': id, 'data-dismiss': "modal"},
             ];
             const title = 'Da li ste sigurni da želite obrišete podatke?';
             const confirmModal = new ConfirmationModalService(title, buttons);

             confirmModal.trigger('show');
         });

         $(document).on('click touchend', '.remove-item', e => {
             const id = e.currentTarget.dataset.id;

             this.#handler.remove(id);
         });
     }
};

export default OfficeContactsController;
