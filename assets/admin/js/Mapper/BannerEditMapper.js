import baseFormMapper from "./BaseFormMapper";

class BannerEditMapper {
    constructor() {
        if (!BannerEditMapper.instance) {
            this.descriptionRs = '#description_rs';
            this.buttonTextRs = '#button_rs';
            this.buttonLinkRs = '#linkRs';
            this.descriptionEn = '#description_en';
            this.buttonTextEn = '#button_en';
            this.buttonLinkEn = '#button_en';
            this.countrySelectBox = '#country-select-box';
            this.type = '#banner-type';

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this[`language_${locale}`] = '#language_'+locale;
            }

            BannerEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return BannerEditMapper.instance;
    }
}

const bannerEditMapper = new BannerEditMapper();

Object.freeze(bannerEditMapper);

export default bannerEditMapper;
