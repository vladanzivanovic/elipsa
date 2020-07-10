import SizeEditMapper from "../Mapper/SizeEditMapper";
import SizeHandler from "../Handler/SizeHandler";
import sizeEditValidator from "../Validators/SizeEditValidator";

class SizeEditController {
    constructor() {
        this.mapper = new SizeEditMapper();
        this.validator = sizeEditValidator;

        this.validator.validate(this.mapper.form);

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