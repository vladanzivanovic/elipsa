import ColorHandler from "../Handler/Product/ColorHandler";
import TagEditMapper from "../Mapper/TagEditMapper";
import TagHandler from "../Handler/Product/TagHandler";
import SizeEditMapper from "../Mapper/SizeEditMapper";
import SizeHandler from "../Handler/SizeHandler";

class SizeEditController {
    constructor() {
        this.mapper = new SizeEditMapper();

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.submitBtn.on('click touchend', e => {
            const handler = new SizeHandler();

            handler.save(this.mapper);
        });
    }
}

export default SizeEditController;