import bannerEditMapper from "../Mapper/BannerEditMapper";
import bannerEditManipulator from "../Manipulator/BannerEditManipulator";
import BannerHandler from "../Handler/BannerHandler";

class BannerEditEvents {
    #mapper;
    #handler;
    #manipulator;

    constructor() {
        if(!BannerEditEvents.instance) {
            this.#mapper = bannerEditMapper;
            this.#handler = new BannerHandler();
            this.#manipulator = bannerEditManipulator;

            BannerEditEvents.instance = this;
        }

        return BannerEditEvents.instance;
    }

    registerEvents()
    {
        $(this.#mapper.submitBtn).on('click', e => {
            this.#handler.save();
        });

        $(this.#mapper.countrySelectBox).on('change', e => {
            const selectedValues = $(e.currentTarget).val();

            $(`[id^='language_']`).addClass('hide-country');

            for (const selectedCode of selectedValues) {
                const languagesCode = COUNTRIES[selectedCode].languages;

                for (const languageCode of languagesCode) {
                    $(this.#mapper[`language_${languageCode}`]).removeClass('hide-country');
                }
            }
        })

        $(document).on('change', this.#mapper.type, e => {
            const type = parseInt($(e.currentTarget).val());

            switch (type) {
                case BANNER_TYPES.TYPE_NEWS_LETTER:
                    this.#manipulator.removeLinks();
                    this.#manipulator.showMobileDropZone();
                    break;
                case BANNER_TYPES.TYPE_POP_UP:
                    this.#manipulator.removeButtons();
                    this.#manipulator.showLinks();
                    this.#manipulator.showMobileDropZone();
                    break;
                case BANNER_TYPES.TYPE_MENU:
                case BANNER_TYPES.TYPE_SEASON:
                    this.#manipulator.showLinks();
                    this.#manipulator.removeButtons();
                    this.#manipulator.removeMobileDropzone();
                    break;
                case BANNER_TYPES.TYPE_LOYALTY:
                    this.#manipulator.removeButtons();
                    this.#manipulator.showLinks();
                    this.#manipulator.showMobileDropZone();
                    break;
                default:
                    this.#manipulator.showButtons();
                    this.#manipulator.showLinks();
                    this.#manipulator.showMobileDropZone();
            }
        });

        $(this.#mapper.type).trigger('change');
        $(this.#mapper.countrySelectBox).trigger('change');
    }
}

const bannerEditEvents = new BannerEditEvents();

Object.freeze(bannerEditEvents);

export default bannerEditEvents;
