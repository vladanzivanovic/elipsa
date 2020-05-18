import locationEditMapper from "../Mapper/LocationEditMapper";
import MapsService from "../../../js/Services/MapsService";
import DropZoneService from "../../../js/Services/DropZoneService";
import LocationHandler from "../Handler/LocationHandler";

class LocationEditController {
    constructor() {
        this.mapper = locationEditMapper;

        this.gmapApi = new MapsService();

        this.dropZone = DropZoneService();
        this.dropZone.init(this.mapper.form);

        this.gmapApi.load().then(() => {
            if (IS_EDIT) {
                this.gmapApi.setCoordinates(LAT, LNG);
            }

            this.gmapApi.showMap();
            this.gmapApi.registerEvents();
        });

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES, 'location');
        }

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.submitBtn).on('click touchend', e => {
            const handler = new LocationHandler();

            handler.save();
        });

        $(this.mapper.city).on('keyup', () => {
            this.getMapByAddress();
        });
        $(this.mapper.street).on('keyup', () => {
            this.getMapByAddress();
        });
        $(this.mapper.country).on('keyup', () => {
            this.getMapByAddress();
        });
    }

    getMapByAddress() {
        let addressArray = [
            $(this.mapper.street).val(),
            $(this.mapper.city).val(),
            $(this.mapper.country).val(),
        ];

        this.gmapApi.getMapsDataByAddress(addressArray);

        this.gmapApi.getMapsDataByAddress([$(this.mapper.country).val()])
            .then(response => {
                const viewport = response[0].geometry.viewport;

                $(this.mapper.countryNorthLat).val(viewport.Ya.j);
                $(this.mapper.countryNorthLng).val(viewport.Ua.j);
                $(this.mapper.countrySouthLat).val(viewport.Ya.i);
                $(this.mapper.countrySouthLng).val(viewport.Ua.i);
                $(this.mapper.countryLat).val(response[0].geometry.location.lat);
                $(this.mapper.countryLng).val(response[0].geometry.location.lng);
                $(this.mapper.countryShortCode).val(response[0].address_components[0].short_name);
            });
    }
}

export default LocationEditController;