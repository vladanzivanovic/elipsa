// import RegistrationService from "../Services/RegistrationService";
// import RegistrationValidator from "../Validation/RegistrationValidator";
// import RegistrationMapper from "../Mapper/RegistrationMapper";
// import Loginservice from "../Services/LoginService";
// import ResetPasswordHandler from "../Handler/ResetPasswordHandler";
// import NotificationService from "../Services/NotificationService";

import NotificationService from "./NotificationService";

const Private = Symbol('private');

class CoreController {
    constructor() {
        this[Private]().registerEvents();
        // RegistrationValidator().validation();
    }

    showFlashMsg() {
        if (window.Messages) {
            let notify = NotificationService();
            window.Messages.forEach(message => {
                notify.show('success', message, true);
            });
        }
    }

    siteMobileMenu() {
        $('nav#mobile_menu_active').meanmenu({
            meanScreenWidth: "991",
            meanMenuContainer: '.mobile-menu-area .container',
        });
    }

    [Private]() {
        let Private = {};

        Private.registerEvents = () => {
            // $(document).on('click', '#signin-button', function(){
            //     Loginservice().doLogin();
            // });

            // $(document).on('click touchend', '#signup-btn', () => {
            //     let mapper = new RegistrationMapper();
            //
            //     mapper.form.validate().destroy();
            //     RegistrationValidator().validation();
            //
            //     RegistrationService().doRegistration()
            // });
            //
            // $(document).on('click touchend', '#reset-password-button', e => {
            //     ResetPasswordHandler().doReset(e);
            // });
        };

        return Private;
    }
}

export default CoreController;