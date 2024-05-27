import baseFormMapper from "../Mapper/BaseFormMapper";

class CountrySelectionEvents {
    #mapper;

    constructor() {
        if(!CountrySelectionEvents.instance) {
            this.#mapper = baseFormMapper;

            CountrySelectionEvents.instance = this;
        }

        return CountrySelectionEvents.instance;
    }

    registerEvents()
    {
        $(this.#mapper.countrySelection.selectBox).on('change', e => {
            const selectedValues = $(e.currentTarget).val();

            $(`[class*='${this.#mapper.countrySelection.countryClassPrefix}']`).addClass('hide-country');

            for (const selectedCode of selectedValues) {
                const languagesCode = COUNTRIES[selectedCode].languages;

                for (const languageCode of languagesCode) {
                    $(this.#mapper.countrySelection[`${this.#mapper.countrySelection.countryClassPrefix}${languageCode}`]).removeClass('hide-country');
                }
            }
        });

        $(this.#mapper.countrySelection.selectBox).trigger('change');
    }
}

const countrySelectionEvents = new CountrySelectionEvents();

Object.freeze(countrySelectionEvents);

export default countrySelectionEvents;
