import loginMapper from "./Embedded/LoginMapper";
import resetPasswordMapper from "./Embedded/ResetPasswordMapper";

class HeaderMapper {
    constructor() {
        if (!HeaderMapper.instance) {
            this.registrationForm = '#registration-form';

            this.login = loginMapper;
            this.reset = resetPasswordMapper;

            this.reset = {
                form: '#reset_form',
                resetPwdFormWrapper: '#reset_password_form_wrapper',
                resetPasswordBtn: '#reset_password_btn',
                resetPasswordSubmitBtn: '#reset_btn',
            };

            this.search = {
                form: '.search-form',
                opener: '.search-opener',
                close: '.search-close',
                area: '.search-area',
                input: '#search-input',
            };

            this.dropDown = {
                close: '.close-drop-down',
                area: '.drop-down-area',
                button: '.mobile-user-item > a',
                locale: '#locale-dropdown',
                country: '#country-dropdown',
            };

            HeaderMapper.instance = this;
        }

        return HeaderMapper.instance;
    }

}

const headerMapper = new HeaderMapper();

Object.freeze(headerMapper);

export default headerMapper;
