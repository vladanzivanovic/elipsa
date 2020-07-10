import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import SliderHandler from "../Handler/SliderHandler";
import HomeBannersDataTables from "../Services/DataTables/HomeBannersDataTables";
import BannerHandler from "../Handler/BannerHandler";
import BannersDataTables from "../Services/DataTables/BannersDataTables";

const Private = Symbol('private');

class BannersController {
    constructor() {
        if (CAN_VIEW) {
            BannersDataTables().init();
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
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-banner', 'data-id': id, 'data-dismiss': "modal"},
                 ];
                 const title = 'Da li ste sigurni da želite obrišete baner?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-banner', e => {
                 const id = e.currentTarget.dataset.id;
                 const handler = new BannerHandler();

                 handler.remove(id);
             });


             $(document).on('change', '.set-active-banner', e => {
                 const id = e.currentTarget.dataset.id;
                 const status = e.currentTarget.checked ? 1 : 0;
                 const handler = new BannerHandler();

                 handler.changeStatus(e.currentTarget, id, status);
             });
         }

         return Private;
    }
};

export default BannersController;