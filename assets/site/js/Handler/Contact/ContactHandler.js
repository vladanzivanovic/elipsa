import loader from "../../Dom/LoaderDom";
import FormHelperService from "../../../../js/Helper/FormHelperService";
import toastrService from "../../../../js/Services/ToastrService";
import askUsFormMapper from "../../Mapper/AskUsFormMapper";
import askUsApiHandler from "./AskUsApiHandler";

class ContactHandler {
    #askUsFormMapper;
    #askUsApiHandler;
    #toastr;

    constructor() {
        if (!ContactHandler.instance) {
            this.#askUsFormMapper = askUsFormMapper;
            this.#askUsApiHandler = askUsApiHandler;
            this.#toastr = toastrService;

            ContactHandler.instance = this;
        }

        return ContactHandler.instance;
    }

    async askUs() {
        const data = FormHelperService.formToJson($(this.#askUsFormMapper.form));

        if (data.telephone) {
            data.telephone = $(this.#askUsFormMapper.fields.telephone).inputmask('unmaskedvalue');
            data.telephone = data.telephone.length === 1 ? null : data.telephone;
        }

        if (! $(this.#askUsFormMapper.form).valid()) {
            return false;
        }

        loader.show();

        try {
            const response = await this.#askUsApiHandler.send(data);

            $(this.#askUsFormMapper.form)[0].reset();

            this.#toastr.success(response.message);

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

const contactHandler = new ContactHandler();

Object.freeze(contactHandler);

export default contactHandler;
