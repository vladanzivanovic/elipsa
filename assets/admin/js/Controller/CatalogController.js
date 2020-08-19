import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import CatalogHandler from "../Handler/CatalogHandler";
import CatalogDataTables from "../Services/DataTables/CatalogDataTables";

const Private = Symbol('private');

class CatalogController {
    constructor() {
        if (CAN_VIEW) {
            CatalogDataTables().init();
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
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-catalog', 'data-id': id, 'data-dismiss': "modal"},
                 ];
                 const title = 'Da li ste sigurni da želite obrišete katalog?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-catalog', e => {
                 const id = e.currentTarget.dataset.id;
                 const handler = new CatalogHandler();

                 handler.remove(id);
             });


             $(document).on('change', '.set-active-catalog', e => {
                 const id = e.currentTarget.dataset.id;
                 const status = e.currentTarget.checked ? 2 : 1;
                 const handler = new CatalogHandler();

                 handler.changeStatus(e.currentTarget, id, status);
             });
         }

         return Private;
    }
};

export default CatalogController;