import headerMapper from "../Mapper/HeaderMapper";
import UserHandler from "../Handler/UserHandler";
import registrationValidator from "../Validators/RegistrationValidator";
import resetPasswordValidator from "../Validators/ResetPasswordValidator";
import orderApiProvider from "../Provider/OrderApiProvider";
import cartDropDownDom from "../Dom/CartDropDownDom";

class HeaderEvents {
    #mapper;
    #userHandler;
    #registrationValidator;
    #resetPasswordValidator;

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

        $(document).on('click touchend', this.#mapper.login.loginSubmitBtn, e => {
            this.#userHandler.doLogin(this.#mapper);
        });

        $(this.#mapper.reset.resetPasswordBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            $('.lrc-login').fadeOut();
            $('.lrc-register').fadeOut();
            $('#reset_password').fadeOut();

            $(this.#mapper.reset.resetPwdFormWrapper).fadeIn();
            $(this.#mapper.login.loginShowWrapper).fadeIn();
        });

        $(this.#mapper.login.loginShowBtn).on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            $('.lrc-login').fadeIn();
            $('.lrc-register').fadeIn();
            $('#reset_password').fadeIn();

            $(this.#mapper.reset.resetPwdFormWrapper).fadeOut();
            $(this.#mapper.login.loginShowWrapper).fadeOut();
        });

        $(document).on('click touchend', this.#mapper.reset.resetPasswordSubmitBtn, e => {

            $(this.#mapper.reset.form).valid();

            this.#userHandler.doResetPassword(this.#mapper);
        });

        $(document).on('click touchend', this.#mapper.search.opener, e => {
            e.preventDefault();
            e.stopPropagation();

            if (!$(this.#mapper.search.area).hasClass('show')) {
                $(this.#mapper.search.area).fadeIn(500);
                $(this.#mapper.search.area).addClass('show');
            }
        })

        $(document).on('click touchend', this.#mapper.search.close, e => {
            e.preventDefault();
            e.stopPropagation();

            if ($(this.#mapper.search.area).hasClass('show')) {
                $(this.#mapper.search.area).fadeOut(500);
                $(this.#mapper.search.area).removeClass('show');
            }
        })

        $(document).on('submit', this.#mapper.search.form, e => {
            e.preventDefault();
            e.stopPropagation();

            const params = {};

            params[Translator.trans('search', null, 'messages', LOCALE)] = $(this.#mapper.search.input).val();

            location.href = Routing.generate(
                `site.shop_page.${LOCALE}`, params);
        })
    }

    #registerValidators()
    {
        this.#registrationValidator = registrationValidator;
        this.#resetPasswordValidator = resetPasswordValidator;

        this.#registrationValidator.validate(this.#mapper.registrationForm);
        this.#resetPasswordValidator.validate(this.#mapper.reset.form);

    }
}

export default HeaderEvents;
