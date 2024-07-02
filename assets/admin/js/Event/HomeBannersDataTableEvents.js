import sliderDataTables from "../Services/DataTables/SliderDataTables";
import BaseDataTableEvents from "./BaseDataTableEvents";
import homeBannersDataTables from "../Services/DataTables/HomeBannersDataTables";

class HomeBannersDataTableEvents extends BaseDataTableEvents {
    #parent;

    constructor() {
        const parent = super(homeBannersDataTables);

        this.#parent = parent;
    }

    registerEvents() {
        super.registerEvents();
    }
}

export default HomeBannersDataTableEvents;
