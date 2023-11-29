import footerMapper from "../Mapper/FooterMapper";

class FooterEvents {
    #mapper;
    #newsLetterMapper;

    constructor() {
        this.#mapper = footerMapper;

        this.#registerEvents();
    }

    #registerEvents() {
        $(this.#mapper.loginBtn).on('click', e => {
            e.preventDefault();
            e.stopPropagation();

            $('#scrollUp').click();
            $('#login_register').click();
        })

        $(this.#mapper.registrationBtn).on('click', e => {
            e.preventDefault();
            e.stopPropagation();

            $('#scrollUp').click();
            $('#login_register').click();
        })

        $(document).euCookieLawPopup().init({
            cookiePolicyUrl : Routing.generate(`site.cookie_policy.${LOCALE}`),
            popupTitle : Translator.trans('eu.cookies.accept.title', null, 'messages', LOCALE),
            popupText : Translator.trans('eu.cookies.accept.text', null, 'messages', LOCALE),
            buttonContinueTitle : Translator.trans('eu.cookies.accept.btn', null, 'messages', LOCALE),
            buttonLearnmoreTitle : Translator.trans('eu.cookies.learn_more.btn', null, 'messages', LOCALE),
            buttonLearnmoreOpenInNewWindow : true,
            agreementExpiresInDays : 30,
            autoAcceptCookiePolicy : false,
            htmlMarkup : null
        });
    }
}

export default FooterEvents;
