import headerMapper from "../Mapper/HeaderMapper";
import registrationValidator from "../Validators/RegistrationValidator";
import resetPasswordValidator from "../Validators/ResetPasswordValidator";
import loginHandler from "../Handler/Login/LoginHandler";
import resetPasswordHandler from "../Handler/ResetPassword/ResetPasswordHandler";

class HeaderEvents {
    #mapper;
    #userHandler;
    #registrationValidator;
    #resetPasswordValidator;

    constructor() {
        this.#mapper = headerMapper;

        this.#registerValidators();

        this.#registerEvents();
    }

    #registerEvents() {
        $(document).on('click', this.#mapper.login.submitBtn, async e => {
            await loginHandler.login();
        });

        $(this.#mapper.reset.resetPasswordBtn).on('click', e => {
            e.preventDefault();
            e.stopPropagation();

            $('.lrc-login').fadeOut();
            $('.lrc-register').fadeOut();
            $('#reset_password').fadeOut();

            $(this.#mapper.reset.resetPwdFormWrapper).fadeIn();
            $(this.#mapper.login.loginShowWrapper).fadeIn();
        });

        $(this.#mapper.login.loginShowBtn).on('click', e => {
            e.preventDefault();
            e.stopPropagation();

            $('.lrc-login').fadeIn();
            $('.lrc-register').fadeIn();
            $('#reset_password').fadeIn();

            $(this.#mapper.reset.resetPwdFormWrapper).fadeOut();
            $(this.#mapper.login.loginShowWrapper).fadeOut();
        });

        $(document).on('click', this.#mapper.reset.resetPasswordSubmitBtn, async e => {

            $(this.#mapper.reset.form).valid();

            await resetPasswordHandler.reset();
        });

        $(document).on('click', this.#mapper.search.opener, e => {
            e.preventDefault();
            e.stopPropagation();

            if (!$(this.#mapper.search.area).hasClass('show')) {
                $(this.#mapper.search.area).fadeIn(500);
                $(this.#mapper.search.area).addClass('show');
            }
        })

        $(document).on('click', this.#mapper.search.close, e => {
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

        $(this.#mapper.dropDown.close).on('click', e => {
            const parent = $(e.currentTarget).parents('.drop-down-area');

            parent.removeClass('drop-down-area-active');
        });

        $(document).on('click', e => {
            $.each($('.drop-down-area-active'), (index, element) => {
                if (0 === $(e.target).parents('.mobile-user-item').length) {
                    $(element).removeClass('drop-down-area-active');
                }
            });
        });

        $(this.#mapper.dropDown.button).on('click', e => {
            const parent = $(e.currentTarget).parent('.mobile-user-item');

            if (parent.hasClass('go-to-link')) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            $.each($('.drop-down-area-active'), (index, element) => {
                if (0 === $(e.target).parents('.mobile-user-item').closest($(element)).length) {
                    $(element).removeClass('drop-down-area-active');
                }
            });

            parent.find(this.#mapper.dropDown.area).addClass('drop-down-area-active');
        });
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
