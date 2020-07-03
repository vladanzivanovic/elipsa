import locationEditMapper from "../Mapper/LocationEditMapper";
import MapsService from "../../../js/Services/MapsService";
import DropZoneService from "../../../js/Services/DropZoneService";
import LocationHandler from "../Handler/LocationHandler";

class LocationEditController {
    constructor() {
        this.mapper = locationEditMapper;

        this.gmapApi = new MapsService();

        this.dropZone = DropZoneService();
        this.dropZone.init($('[data-files="location"]'));

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

        if (!addressArray[0] || !addressArray[1] || !addressArray[2]) {
            return ;
        }

        this.gmapApi.getMapsDataByAddress(addressArray, true);
    }
}

export default LocationEditController;