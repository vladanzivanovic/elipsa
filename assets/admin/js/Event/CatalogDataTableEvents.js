import sliderDataTables from "../Services/DataTables/SliderDataTables";
import BaseDataTableEvents from "./BaseDataTableEvents";
import catalogDataTables from "../Services/DataTables/CatalogDataTables";

class CatalogDataTableEvents extends BaseDataTableEvents {
    #parent;

    constructor() {
        const parent = super(catalogDataTables);

        this.#parent = parent;

        super.registerEvents();
    }
}

export default CatalogDataTableEvents;
