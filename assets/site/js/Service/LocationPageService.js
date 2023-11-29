import FlexSliderService from "./FlexSliderService";
import locationPageMapper from "../Mapper/LocationPageMapper";
import locationInfoWindowDom from "../../../js/Services/Dom/LocationInfoWindowDom";
import * as JsSearch from 'js-search';

class LocationPageService {
    #infoWindowDom;
    #searcher;
    #locations;

    constructor() {
        this.mapper = locationPageMapper;
        this.#infoWindowDom = locationInfoWindowDom;
        this.#searcher = new JsSearch.Search('id');
        this.#locations = this.remapLocations();

        this.#searcher.addDocuments(this.#locations);
        this.#searcher.addIndex(['translations', LOCALE, 'title']);
        this.#searcher.addIndex(['translations', LOCALE, 'slug']);
        this.#searcher.addIndex(['translations', LOCALE, 'city']);
        this.#searcher.addIndex(['translations', LOCALE, 'country']);
        this.#searcher.addIndex(['translations', LOCALE, 'street']);
        this.#searcher.addIndex('zip_code');
    }

    getFormattedLocations()
    {
        return this.#locations;
    }

    searchLocations(searchQuery, gmapApi)
    {
        $(this.mapper.locationItem).addClass('hide');
        $(this.mapper.locationBtn).removeClass('active');

        if ('' === searchQuery) {
            $(this.mapper.locationItem).removeClass('hide');
            $(`${this.mapper.locationBtn}:visible`).eq(0).addClass('active');

            this.showLocationDetails(this.#locations[0], gmapApi);

            return;
        }

        const searchedLocationResults = this.#searcher.search(searchQuery);

        $(this.mapper.locationItem).addClass('hide');

        for (const index in searchedLocationResults) {
            $(`${this.mapper.locationItem}[data-id="${searchedLocationResults[index].id}"]`).removeClass('hide');
        }

        $(`${this.mapper.locationBtn}:visible`).eq(0).addClass('active');

        const firstItemId = $(`${this.mapper.locationItem}:visible`).eq(0).data('id');
        const firstVisibleItemIndex = searchedLocationResults.findIndex(location => location.id === firstItemId);

        this.showLocationDetails(searchedLocationResults[firstVisibleItemIndex], gmapApi);
    }

    showLocationDetails(location, gmapApi) {
        gmapApi.setCoordinates(location.coordinates.lat, location.coordinates.lng);

        this.#infoWindowDom.setParameters(location);

        gmapApi.showMap(null, this.#infoWindowDom);

        setTimeout(() => {
            gmapApi.triggerInfoWindowOpen();
        }, 350);

    }

    remapLocations()
    {
        let locationArray = [];

        for (const city in LOCATIONS) {
            locationArray = locationArray.concat(LOCATIONS[city]);
        }

        return locationArray;
    }

    getLocationPositionInArray(locationId)
    {
        return this.#locations.findIndex(location => location.id === parseInt(locationId));
    }
}

export default LocationPageService;
