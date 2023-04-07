class SliderEditMapper {
    constructor() {
        if (!SliderEditMapper.instance) {
            this.form = $('#edit_form');
            this.descriptionRs = $('#description_rs', this.form);
            this.buttonTextRs = $('#button_rs', this.form);
            this.buttonLinkRs = $('#linkRs', this.form);
            this.descriptionEn = $('#description_en', this.form);
            this.buttonTextEn = $('#button_en', this.form);
            this.buttonLinkEn = $('#button_en', this.form);
            this.submitBtn = $('#slider_submit');

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this[`title_${locale}`] = '#title_'+locale;
                this[`structure_${locale}`] = '#structure_'+locale;
                this[`remark_${locale}`] = '#remark_'+locale;
                this[`cleaning_${locale}`] = '#cleaning_'+locale;
            }

            SliderEditMapper.instance = this;
        }

        return SliderEditMapper.instance;
    }
}
const sliderEditMapper = new SliderEditMapper();

Object.freeze(sliderEditMapper);

export default sliderEditMapper;
