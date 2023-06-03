class HeaderMapper {
    constructor() {
        if (!HeaderMapper.instance) {
            this.loginRegistrationBtn = '#login_register_show_btn';
            this.loginRegistrationFormWraper = '#login_register_show';
            this.loginSubmitBtn = '#login-btn';
            this.loginForm = '#login-form';
            this.registrationForm = '#registration-form';
            this.registrationSubmitBtn = '#registration-btn';
            this.resetPasswordBtn = '#reset_password_btn';
            this.resetPwdFormWrapper = '#reset_password_form_wrapper';
            this.resetPasswordSubmitBtn = '#reset_btn';
            this.resetForm = '#reset_form';

            HeaderMapper.instance = this;
        }

        return HeaderMapper.instance;;
    }

}

const headerMapper = new HeaderMapper();

Object.freeze(headerMapper);

export default headerMapper;
