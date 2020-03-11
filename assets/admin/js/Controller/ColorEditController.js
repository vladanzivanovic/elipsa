import ColorEditMapper from "../Mapper/ColorEditMapper";
import ColorHandler from "../Handler/Product/ColorHandler";
import('bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css');
require('bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min');

class ColorEditController {
    constructor() {
        this.mapper = new ColorEditMapper();

        this.mapper.color.colorpicker();

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.submitBtn.on('click touchend', e => {
            const handler = new ColorHandler();

            handler.save(this.mapper);
        });
    }
}

export default ColorEditController;