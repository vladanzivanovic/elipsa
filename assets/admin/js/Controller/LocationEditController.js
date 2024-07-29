import locationEditMapper from "../Mapper/LocationEditMapper";
import MapsService from "../../../js/Services/MapsService";
import DropZoneService from "../../../js/Services/DropZoneService";
import LocationHandler from "../Handler/LocationHandler";
import productEditValidator from "../Validators/ProductEditValidator";
import locationEditValidator from "../Validators/LocationEditValidator";
import baseEvents from "./BaseEvents";

class LocationEditController {
    #mapper;
    #validator;
    #gmapApi;
    #dropZone;
    #baseEvents

    constructor() {
        this.#baseEvents = baseEvents;
        this.#mapper = locationEditMapper;
        this.#validator = locationEditValidator;

        this.#gmapApi = new MapsService();

        this.#dropZone = DropZoneService();

        this.#initForm();

        this.#registerEvents();
    }

    #initForm()
    {
        $(`${this.#mapper.form} select`).select2({
            minimumResultsForSearch: -1
        });

        this.#dropZone.init($('[data-files="location"]'));

        this.#gmapApi.load().then(() => {
            if (IS_EDIT) {
                this.#gmapApi.setCoordinates(LAT, LNG);
            }

            this.#gmapApi.showMap();
            this.#gmapApi.registerEvents();
        });

        if (IS_EDIT) {
            this.#dropZone.setFiles(IMAGES, 'location');
        }

        this.#validator.validate(this.#mapper.form);
    }

    #registerEvents() {
        $(this.#mapper.submitBtn).on('click touchend', e => {
            const handler = new LocationHandler();

            handler.save();
        });

        $(this.#mapper.fields.city_en).on('keyup', () => {
            this.getMapByAddress();
        });
        $(this.#mapper.fields.street_en).on('keyup', () => {
            this.getMapByAddress();
        });
        $(this.#mapper.fields.country_en).on('keyup', () => {
            this.getMapByAddress();
        });

        this.#baseEvents.events();
    }

    getMapByAddress() {
        let addressArray = [
            $(this.#mapper.fields.street_en).val(),
            $(this.#mapper.fields.city_en).val(),
            $(this.#mapper.fields.country_en).val(),
        ];

        if (!addressArray[0] || !addressArray[1] || !addressArray[2]) {
            return ;
        }

        this.#gmapApi.getMapsDataByAddress(addressArray, true);
    }
}

export default LocationEditController;
