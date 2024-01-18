import registrationMapper from "../../Mapper/RegistrationMapper";
import loader from "../../Dom/LoaderDom";
import FormHelperService from "../../../../js/Helper/FormHelperService";
import registrationApiHandler from "./MyAccountApiHandler";
import toastrService from "../../../../js/Services/ToastrService";
import AppHelperService from "../../../../js/Helper/AppHelperService";
import myAccountPageMapper from "../../Mapper/MyAccountPageMapper";
import myAccountApiHandler from "./MyAccountApiHandler";

require('inputmask/dist/jquery.inputmask.bundle')

class MyAccountHandler {
    #mapper;
    #apiHandler;
    #toastr;

    constructor() {
        if (!MyAccountHandler.instance) {
            this.#mapper = myAccountPageMapper;
            this.#apiHandler = myAccountApiHandler;
            this.#toastr = toastrService;

            MyAccountHandler.instance = this;
        }

        return MyAccountHandler.instance;
    }

    async update() {
        const data = FormHelperService.formToJson($(this.#mapper.personalForm));

        if (! $(this.#mapper.personalForm).valid()) {
            return false;
        }

        loader.show();

        try {
            data.address.mobile_phone = $(this.#mapper.personalFormFields.mobilePhone).inputmask('unmaskedvalue');
            data.address.zip_code = $(this.#mapper.personalFormFields.zipCode).inputmask('unmaskedvalue')

            await this.#apiHandler.send(data);

            AppHelperService.redirect('reload');

            return ;
        } catch (e) {
            let message = e.message;

            if (e.responseJSON !== undefined && e.responseJSON.error !== undefined) {
                message = e.responseJSON.error.message;
            }

            this.#toastr.error(message);
        }

        loader.hide();
    }
}

const myAccountHandler = new MyAccountHandler();

Object.freeze(myAccountHandler);

export default myAccountHandler;
