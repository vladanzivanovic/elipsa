import BlogDataTables from "../Services/DataTables/BlogDataTables";
import BlogEditHandler from "../Handler/BlogEditHandler";
import ConfirmationModalService from "../Services/ConfirmationModalService";
import BlogDataTableEvents from "../Event/BlogDataTableEvents";

const Private = Symbol('private');

class BlogController {
    constructor() {
        const dataTable = new BlogDataTableEvents();

        this.handler = new BlogEditHandler();

        dataTable.registerEvents();
        this[Private]().registerEvents();
    }

    [Private]() {
        let Private = {};

        Private.registerEvents = () => {
            $(document).on('click touchend', '.remove-item-button', e => {
                const id = e.currentTarget.dataset.id;
                const buttons = [
                    {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-blog', 'data-id': id, 'data-dismiss': "modal"},
                ];
                const title = 'Da li ste sigurni da želite obrišete blog?';
                const confirmModal = new ConfirmationModalService(title, buttons);

                confirmModal.trigger('show');
            });

            $(document).on('change', '.set-active-blog', e => {
                const id = e.currentTarget.dataset.id;
                const status = e.currentTarget.checked ? 1 : 2;

                this.handler.changeStatus(e.currentTarget, id, status);
            });

            $(document).on('click touchend', '.remove-blog', e => {
                const id = e.currentTarget.dataset.id;

                this.handler.remove(id);
            });
        };

        return Private;
    }
}

export default BlogController;
