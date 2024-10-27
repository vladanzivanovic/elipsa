import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import userService from "../Service/UserService";
import checkoutHandler from "../Handler/CheckoutHandler";
import checkoutPageManipulator from "../Manipulator/CheckoutPageManipulator";

class CheckoutPageEvents {
    #pageMapper;
    #userService;
    #handler;
    #pageManipulator;

    constructor() {
        if(!CheckoutPageEvents.instance) {
            this.#pageMapper = checkoutPageMapper;
            this.#userService = userService;
            this.#handler = checkoutHandler;
            this.#pageManipulator = checkoutPageManipulator;

            CheckoutPageEvents.instance = this;
        }

        return CheckoutPageEvents.instance;
    }

    registerEvents()
    {
        $('.open-login').on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            $('#login_register').click();
        })

        $(this.#pageMapper.accountCreateChk).on('click touchend', async e => {
            const email = $(this.#pageMapper.email).val();

            $(this.#pageMapper.accountCreateError).fadeOut();

            if ($(this.#pageMapper.accountCreateChk).is(':checked') && email.length > 0) {

                try {
                    await this.#userService.isUserExistsByEmail(email);

                    $(this.#pageMapper.password).removeAttr('disabled');
                } catch (error) {
                    let message = 'checkout.account_exists';

                    if (error.status === 403) {
                        message = 'checkout.account_deactivated';
                    }

                    $(this.#pageMapper.accountCreateError).fadeIn();
                    $(this.#pageMapper.accountCreateError).text(Translator.trans(message, {'email': email}, 'messages', LOCALE));
                    $(this.#pageMapper.accountCreateChk).prop('checked', false);
                }

                return;
            }

            $(this.#pageMapper.password).val('');
            $(this.#pageMapper.password).attr('disabled', 'disabled');
            $(this.#pageMapper.accountCreateChk).prop('checked', false);
        });

        $(this.#pageMapper.form).on('submit', e => {
            e.preventDefault();
            e.stopPropagation();

            this.#handler.save();
        });

        $(this.#pageMapper.email).on('keyup', e => {
            $(this.#pageMapper.accountCreateError).fadeOut();
        })

        $(`${this.#pageMapper.shipping.locations} select`).select2();

        $(`${this.#pageMapper.shipping.method} input`).on('change', e => {
            const selectedMethod = $(e.currentTarget).val();

            $(this.#pageMapper.shipping.locations).addClass('hide');

            if (selectedMethod === IN_STORE_SHIPPING_METHOD) {
                $(this.#pageMapper.shipping.locations).removeClass('hide');
            }
        });

        $(`${this.#pageMapper.paymentType}`).on('change', e => {
            const selectedMethod = $(e.currentTarget).val();

            $('#shipping_in_store').attr('disabled', 'disabled');
            $('#shipping_on_delivery').click();

            if (selectedMethod === ON_DELIVERY_PAYMENT) {
                $('#shipping_in_store').removeAttr('disabled');
            }
        });

        $(document).on('order:fetch', (e, order) => {
            this.#pageManipulator.setPage(order);
        })
    }
}

const checkoutPageEvents = new CheckoutPageEvents();

Object.freeze(checkoutPageEvents);

export default checkoutPageEvents;
