import ProductDataTables from "../Services/DataTables/ProductDataTables";
// import ProductRemoveHandler from "../Handler/Product/ProductRemoveHandler";
// import ProductEditHandler from "../Handler/Product/ProductEditHandler";
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
             //
             // $(document).on('click touchend', '.remove-product', e => {
             //     const alias = e.currentTarget.dataset.alias;
             //
             //     ProductRemoveHandler.remove(alias);
             // });
         }

         return Private;
    }
};

export default DashboardController;