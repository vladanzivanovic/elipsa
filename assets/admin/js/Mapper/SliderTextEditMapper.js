import baseFormMapper from "./BaseFormMapper";

class SliderTextEditMapper {
    constructor() {
        if (!SliderTextEditMapper.instance) {
            this.fields = {};

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this.fields[`description_${locale}`] = '#description_'+locale;
                this.fields[`link_${locale}`] = '#link_'+locale;
            }

            SliderTextEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return SliderTextEditMapper.instance;
    }
}

const sliderTextEditMapper = new SliderTextEditMapper();

Object.freeze(sliderTextEditMapper);

export default sliderTextEditMapper;
