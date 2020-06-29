import collaboratorPageMapper from "../Mapper/CollaboratorPageMapper";
import collaboratorPageValidation from "../Validators/CollaboratorPageValidation";
import CollaboratorPageHandler from "../Handler/CollaboratorPageHandler";

class CollaboratorPageController {
    constructor() {
        this.mapper = collaboratorPageMapper;
        this.validator = collaboratorPageValidation;
        this.handler = new CollaboratorPageHandler();

        this.validator.validate();

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.submitBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.handler.save();
        });
    }
}

export default CollaboratorPageController;