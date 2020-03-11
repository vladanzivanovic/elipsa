import ColorHandler from "../Handler/Product/ColorHandler";
import TagEditMapper from "../Mapper/TagEditMapper";
import TagHandler from "../Handler/Product/TagHandler";

class TagEditController {
    constructor() {
        this.mapper = new TagEditMapper();

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.submitBtn.on('click touchend', e => {
            const handler = new TagHandler();

            handler.save(this.mapper);
        });
    }
}

export default TagEditController;