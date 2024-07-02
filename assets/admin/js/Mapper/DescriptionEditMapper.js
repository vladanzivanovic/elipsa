import baseFormMapper from "./BaseFormMapper";

class DescriptionEditMapper {
    constructor() {
        // this.form = '#edit_form';
        this.typeBox = '#description_select_box';
        // this.desc_rs = '#description_rs';
        // this.desc_en = '#description_en';
        // this.submitBtn = '#description_submit';

        this.fields = {};

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            this.fields[`description_${locale}`] = '#description_'+locale;
            this.fields[`short_description_${locale}`] = '#short_description_'+locale;
            this.fields[`title_${locale}`] = '#title_'+locale;
        }

        if (!DescriptionEditMapper.instance) {
            DescriptionEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return DescriptionEditMapper.instance;
    }
}

const descriptionEditMapper = new DescriptionEditMapper();

Object.freeze(descriptionEditMapper);

export default descriptionEditMapper;
