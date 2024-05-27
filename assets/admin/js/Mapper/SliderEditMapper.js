import baseFormMapper from "./BaseFormMapper";

class SliderEditMapper {
    constructor() {
        if (!SliderEditMapper.instance) {

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this[`description_${locale}`] = '#description_'+locale;
                this[`button_link_${locale}`] = '#button_link_'+locale;
            }

            SliderEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return SliderEditMapper.instance;
    }
}
const sliderEditMapper = new SliderEditMapper();

Object.freeze(sliderEditMapper);

export default sliderEditMapper;
