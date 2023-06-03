import baseFormMapper from "./BaseFormMapper";

class OfficeContactEditMapper {
    constructor() {
        if (!OfficeContactEditMapper.instance) {
            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this[`title_${locale}`] = '#title_'+locale;
            }

            this[`telephone`] = '#telephone';

            OfficeContactEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return OfficeContactEditMapper.instance;
    }
}

const officeContactEditMapper = new OfficeContactEditMapper();

Object.freeze(officeContactEditMapper);

export default officeContactEditMapper;
