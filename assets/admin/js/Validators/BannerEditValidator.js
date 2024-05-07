import bannerEditMapper from "../Mapper/BannerEditMapper";

require ('../../../js/Validators/ValidationRuleHelper');

class BannerEditValidator {
    #mapper;

    constructor() {
        if (!BannerEditValidator.instance) {
            this.#mapper = bannerEditMapper;

            BannerEditValidator.instance = this;
        }

        return BannerEditValidator.instance;
    }

    validate() {
        let options;

        options = {
            rules: {
                position: 'required',
                banner: {
                    dropZoneHasImage: true,
                    dropZoneHasMainImage: true,
                },
                banner_mobile: {
                    dropZoneHasImage: true,
                    dropZoneHasMainImage: true,
                },
                type: {
                    isSelectBoxEmpty: true,
                },
                'available_countries[]': {
                    isMultiSelectBoxEmpty: true,
                }
            },
        };

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            options.rules[`${locale}_button`] = {required: true};
            options.rules[`${locale}_link`] = {required: true};
        }

        $.extend(options, window.helpBlock);

        return $(this.#mapper.form).validate(options);
    }
}

const bannerEditValidator = new BannerEditValidator();

Object.freeze(bannerEditValidator);

export default bannerEditValidator;
