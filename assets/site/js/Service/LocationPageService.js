import AppHelperService from "../../../js/Helper/AppHelperService";
import FlexSliderService from "./FlexSliderService";
import locationPageMapper from "../Mapper/LocationPageMapper";

class LocationPageService {
    constructor() {
        this.mapper = locationPageMapper;
    }
    
    getList(countryCode) {
        let waitResponse = $.Deferred();

        $.ajax({
            type: "GET",
            url: AppHelperService.generateLocalizedUrl('site_api.location_list', {countryCode}),
            dataType: 'json',
            success: response => {
                waitResponse.resolve(response);
            },
            error: error => {
                waitResponse.reject(error);
            }
        })

        return waitResponse;
    }

    showLocationDetails(location, gmapApi) {
        const fullImageWrapper = `${this.mapper.fullImageWrapper} ${this.mapper.sliders}`;
        const thumbImageWrapper = `${this.mapper.thumbImageWrapper} ${this.mapper.sliders}`;

        $(this.mapper.email).empty();
        $(this.mapper.address).empty();
        $(this.mapper.workTimeWeekend).empty();
        $(this.mapper.workTime).empty();
        $(this.mapper.description).empty();
        $(this.mapper.title).empty();
        $(this.mapper.thumbImageWrapper).remove();
        $(this.mapper.fullImageWrapper).remove();

        if (IS_MOBILE) {
            $(this.mapper.mobileTitle).addClass('hide');
            $(this.mapper.mobileTitle).empty();
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

        $(this.mapper.imagesWrapper).addClass('hide');

        if (location.images.length > 0) {
            $(this.mapper.imagesWrapper).removeClass('hide');
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
            $(this.mapper.mobileTitle).removeClass('hide');
            $(this.mapper.mobileTitle).text(location.title);
        } else {
            $(this.mapper.title).text(location.title);
        }

        $(this.mapper.description).html(location.short_description);
        $(this.mapper.workTime).text(location.working_time);
        $(this.mapper.workTimeWeekend).text(location.weekend);
        $(this.mapper.address).text(location.address);
        $(this.mapper.email).text(location.email);
        $(this.mapper.telephone).text(location.telephone);

        FlexSliderService.setProductSlider($(this.mapper.thumbImageWrapper), $(this.mapper.fullImageWrapper));

        gmapApi.setCoordinates(location.lat, location.lng);

        gmapApi.showMap();
    }
}

export default LocationPageService;