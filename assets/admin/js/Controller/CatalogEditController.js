import DropZoneService from "../../../js/Services/DropZoneService";
import catalogMapper from "../Mapper/CatalogMapper";
import CatalogHandler from "../Handler/CatalogHandler";
import countrySelectionEvents from "../Event/CountrySelectionEvents";

class CatalogEditController {
    #mapper;
    #handler;
    #countrySelectionEvents;
    constructor() {
        this.#mapper = catalogMapper;
        this.#handler = new CatalogHandler();
        this.#countrySelectionEvents = countrySelectionEvents;

        const dropZone = DropZoneService();

        dropZone.init($('[data-files="catalog"]'));

        $(`${this.#mapper.form} select`).select2({
            minimumResultsForSearch: -1
        });

        if (IS_EDIT) {
            dropZone.setFiles(IMAGES, 'catalog');
        }

        this.registerEvents();
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click', e => {
            this.#handler.save();
        });

        this.#countrySelectionEvents.registerEvents();
    }
}

export default CatalogEditController;
