import MapsService from "../../../js/Services/MapsService";
import locationPageMapper from "../Mapper/LocationPageMapper";
import LocationPageService from "../Service/LocationPageService";

require('flexslider');
require('jquery-visible');

class LocationPageController {
    constructor() {
        this.mapper = locationPageMapper;
        this.service = new LocationPageService();
        this.gmapApi = new MapsService();

        this.gmapApi.load().then(() => {
            this.service.showLocationDetails(LOCATIONS[0], this.gmapApi);
        });

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.locationBtn).on('click touchend', e => {
            const index = e.currentTarget.dataset.index;

            $(`${this.mapper.locationBtn}.active`).removeClass('active');
            $(e.currentTarget).addClass('active');

            this.service.showLocationDetails(LOCATIONS[index], this.gmapApi);
        })

        $(this.mapper.mobileDropDown).on('change', e => {
            const index = e.currentTarget.value;

            this.service.showLocationDetails(LOCATIONS[index], this.gmapApi);
        })
    }
}

export default LocationPageController;