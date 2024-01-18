import resetPasswordPageMapper from "../Mapper/ResetPasswordPageMapper";
import resetPasswordPageValidation from "../Validators/ResetPasswordPageValidation";
import resetPasswordHandler from "../Handler/ResetPassword/ResetPasswordHandler";

class ResetPasswordPageController {
    #mapper;

    constructor() {
        this.#mapper = resetPasswordPageMapper;

        resetPasswordPageValidation.validate();

        this.registerEvents();
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click touchend', async e => {
            e.preventDefault();
            e.stopPropagation();

            await resetPasswordHandler.setNewPassword();
        });
    }
}

export default ResetPasswordPageController;
