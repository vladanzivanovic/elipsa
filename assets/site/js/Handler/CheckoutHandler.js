import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import AppHelperService from "../../../js/Helper/AppHelperService";
import FormHelperService from "../../../js/Helper/FormHelperService";
import loader from "../Dom/LoaderDom";
import {sha512} from "js-sha512";
import orderApiHandler from "./Order/OrderApiHandler";

require('jquery.redirect');

class CheckoutHandler {
    #orderApiHandler;

    constructor() {
        this.mapper = checkoutPageMapper;
        this.#orderApiHandler = orderApiHandler;
    }

    save() {
        grecaptcha.ready(() => {
            if (!localStorage.getItem('order')) {
                return;
            }

            grecaptcha.execute(GOOGLE_RECAPTCHA_KEY_SITE, {action: 'complete_order'}).then((token) => {
                const data = this.mapper.form.serializeArray

                data.push({
                    name: 'recaptcha_response',
                    value: token
                });

                if (! this.mapper.form.valid()) {
                    return false;
                }

                loader.show();

                this.#orderApiHandler.completeOrder()
                    .then(order => {
                        if ($(`${this.mapper.paymentType}:checked`).val() == PAYMENT_TYPE_ON_DELIVERING) {
                            AppHelperService.redirect(Routing.generate(`site.checkout_completed_successful.${LOCALE}`));

                            return;
                        }

                        this.redirectToIntesaPayment(response);
                    });

                // $.ajax({
                //     type: 'PUT',
                //     url: Routing.generate(`site_api.complete_order.${LOCALE}`),
                //     data: FormHelperService.sanitize(data),
                //     dataType: 'json',
                //     success: response => {
                //         if ($(`${this.mapper.paymentType}:checked`).val() == PAYMENT_TYPE_ON_DELIVERING) {
                //             AppHelperService.redirect(Routing.generate(`site.checkout_completed_successful.${LOCALE}`));
                //
                //             return;
                //         }
                //
                //         this.redirectToIntesaPayment(response);
                //     },
                //     error: error => {
                //         loader.hide();
                //     }
                // })
            });
        });
    }

    redirectToIntesaPayment(response) {

        $.redirect(
            INTESA_GATEWAY,
            response
        )
    }
}

export default CheckoutHandler;
