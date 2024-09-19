import ConfirmationModalService from "../Services/ConfirmationModalService";
import ColorsDataTables from "../Services/DataTables/ColorsDataTables";
import NotificationService from "../../../js/NotificationService";
import ColorHandler from "../Handler/Product/ColorHandler";
import SizesDataTables from "../Services/DataTables/SizesDataTables";
import SizeHandler from "../Handler/SizeHandler";

const Private = Symbol('private');

class SizesController {
    constructor() {
        if (CAN_VIEW) {
            SizesDataTables().init();
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
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-product', 'data-id': id, 'data-dismiss': "modal"},
                 ];
                 const title = 'Da li ste sigurni da želite obrišete veličinu?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-product', e => {
                 const id = e.currentTarget.dataset.id;
                 const handler = new SizeHandler();

                 handler.remove(id);
             });
         }

         return Private;
    }
};

export default SizesController;
