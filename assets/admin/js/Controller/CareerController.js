import NotificationService from "../../../js/NotificationService";
import CareerDataTables from "../Services/DataTables/CareerDataTables";

const Private = Symbol('private');

class CareerController {
    constructor() {
        if (CAN_VIEW) {
            CareerDataTables().init();
        }
        this.notification = NotificationService();

        // this[Private]().registerEvents();
    }

    [Private]() {
        let Private = {};

         Private.registerEvents = () => {

         }

         return Private;
    }
};

export default CareerController;