import NotificationService from "../../../js/NotificationService";
import LoyaltyDataTables from "../Services/DataTables/LoyaltyDataTables";
import CollaboratorsDataTables from "../Services/DataTables/CollaboratorsDataTables";

const Private = Symbol('private');

class CollaboratorsController {
    constructor() {
        if (CAN_VIEW) {
            CollaboratorsDataTables().init();
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

export default CollaboratorsController;