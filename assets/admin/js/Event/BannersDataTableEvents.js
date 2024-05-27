import sliderDataTables from "../Services/DataTables/SliderDataTables";
import BaseDataTableEvents from "./BaseDataTableEvents";
import bannersDataTables from "../Services/DataTables/BannersDataTables";

class BannersDataTableEvents extends BaseDataTableEvents {
    #parent;

    constructor() {
        const parent = super(bannersDataTables);

        this.#parent = parent;
    }

    registerEvents() {
        super.registerEvents();
    }
}

export default BannersDataTableEvents;
