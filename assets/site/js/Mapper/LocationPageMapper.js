class LocationPageMapper {
    constructor() {
        if(!LocationPageMapper.instance) {
            this.countryOptions = '#country-list';
            this.locationBtn = '.location-btn';
            this.fullImageWrapper = '#sliders';
            this.thumbImageWrapper = '#carousel';
            this.sliders = '.slides';
            this.title = '.spd-title';
            this.description = '.spd-text';
            this.workTime = '.work-time';
            this.workTimeWeekend = '.work-time-weekend';
            this.address = '.address-info';
            this.email = '.email-info';
            this.telephone = '.telephone-info';
            this.mobileTitle = '#mobile-title';
            this.imagesWrapper = '.images-wrapper';
            this.mobileDropDown = '.location-drop-down';

            LocationPageMapper.instance = this;
        }

        return LocationPageMapper.instance;
    }
}

const locationPageMapper = new LocationPageMapper();

Object.freeze(locationPageMapper);

export default locationPageMapper;