import MapsService from "../../../js/Services/MapsService";

class ContactPageController {
    constructor() {
        this.gmapApi = new MapsService();
        $('#map_canvas').addClass('desktop');

        if (IS_MOBILE) {
            $('#map_canvas').removeClass('desktop');
            $('#map_canvas').addClass('mobile');
        }

        this.gmapApi.load().then(() => {
            this.gmapApi.showMap();

            this.gmapApi.getMapsDataByAddress(ADDRESS, true);
        });

    }
}

export default ContactPageController;