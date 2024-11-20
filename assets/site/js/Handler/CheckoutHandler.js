import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import AppHelperService from "../../../js/Helper/AppHelperService";
import FormHelperService from "../../../js/Helper/FormHelperService";
import loader from "../Dom/LoaderDom";
import {sha512} from "js-sha512";
import orderApiHandler from "./Order/OrderApiHandler";
import orderApiProvider from "../Provider/OrderApiProvider";
import orderStorageManipulator from "../Manipulator/OrderStorageManipulator";
import toastrService from "../../../js/Services/ToastrService";

require('jquery.redirect');

class CheckoutHandler {
    #mapper;
    #orderApiHandler;
    #orderApiProvider;
    #orderStorageManipulator;
    #toastr;

    constructor() {
        if(!CheckoutHandler.instance) {
            this.#mapper = checkoutPageMapper;
            this.#orderApiHandler = orderApiHandler;
            this.#orderApiProvider = orderApiProvider;
            this.#orderStorageManipulator = orderStorageManipulator;
            this.#toastr = toastrService;

            CheckoutHandler.instance = this;
        }

        return CheckoutHandler.instance;
    }

    save()
    {
        grecaptcha.ready(() => {
            const orderToken = this.#orderStorageManipulator.getOrderToken();

            if (!orderToken) {
                return;
            }

            grecaptcha.execute(GOOGLE_RECAPTCHA_KEY_SITE, {action: 'complete_order'}).then((token) => {
                const data = FormHelperService.formToJson($(this.#mapper.form));
                const user = data.user;


                data.recaptcha_response = token;
                data.shipping_address = data.billing_address;
                data.user = {...user, ...data.billing_address};

                if (! $(this.#mapper.form).valid()) {
                    return false;
                }

                loader.show();

                this.#orderApiHandler.completeOrder(data)
                    .then(async order => {
                        const paymentData = await this.#orderApiProvider.getPayment(this.#orderStorageManipulator.getOrderToken('order'));

                        if (0 < Object.keys(paymentData).length) {
                            switch (COUNTRY_DEFAULT_LOCALE) {
                                case 'ba':
                                    this.#redirectToBankArtPayment(paymentData);
                                    break;
                                case 'rs':
                                    this.#redirectToIntesaPayment(paymentData);
                                    break;
                                default:
                                    throw Error('not supported payment for given country');
                            }

                            return;
                        }

                        const redirectUrl = Routing.generate(
                            `site.checkout_completed_successful.${LOCALE}`,
                            {
                                'token': this.#orderStorageManipulator.getOrderToken('order')
                            }
                        );

                        this.#orderStorageManipulator.removeOrder();

                        AppHelperService.redirect(redirectUrl);
                    })
                    .catch(e => {
                        let message = e.message;

                        if (e.responseJSON !== undefined && e.responseJSON.error !== undefined) {
                            message = e.responseJSON.error.message;
                        }

                        this.#toastr.error(message);

                        loader.hide();
                    });
            });
        });
    }

    #redirectToIntesaPayment(response) {

        $.redirect(
            INTESA_GATEWAY,
            response
        )
    }

    #redirectToBankArtPayment(response)
    {
        AppHelperService.redirect(response.redirect_url);
    }
}

const checkoutHandler = new CheckoutHandler();

Object.freeze(checkoutHandler);

export default checkoutHandler;
