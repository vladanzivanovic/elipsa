require ('../../../js/Validators/ValidationRuleHelper');

class LocationEditValidator {
    constructor() {
        if (!LocationEditValidator.instance) {
            LocationEditValidator.instance = this;
        }

        return LocationEditValidator.instance;
    }

    validate(form) {
        let options;

        options = {
            rules: {
                working_hours: 'required',
                working_hours_saturday: 'required',
                email: 'email',
                zip_code: 'required',
                location: {
                    dropZoneHasImage: true,
                    dropZoneHasMainImage: true,
                }
            },
        };

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            options.rules[`translations[${locale}][title]`] = 'required';
            options.rules[`translations[${locale}][street]`] = 'required';
            options.rules[`translations[${locale}][city]`] = 'required';
            options.rules[`translations[${locale}][country]`] = 'required';
        }

        $.extend(options, window.helpBlock);

        return $(form).validate(options);
    }
}

const locationEditValidator = new LocationEditValidator();

Object.freeze(locationEditValidator);

export default locationEditValidator;
