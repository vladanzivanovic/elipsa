class LocationModalMapper {
    constructor() {
        if(!LocationModalMapper.instance) {
            this.modal = '#locationModal';
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

            LocationModalMapper.instance = this;
        }

        return LocationModalMapper.instance;
    }
}

const locationModalMapper = new LocationModalMapper();

Object.freeze(locationModalMapper);

export default locationModalMapper;