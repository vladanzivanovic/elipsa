import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import CheckoutHandler from "../Handler/CheckoutHandler";
import checkoutValidation from "../Validators/CheckoutValidation";
import UserService from "../Service/UserService";
import RecaptchaLoader from "../../../js/Services/RecaptchaLoader";
import checkoutPageEvents from "../Events/CheckoutPageEvents";
import checkoutPageManipulator from "../Manipulator/CheckoutPageManipulator";

class CheckoutPageController {
    #pageEvents;
    #pageManipulator;

    constructor() {
        this.mapper = checkoutPageMapper;
        this.handler = new CheckoutHandler();
        this.validator = checkoutValidation;
        // this.userService = new UserService();
        this.#pageEvents = checkoutPageEvents;
        this.#pageManipulator = checkoutPageManipulator;

        RecaptchaLoader.loadRecaptcha();

        this.#pageManipulator.setPage();

        this.validator.validate();

        this.#pageEvents.registerEvents();
    }

    registerEvents() {
        // this.mapper.form.on('submit', e => {
        //     e.preventDefault();
        //     e.stopPropagation();
        //
        //     this.handler.save();
        // });
        // this.mapper.btn.on('click touchend', e => {
        //     this.handler.save();
        // });

        // $('.open-login').on('click touchend', e => {
        //     e.preventDefault();
        //     e.stopPropagation();
        //
        //     $('#login_register').click();
        // })

        // this.mapper.accountCreateChk.on('click touchend', e => {
        //     this.mapper.accountCreateError.fadeOut();
        //     const email = this.mapper.email.val();
        //
        //     if (this.mapper.accountCreateChk.is(':checked') && email.length > 0) {
        //
        //         this.userService.isUserExistsByEmail(email)
        //             .then(response => {
        //                 this.mapper.password.removeAttr('disabled');
        //             })
        //             .fail(error => {
        //                 let message = 'checkout.account_exists';
        //
        //                 if (error.status === 403) {
        //                     message = 'checkout.account_deactivated';
        //                 }
        //
        //                 this.mapper.accountCreateError.fadeIn();
        //                 this.mapper.accountCreateError.text(Translator.trans(message, {'email': email}, 'messages', LOCALE));
        //                 this.mapper.accountCreateChk.prop('checked', false);
        //             });
        //
        //
        //         return;
        //     }
        //
        //     this.mapper.password.val('');
        //     this.mapper.password.attr('disabled', 'disabled');
        //     this.mapper.accountCreateChk.prop('checked', false);
        // });

        this.mapper.email.on('keyup', e => {
            this.mapper.accountCreateError.fadeOut();
        })
    }
}

export default CheckoutPageController;
