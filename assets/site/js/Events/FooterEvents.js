import footerMapper from "../Mapper/FooterMapper";

class FooterEvents {
    #mapper;

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
    }
}

export default FooterEvents;
