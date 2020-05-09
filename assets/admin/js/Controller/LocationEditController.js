import BannerHandler from "../Handler/BannerHandler";
import locationEditMapper from "../Mapper/LocationEditMapper";
import MapsService from "../../../js/Services/MapsService";

class LocationEditController {
    constructor() {
        this.mapper = locationEditMapper;

        this.gmapApi = new MapsService();

        this.gmapApi.load().then(() => {
            this.gmapApi.showMap();
            this.gmapApi.registerEvents();
        });

        if (IS_EDIT) {
            this.gmapApi.setCoordinates(window.coordinates[0], window.coordinates[1]);
        }

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.submitBtn).on('click touchend', e => {
            const handler = new BannerHandler();

            handler.save(this.mapper);
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

        this.gmapApi.getMapsDataByAddress(addressArray)
            .then(() => {
                // $('input[data-lat]:checked').each((i, v) => {
                //     this.measureDistance(v);
                // })
            });
    }
}

export default LocationEditController;