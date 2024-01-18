class LoginMapper {
    constructor() {
        if (!LoginMapper.instance) {
            this.form = '#login-form';
            this.submitBtn = '#login-btn';
            this.loginShowBtn = '#login_register_show_btn';
            this.loginShowWrapper = '#login_register_show';

            LoginMapper.instance = this;
        }

        return LoginMapper.instance;
    }

}

const loginMapper = new LoginMapper();

Object.freeze(loginMapper);

export default loginMapper;
