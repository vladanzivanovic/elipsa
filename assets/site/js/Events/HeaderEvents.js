import headerMapper from "../Mapper/HeaderMapper";
import UserHandler from "../Handler/UserHandler";
import registrationValidator from "../Validators/RegistrationValidator";
import resetPasswordValidator from "../Validators/ResetPasswordValidator";

class HeaderEvents {
    #mapper;
    #userHandler;
    #registrationValidator;
    #resetPasswordValidator

    constructor() {
        this.#mapper = headerMapper;
        this.#userHandler = new UserHandler();

        this.#registerValidators();

        this.#registerEvents();
    }

    #registerEvents() {
        $(document).on('click touchend', this.#mapper.registrationSubmitBtn, e => {
            $(this.#mapper.registrationForm).valid();

            this.#userHandler.doRegistration(this.#mapper.registrationForm);
        });

        $(document).on('click touchend', this.#mapper.loginSubmitBtn, e => {
            this.#userHandler.doLogin(this.#mapper);
        });

        $(this.#mapper.resetPasswordBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            $('.lrc-login').fadeOut();
            $('.lrc-register').fadeOut();
            $('#reset_password').fadeOut();

            $(this.#mapper.resetPwdFormWrapper).fadeIn();
            $(this.#mapper.loginRegistrationFormWraper).fadeIn();
        });

        $(this.#mapper.loginRegistrationBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            $('.lrc-login').fadeIn();
            $('.lrc-register').fadeIn();
            $('#reset_password').fadeIn();

            $(this.#mapper.resetPwdFormWrapper).fadeOut();
            $(this.#mapper.loginRegistrationFormWraper).fadeOut();
        });

        $(document).on('click touchend', this.#mapper.resetPasswordSubmitBtn, e => {

            $(this.#mapper.resetForm).valid();

            this.#userHandler.doResetPassword(this.#mapper);
        });
    }

    #registerValidators()
    {
        this.#registrationValidator = registrationValidator;
        this.#resetPasswordValidator = resetPasswordValidator;

        this.#registrationValidator.validate(this.#mapper.registrationForm);
        this.#resetPasswordValidator.validate(this.#mapper.resetForm);

    }
}

export default HeaderEvents;
