import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import userService from "../Service/UserService";
import checkoutHandler from "../Handler/CheckoutHandler";

class CheckoutPageEvents {
    #pageMapper;
    #userService;
    #handler;

    constructor() {
        if(!CheckoutPageEvents.instance) {
            this.#pageMapper = checkoutPageMapper;
            this.#userService = userService;
            this.#handler = checkoutHandler;

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
    }
}

const checkoutPageEvents = new CheckoutPageEvents();

Object.freeze(checkoutPageEvents);

export default checkoutPageEvents;
