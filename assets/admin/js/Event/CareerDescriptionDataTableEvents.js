import BaseDataTableEvents from "./BaseDataTableEvents";
import jobsDataTables from "../Services/DataTables/JobsDataTables";

class CareerDescriptionDataTableEvents extends BaseDataTableEvents {
    #parent;

    constructor() {
        const parent = super(jobsDataTables);

        this.#parent = parent;

        super.registerEvents();
    }
}

export default CareerDescriptionDataTableEvents;
