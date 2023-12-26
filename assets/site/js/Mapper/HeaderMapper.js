class HeaderMapper {
    constructor() {
        if (!HeaderMapper.instance) {
            this.registrationForm = '#registration-form';
            this.registrationSubmitBtn = '#registration-btn';
            this.localeDropDown = '#locale-dropdown';

            this.login = {
                loginForm: '#login-form',
                loginSubmitBtn: '#login-btn',
                loginShowBtn: '#login_register_show_btn',
                loginShowWrapper: '#login_register_show',
            };

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

            HeaderMapper.instance = this;
        }

        return HeaderMapper.instance;;
    }

}

const headerMapper = new HeaderMapper();

Object.freeze(headerMapper);

export default headerMapper;
