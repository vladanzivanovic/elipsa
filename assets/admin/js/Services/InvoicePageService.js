import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";

class InvoicePageService {
    constructor() {
        if (!InvoicePageService.instance) {

            this.notification = NotificationService();

            InvoicePageService.instance = this;
        }

        return InvoicePageService.instance;
    }

    doRequest(url) {
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'GET',
            url,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect('reload');
            },
            error: error => {
                const errors = error.responseJSON;

                if (errors.hasOwnProperty('message')) {
                    this.notification.show('error', errors.message, true);

                    return;
                }

                this.notification.show('error', Translator.trans('generic_error', null, 'messages'. LOCALE), true);
            }
        });
    }
}

const invoicePageService = new InvoicePageService();

Object.freeze(invoicePageService);

export default invoicePageService;
