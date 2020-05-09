class LocationEditMapper {
    constructor() {
        if (!LocationEditMapper.instance) {
            this.form = '#edit_form';
            this.submitBtn = '#location_submit';
            this.street = '#street_rs';
            this.city = '#city_rs';
            this.country = '#country_rs';

            LocationEditMapper.instance = this;
        }

        return LocationEditMapper.instance
    }
}

const locationEditMapper = new LocationEditMapper();

Object.freeze(locationEditMapper);

export default locationEditMapper;