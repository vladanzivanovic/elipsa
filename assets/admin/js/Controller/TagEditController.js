import TagEditMapper from "../Mapper/TagEditMapper";
import TagHandler from "../Handler/Product/TagHandler";
import tagEditValidator from "../Validators/TagEditValidator";

class TagEditController {
    constructor() {
        this.mapper = new TagEditMapper();
        this.validator = tagEditValidator;

        this.validator.validate(this.mapper.form);

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