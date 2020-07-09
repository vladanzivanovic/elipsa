class ResetPasswordPageMapper {
    constructor() {
        if (!ResetPasswordPageMapper.instance) {
            this.form = '#reset_password_form';
            this.token = '#token';
            this.password = '#password';
            this.rePassword = '#repeat_password';
            this.submitBtn = '#reset_password_submit';

            ResetPasswordPageMapper.instance = this;
        }

        return  ResetPasswordPageMapper.instance;
    }
}

const resetPasswordPageMapper = new ResetPasswordPageMapper();

Object.freeze(resetPasswordPageMapper);

export default resetPasswordPageMapper;