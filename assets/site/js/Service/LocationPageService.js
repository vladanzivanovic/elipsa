import AppHelperService from "../../../js/Helper/AppHelperService";

class LocationPageService {
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
}

export default LocationPageService;