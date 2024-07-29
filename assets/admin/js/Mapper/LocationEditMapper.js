import baseFormMapper from "./BaseFormMapper";

class LocationEditMapper {
    constructor() {
        if (!LocationEditMapper.instance) {
            this.fields = {
                zip_code: '#zip_code',
                working_hours: '#working_hours',
                working_hours_saturday: '#working_hours_saturday',
                working_hours_sunday: '#working_hours_sunday',
                email: '#email',
                telephone: '#telephone',
            };

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this.fields[`title_${locale}`] = '#title_'+locale;
                this.fields[`street_${locale}`] = '#street_'+locale;
                this.fields[`city_${locale}`] = '#city_'+locale;
                this.fields[`country_${locale}`] = '#country_'+locale;
            }

            LocationEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return LocationEditMapper.instance
    }
}

const locationEditMapper = new LocationEditMapper();

Object.freeze(locationEditMapper);

export default locationEditMapper;
