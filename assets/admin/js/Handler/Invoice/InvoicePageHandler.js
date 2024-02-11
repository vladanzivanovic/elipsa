import invoiceApiHandler from "./InvoiceApiHandler";
import toastrService from "../../../../js/Services/ToastrService";
import AppHelperService from "../../../../js/Helper/AppHelperService";
import orderSinglePageMapper from "../../Mapper/OrderSinglePageMapper";
import FormHelperService from "../../../../js/Helper/FormHelperService";

class InvoicePageHandler {
    #apiHandler;
    #notification;
    #mapper;

    constructor() {
        if(!InvoicePageHandler.instance) {
            this.#apiHandler = invoiceApiHandler;
            this.#notification = toastrService;
            this.#mapper = orderSinglePageMapper;

            InvoicePageHandler.instance = this;
        }

        return InvoicePageHandler.instance;
    }

    async setState()
    {
        try {
            const data = FormHelperService.formToJson($(this.#mapper.state.form));

            this.#notification.showLoadingMessage();

            await this.#apiHandler.saveState(data);

            AppHelperService.redirect('reload');
        } catch (error) {
            this.#notification.error(Translator.trans('generic_error', null, 'messages', LOCALE));
        }
    }
}

const invoicePageHandler = new InvoicePageHandler();

Object.freeze(invoicePageHandler);

export default invoicePageHandler;
