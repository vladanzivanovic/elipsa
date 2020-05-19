import BlogDataTables from "../Services/DataTables/BlogDataTables";
import BlogEditHandler from "../Handler/BlogEditHandler";
import ConfirmationModalService from "../Services/ConfirmationModalService";
import UserEditHandler from "../Handler/UserEditHandler";

const Private = Symbol('private');

class BlogIndexController {
    constructor() {
        if (CAN_VIEW) {
            BlogDataTables().init();
        }

        this[Private]().registerEvents();
    }

    [Private]() {
        let Private = {};

        Private.registerEvents = () => {
            $(document).on('click touchend', '.remove-item-button', e => {
                const alias = e.currentTarget.dataset.alias;
                const buttons = [
                    {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-blog', 'data-alias': alias, 'data-dismiss': "modal"},
                ];
                const title = 'Da li ste sigurni da želite obrišete blog?';
                const confirmModal = new ConfirmationModalService(title, buttons);

                confirmModal.trigger('show');
            });

            $(document).on('change', '.set-active-blog', e => {
                const alias = e.currentTarget.dataset.alias;
                const status = e.currentTarget.checked ? 1 : 2;

                BlogEditHandler.changeStatus(e.currentTarget, alias, status);
            });

            $(document).on('click touchend', '.remove-blog', e => {
                const alias = e.currentTarget.dataset.alias;

                BlogEditHandler.remove(alias);
            });
        };

        return Private;
    }
}

export default BlogIndexController;