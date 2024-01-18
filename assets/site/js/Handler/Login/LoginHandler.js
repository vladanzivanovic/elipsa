import loader from "../../Dom/LoaderDom";
import FormHelperService from "../../../../js/Helper/FormHelperService";
import toastrService from "../../../../js/Services/ToastrService";
import AppHelperService from "../../../../js/Helper/AppHelperService";
import loginMapper from "../../Mapper/Embedded/LoginMapper";
import loginApiHandler from "./LoginApiHandler";

class LoginHandler {
    #mapper;
    #apiHandler;
    #toastr;

    constructor() {
        if (!LoginHandler.instance) {
            this.#mapper = loginMapper;
            this.#apiHandler = loginApiHandler;
            this.#toastr = toastrService;

            LoginHandler.instance = this;
        }

        return LoginHandler.instance;
    }

    async login() {
        const data = FormHelperService.formToJson($(this.#mapper.form));

        if (data._remember_me) {
            data._remember_me = true;
        }

        loader.show();

        try {
            await this.#apiHandler.send(data);

            AppHelperService.redirect(Routing.generate('site.home_page'));

            return ;
        } catch (e) {
            let message = e.message;

            console.error(e);

            if (e.responseJSON !== undefined && e.responseJSON.message !== undefined) {
                message = e.responseJSON.message;
            }

            this.#toastr.error(message);
        }

        loader.hide();
    }
}

const loginHandler = new LoginHandler();

Object.freeze(loginHandler);

export default loginHandler;
