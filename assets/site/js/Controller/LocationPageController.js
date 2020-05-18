import MapsService from "../../../js/Services/MapsService";
import locationModalMapper from "../Mapper/LocationModalMapper";
import locationPageMapper from "../Mapper/LocationPageMapper";
import LocationPageService from "../Service/LocationPageService";
import FlexSliderService from "../Service/FlexSliderService";

require('flexslider');
require('jquery-visible');

class LocationPageController {
    constructor() {
        this.mapper = locationPageMapper;
        this.modalMapper = locationModalMapper;
        this.service = new LocationPageService();
        this.gmapApi = new MapsService();

        this.gmapApi.load().then(() => {
            this.gmapApi.showMapWitMultipleMarkersWithPopupCallback(LOCATIONS, this.showLocationPopup);
        });

        this.registerEvents();
    }

    showLocationPopup(location) {
        const modalMapper = locationModalMapper;
        const fullImageWrapper = `${modalMapper.fullImageWrapper} ${modalMapper.sliders}`;
        const thumbImageWrapper = `${modalMapper.thumbImageWrapper} ${modalMapper.sliders}`;

        $(modalMapper.email).empty();
        $(modalMapper.address).empty();
        $(modalMapper.workTimeWeekend).empty();
        $(modalMapper.workTime).empty();
        $(modalMapper.description).empty();
        $(modalMapper.title).empty();
        $(modalMapper.thumbImageWrapper).remove();
        $(modalMapper.fullImageWrapper).remove();

        if (IS_MOBILE) {
            $(modalMapper.mobileTitle).addClass('hide');
            $(modalMapper.mobileTitle).empty();
        }

        $('.product-tab-area > div').append(`
            <div id="sliders" class="product-tab-content tab-content flexslider">
                <ul class="slides"></ul>
            </div>
        `);

        if (!IS_MOBILE) {
            $('.product-tab-area > div').append(`
                 <div id="carousel" class="product-tab-menu flexslider">
                    <ul class="product-tab slides"></ul>
                 </div>
            `);
        }

        for (let i = 0; i < location.images.length; i++) {
            let image = location.images[i];

            $(fullImageWrapper).append(
                `<li>
                    <img src="${image.image_link}" alt="Elipsa lokacija - ${location.title}">
                </li>`
            )

            if (!IS_MOBILE) {
                $(thumbImageWrapper).append(
                    ` <li>
                   <a href="#"><img src="${image.image_link_thumb}" alt="Elipsa lokacija - ${location.title}"></a>
                </li>`
                )
            }
        }

        if (IS_MOBILE) {
            $(modalMapper.mobileTitle).removeClass('hide');
            $(modalMapper.mobileTitle).text(location.title);
        } else {
            $(modalMapper.title).text(location.title);
        }

        $(modalMapper.description).html(location.short_description);
        $(modalMapper.workTime).text(location.working_time);
        $(modalMapper.workTimeWeekend).text(location.weekend);
        $(modalMapper.address).text(location.address);
        $(modalMapper.email).text(location.email);
        $(modalMapper.telephone).text(location.telephone);

        FlexSliderService.setProductSlider($(modalMapper.thumbImageWrapper), $(modalMapper.fullImageWrapper));

        $(modalMapper.modal).modal('show');
    }

    registerEvents() {
        $(this.mapper.countryOptions).on('change', e => {
            e.preventDefault();
            e.stopPropagation();

            this.service.getList($(e.currentTarget).val())
                .then(locations => {
                    this.gmapApi.showMapWitMultipleMarkersWithPopupCallback(locations, this.showLocationPopup);
                });
        });
    }
}

export default LocationPageController;