import registrationMapper from "../../Mapper/RegistrationMapper";
import loader from "../../Dom/LoaderDom";
import FormHelperService from "../../../../js/Helper/FormHelperService";
import registrationApiHandler from "./RegistrationApiHandler";
import toastrService from "../../../../js/Services/ToastrService";
import AppHelperService from "../../../../js/Helper/AppHelperService";

require('inputmask/dist/jquery.inputmask.bundle')

class RegistrationHandler {
    #mapper;
    #apiHandler;
    #toastr;

    constructor() {
        if (!RegistrationHandler.instance) {
            this.#mapper = registrationMapper;
            this.#apiHandler = registrationApiHandler;
            this.#toastr = toastrService;

            RegistrationHandler.instance = this;
        }

        return RegistrationHandler.instance;
    }

    async save() {
        const data = FormHelperService.formToJson($(this.#mapper.form));

        if (! $(this.#mapper.form).valid()) {
            return false;
        }

        loader.show();

        try {
            data.address.mobile_phone = $(this.#mapper.fields.mobilePhone).inputmask('unmaskedvalue');
            data.address.zip_code = $(this.#mapper.fields.zipCode).inputmask('unmaskedvalue')

            await this.#apiHandler.register(data);

            AppHelperService.redirect(Routing.generate('site.registration_confirmation'));

            return ;
        } catch (e) {
            let message = e.message;

            if (e.responseJSON !== undefined && e.responseJSON.error !== undefined) {
                message = e.responseJSON.error.message;
            }

            this.#toastr.error(message);
        }

        loader.hide();

        // $.ajax({
        //     type,
        //     url: urlRoute,
        //     data,
        //     dataType: 'json',
        //     success: (response) => {
        //         AppHelperService.redirect('reload');
        //         this.notification.success(Translator.trans(`registration.success.message`, null, 'messages', LOCALE));
        //         loader.hide();
        //     },
        //     error: (error) => {
        //         this.notification.error(Translator.trans(`registration.error.user_exists`, null, 'messages', LOCALE));
        //         loader.hide();
        //     }
        // })
    }
}

const registrationHandler = new RegistrationHandler();

Object.freeze(registrationHandler);

export default registrationHandler;
