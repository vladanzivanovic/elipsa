import sliderDataTables from "../Services/DataTables/SliderDataTables";
import BaseDataTableEvents from "./BaseDataTableEvents";
import blogDataTables from "../Services/DataTables/BlogDataTables";

class BlogDataTableEvents extends BaseDataTableEvents {
    #parent;

    constructor() {
        const parent = super(blogDataTables);

        this.#parent = parent;

        super.registerEvents();
    }
}

export default BlogDataTableEvents;
