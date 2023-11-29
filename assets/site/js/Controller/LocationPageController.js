import MapsService from "../../../js/Services/MapsService";
import locationPageMapper from "../Mapper/LocationPageMapper";
import LocationPageService from "../Service/LocationPageService";

require('flexslider');
require('jquery-visible');
require ('select2/dist/js/select2.full.min');

class LocationPageController {
    #locations;

    constructor() {
        this.mapper = locationPageMapper;
        this.service = new LocationPageService();
        this.gmapApi = new MapsService({marker: {draggable: false}});
        this.#locations = this.service.getFormattedLocations();

        this.gmapApi.load().then(() => {
            $(`${this.mapper.locationBtn}`).eq(0).addClass('active');
            this.service.showLocationDetails(this.#locations[0], this.gmapApi);
            this.gmapApi.registerEvents();
        });

        if (IS_MOBILE) {
            $(`${this.mapper.mobileDropDown}`).select2();
        }

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.locationBtn).on('click touchend', e => {
            const locationId = $(e.currentTarget).parents('.location-item').data('id');

            const index = this.#locations.findIndex(location => location.id === locationId);

            $(`${this.mapper.locationBtn}.active`).removeClass('active');
            $(e.currentTarget).addClass('active');

            this.service.showLocationDetails(this.#locations[index], this.gmapApi);
        })

        $(this.mapper.mobileDropDown).on('change', e => {
            const locationId = e.currentTarget.value;

            this.service.showLocationDetails(this.#locations[this.service.getLocationPositionInArray(locationId)], this.gmapApi);
        })

        $(this.mapper.searchInput).on('keyup', e => {
            const searchVal = $(this.mapper.searchInput).val();

            this.service.searchLocations(
                searchVal,
                this.gmapApi
            );

            if ('' !== searchVal) {
                $(this.mapper.resetSearchBtn).removeClass('hide');

                return;
            }

            $(this.mapper.resetSearchBtn).addClass('hide');
        });

        $(this.mapper.searchInput).on('blur', e => {
            setTimeout(() => {
                $(e.currentTarget).focus()
            }, 10);
        });

        $(this.mapper.resetSearchBtn).on('click', e => {
            $(this.mapper.searchInput).val('');

            $(this.mapper.searchInput).trigger('keyup');
        })
    }
}

export default LocationPageController;
