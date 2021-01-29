import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import TagsDataTables from "../Services/DataTables/TagsDataTables";
import TagHandler from "../Handler/Product/TagHandler";

const Private = Symbol('private');

class TagController {
    constructor() {
        // if (CAN_VIEW) {
            TagsDataTables().init();
        // }
        this.notification = NotificationService();

        this[Private]().registerEvents();
    }

    [Private]() {
        let Private = {};

         Private.registerEvents = () => {
             $(document).on('click touchend', '.remove-item-button', e => {
                 const slug = e.currentTarget.dataset.slug;
                 const totalProducts = e.currentTarget.dataset.totalProducts;

                 const buttons = [
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-item', 'data-slug': slug, 'data-dismiss': "modal"},
                 ];
                 let title = 'Da li ste sigurni da želite obrišete tag?';

                 if (totalProducts > 0) {
                     let linkWith = 'proizvodima';

                     if (ROUTE_SUB_NAME === 'blog') {
                         linkWith = 'blogovima';
                     }

                     title += ` Veza sa ${linkWith} biće obrisana takođe.`
                 }

                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-item', e => {
                 const slug = e.currentTarget.dataset.slug;
                 const handler = new TagHandler();

                 handler.remove(slug);
             });
         }

         return Private;
    }
};

export default TagController;