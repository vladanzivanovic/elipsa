class AboutUsPageMapper {
    constructor() {
        this.form = '#edit_form';
        this.desc_rs = '#about_us_description_rs';
        this.desc_en = '#about_us_description_en';
        this.submitBtn = '#about_us_submit';

        if (!AboutUsPageMapper.instance) {
            AboutUsPageMapper.instance = this;
        }

        return AboutUsPageMapper.instance;
    }
}

const aboutUsPageMapper = new AboutUsPageMapper();

Object.freeze(aboutUsPageMapper);

export default aboutUsPageMapper;