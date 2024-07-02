class BaseFormMapper {
    constructor() {
        if (!BaseFormMapper.instance) {
            this.form = '#edit_form';
            this.submitBtn = '#submit_btn';

            this.countrySelection = {
                selectBox: '#country-select-box',
                countryClassPrefix: 'country_',
            }

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this.countrySelection[`country_${locale}`] = '.country_'+locale;
            }

            BaseFormMapper.instance = this;
        }

        return BaseFormMapper.instance;
    }
}

const baseFormMapper = new BaseFormMapper();

Object.freeze(baseFormMapper);
export default baseFormMapper;
