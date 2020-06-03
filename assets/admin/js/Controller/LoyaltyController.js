import ConfirmationModalService from "../Services/ConfirmationModalService";
import NotificationService from "../../../js/NotificationService";
import SliderHandler from "../Handler/SliderHandler";
import HomeBannersDataTables from "../Services/DataTables/HomeBannersDataTables";
import BannerHandler from "../Handler/BannerHandler";
import LocationDataTables from "../Services/DataTables/LocationDataTables";
import LocationHandler from "../Handler/LocationHandler";
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