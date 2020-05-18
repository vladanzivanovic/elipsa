class LocationPageMapper {
    constructor() {
        if(!LocationPageMapper.instance) {
            this.countryOptions = '#country-list';

            LocationPageMapper.instance = this;
        }

        return LocationPageMapper.instance;
    }
}

const locationPageMapper = new LocationPageMapper();

Object.freeze(locationPageMapper);

export default locationPageMapper;