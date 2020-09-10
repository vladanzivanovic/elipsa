import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import AppHelperService from "../../../js/Helper/AppHelperService";
import FormHelperService from "../../../js/Helper/FormHelperService";
import loader from "../Dom/LoaderDom";
import {sha512} from "js-sha512";

require('jquery.redirect');

class CheckoutHandler {
    constructor() {
        this.mapper = checkoutPageMapper;
    }

    save() {
        const data = this.mapper.form.serializeArray();

        if (! this.mapper.form.valid()) {
            return false;
        }

        loader.show();

        $.ajax({
            type: 'PUT',
            url: Routing.generate(`site_api.complete_order.${LOCALE}`),
            data: FormHelperService.sanitize(data),
            dataType: 'json',
            success: response => {
                if ($(`${this.mapper.paymentType}:checked`).val() == PAYMENT_TYPE_ON_DELIVERING) {
                    AppHelperService.redirect(Routing.generate(`site.checkout_completed_successful.${LOCALE}`));

                    return;
                }

                this.redirectToIntesaPayment(response);
            },
            error: error => {
                loader.hide();
            }
        })
    }

    redirectToIntesaPayment(response) {

        $.redirect(
            INTESA_GATEWAY,
            response
        )
    }
}

export default CheckoutHandler;