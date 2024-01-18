import loader from "../../Dom/LoaderDom";
import FormHelperService from "../../../../js/Helper/FormHelperService";
import toastrService from "../../../../js/Services/ToastrService";
import AppHelperService from "../../../../js/Helper/AppHelperService";
import resetPasswordMapper from "../../Mapper/Embedded/ResetPasswordMapper";
import resetPasswordApiHandler from "./ResetPasswordApiHandler";
import resetPasswordPageMapper from "../../Mapper/ResetPasswordPageMapper";

class ResetPasswordHandler {
    #mapper;
    #pageMapper;
    #apiHandler;
    #toastr;

    constructor() {
        if (!ResetPasswordHandler.instance) {
            this.#mapper = resetPasswordMapper;
            this.#pageMapper = resetPasswordPageMapper;
            this.#apiHandler = resetPasswordApiHandler;
            this.#toastr = toastrService;

            ResetPasswordHandler.instance = this;
        }

        return ResetPasswordHandler.instance;
    }

    async reset() {
        const data = FormHelperService.formToJson($(this.#mapper.form));

        loader.show();

        try {
            await this.#apiHandler.send(data);

            AppHelperService.redirect('reload');

            return ;
        } catch (e) {
            let message = e.message;

            if (e.responseJSON !== undefined && e.responseJSON.message !== undefined) {
                message = e.responseJSON.message;
            }

            this.#toastr.error(message);
        }

        loader.hide();
    }

    async setNewPassword() {
        const data = FormHelperService.formToJson($(this.#pageMapper.form));

        loader.show();

        try {
            await this.#apiHandler.sendNewPassword(data);

            AppHelperService.redirect(Routing.generate(`site.home_page`));

        } catch (e) {
            let message = e.message;

            if (e.responseJSON !== undefined && e.responseJSON.message !== undefined) {
                message = e.responseJSON.message;
            }

            this.#toastr.error(message);
        }

        loader.hide();
    }
}

const resetPasswordHandler = new ResetPasswordHandler();

Object.freeze(resetPasswordHandler);

export default resetPasswordHandler;
