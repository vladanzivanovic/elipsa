import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import JobsHandler from "../Handler/JobsHandler";
import CareerDescriptionDataTableEvents from "../Event/CareerDescriptionDataTableEvents";

const Private = Symbol('private');

class JobsController {
    constructor() {
        const dataTable = new CareerDescriptionDataTableEvents();

        this.notification = NotificationService();

        dataTable.registerEvents();

        this[Private]().registerEvents();
    }

    [Private]() {
        let Private = {};

         Private.registerEvents = () => {
             $(document).on('click touchend', '.remove-item-button', e => {
                 const id = e.currentTarget.dataset.id;
                 const buttons = [
                     {type: 'button', text: 'Obriši', 'class': 'btn btn-primary remove-job', 'data-id': id, 'data-dismiss': "modal"},
                 ];
                 const title = 'Da li ste sigurni da želite obrišete zaposlenje?';
                 const confirmModal = new ConfirmationModalService(title, buttons);

                 confirmModal.trigger('show');
             });

             $(document).on('click touchend', '.remove-job', e => {
                 const id = e.currentTarget.dataset.id;
                 const handler = new JobsHandler();

                 handler.remove(id);
             });


             $(document).on('change', '.set-active-job', e => {
                 const id = e.currentTarget.dataset.id;
                 const status = e.currentTarget.checked ? ENTITY_STATUSES.STATUS_ACTIVE : ENTITY_STATUSES.STATUS_PENDING;
                 const handler = new JobsHandler();

                 handler.changeStatus(e.currentTarget, id, status);
             });
         }

         return Private;
    }
};

export default JobsController;
