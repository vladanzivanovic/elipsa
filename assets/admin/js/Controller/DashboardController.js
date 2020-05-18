import ProductDataTables from "../Services/DataTables/ProductDataTables";
import ConfirmationModalService from "../Services/ConfirmationModalService";
import ProductEditHandler from "../Handler/Product/ProductEditHandler";

const Private = Symbol('private');

class DashboardController {
    constructor() {
        if (CAN_VIEW) {
            ProductDataTables().init();
        }
        this[Private]().registerEvents();
    }

    [Private]() {
        let Private = {};

         Private.registerEvents = () => {
             $(document).on('click touchend', '.remove-item-button', e => {
                 const alias = e.currentTarget.dataset.alias;
                 const buttons = [
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-product', 'data-alias': alias, 'data-dismiss': "modal"},
                 ];
                 const title = 'Da li ste sigurni da želite obrišete oglas?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('change', '.set-active-product', e => {
                 const slug = e.currentTarget.dataset.slug;
                 const status = e.currentTarget.checked ? 2 : 1;
                 const handler = new ProductEditHandler();

                 handler.changeStatus(e.currentTarget, slug, status);
             });

             $(document).on('change', '.set-home-page', e => {
                 const slug = e.currentTarget.dataset.slug;
                 const status = e.currentTarget.checked ? 1 : 0;
                 const handler = new ProductEditHandler();

                 handler.toggleShowHomePage(e.currentTarget, slug, status);
             });
         }

         return Private;
    }
};

export default DashboardController;