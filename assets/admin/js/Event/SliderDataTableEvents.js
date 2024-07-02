import sliderDataTables from "../Services/DataTables/SliderDataTables";
import BaseDataTableEvents from "./BaseDataTableEvents";
import {Router} from "../../../../public/bundles/fosjsrouting/js/router";

class SliderDataTableEvents extends BaseDataTableEvents {
    #parent;

    constructor() {
        const parent = super(sliderDataTables);

        this.#parent = parent;
    }

    registerEvents() {
        this.#parent.getDataTable().on('row-reorder', (e, diff, edit) => {
            let data = {};
            for(let i = 0; i < diff.length; i++) {
                let rowData = this.#parent.getDataTable().row( diff[i].node ).data();

                data[rowData.id] = {
                    'id': rowData.id,
                    'position': diff[i].newPosition + 1,
                };
            }

            $.ajax({
                type: 'POST',
                url: Routing.generate('admin.set_sliders_position'),
                data: {'rows': JSON.stringify(data)},
                dataType: 'json',
                success: response => {
                    this.#parent.getDataTable().reload();
                },
                error: error => {

                },
            })
        })

        super.registerEvents();
    }
}

export default SliderDataTableEvents;
