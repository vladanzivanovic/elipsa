import resetPasswordPageMapper from "../Mapper/ResetPasswordPageMapper";
import ResetPasswordPageHandler from "../Handler/ResetPasswordPageHandler";
import resetPasswordPageValidation from "../Validators/ResetPasswordPageValidation";

class ResetPasswordPageController {
    constructor() {
        this.mapper = resetPasswordPageMapper;
        this.validator = resetPasswordPageValidation;
        this.handler = new ResetPasswordPageHandler();

        this.validator.validate(this.mapper.form);

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

export default ResetPasswordPageController;