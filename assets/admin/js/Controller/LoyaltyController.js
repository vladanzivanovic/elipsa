import NotificationService from "../../../js/NotificationService";
import LoyaltyDataTables from "../Services/DataTables/LoyaltyDataTables";

const Private = Symbol('private');

class LoyaltyController {
    constructor() {
        if (CAN_VIEW) {
            LoyaltyDataTables().init();
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

export default LoyaltyController;